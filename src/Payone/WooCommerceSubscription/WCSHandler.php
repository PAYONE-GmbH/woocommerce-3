<?php

namespace Payone\WooCommerceSubscription;

use Payone\Gateway\Invoice;

trait WCSHandler {
    public static function is_wcs_active() {
        return (bool) in_array(
            'woocommerce-subscriptions/woocommerce-subscriptions.php',
            apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
            true
        );
    }

    public static function is_subscription( \WC_Order $order ) {
        return self::is_wcs_active() && wcs_order_contains_subscription( $order );
    }

    /**
     * @param \WC_Subscription $subscription
     * @param \WC_Order $last_order
     *
     * @return void
     */
    public static function process_woocommerce_subscription_renewal_payment_failed( $subscription, $last_order ) {
        if ( ! $subscription instanceof \WC_Subscription || ! $last_order instanceof \WC_Order ) {
            return;
        }

        // Do not handle anything else except our payment gateways that are aware of subscriptions.
        if ( ! self::is_payone_gateway_is_available_and_subscritpion_aware( $last_order->get_payment_method() ) ) {
            return;
        }

        $payone_renewal_payment_fails = (int) $subscription->get_meta( '_payone_renewal_payment_fails' );
        $payone_renewal_payment_fails++;

        $subscription->update_meta_data( '_payone_renewal_payment_fails', $payone_renewal_payment_fails );

        // If order already uses Invoice, just skip it.
        if ( $last_order->get_payment_method() === Invoice::GATEWAY_ID ) {
            return;
        }
        
        // Are there any retries left, if retry manager is turned on? If yes, do nothing.
        // At the time of writing this, there are total of 5 (by default, without plugins) tries.
        // See \WCS_Retry_Rules::__construct and https://docs.woocommerce.com/document/subscriptions/develop/failed-payment-retry/ for more info.
        // Once there are no more retries (mandated by Subscriptions Plugin or by other plugins), we add our own business logic.
        if ( \WCS_Retry_Manager::is_retry_enabled() ) {
            $retry_store = \WCS_Retry_Manager::store();
            /** @var \WCS_Retry_Rules $retry_rules */
            $retry_rules = \WCS_Retry_Manager::rules();
            $retry_count = $retry_store->get_retry_count_for_order( $last_order->get_id() );
            if ( (bool) $retry_rules->has_rule( $retry_count, $last_order->get_id() ) ) {
                return;
            }
        }

        $payment_gateway_invoice = new Invoice();
        if ( $payment_gateway_invoice->get_option( 'enabled' ) === 'yes'
             && $payment_gateway_invoice->supports( 'subscriptions' )
        ) {
            // Leave the old order as failed, we do not care about it. But update subscription status back to active,
            // and update next_payment to run again in 10 minutes.
            $subscription->update_status('active');
            $subscription->set_payment_method($payment_gateway_invoice);
            $subscription->save();
            $subscription->add_order_note(__('Subscription payment method is changed to Invoice.', 'payone-woocommerce-3'));

            $dateTime = (new \DateTimeImmutable())->add(new \DateInterval('PT10M'));
            $subscription->update_dates(['next_payment' => $dateTime->format('Y-m-d H:i:s')]);
        }
    }

    /**
     * @param string $gateway_id
     *
     * @return bool
     */
    public static function is_payone_gateway_is_available_and_subscritpion_aware( $gateway_id ) {
        foreach ( WC()->payment_gateways()->payment_gateways() as $payment_gateway_id => $payment_gateway ) {
            /**
             * @var string $payment_gateway_id
             * @var \WC_Payment_Gateway $payment_gateway
             */
            if ( $gateway_id === $payment_gateway_id
                && $payment_gateway->supports( 'subscriptions' )
                && trim( $payment_gateway->get_option( 'enabled' ) ) === 'yes'
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function is_payone_subscription_auto_failover_enabled() {
        $options = get_option( 'payone_account', [ 'payone_subscription_auto_failover' => false ] );

        if ( isset( $options['payone_subscription_auto_failover'] ) ) {
            return (bool) $options['payone_subscription_auto_failover'];
        }

        return false;
    }
}
