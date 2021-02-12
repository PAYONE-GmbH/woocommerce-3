<?php

namespace Payone\Subscription;

use DateInterval;
use DateTimeImmutable;
use Payone\Admin\Option\Account;
use Payone\Gateway\Invoice;
use Payone\Gateway\SubscriptionAwareInterface;
use WC_Order;
use WC_Payment_Gateway;
use WC_Subscription;
use WCS_Retry_Manager;
use WCS_Retry_Rules;
use WCS_Retry_Store;
use function WC;

class SubscriptionHandler {
	/** @var SubscriptionHandler $self */
	private static $self;
	/** @var array<string,string> $options */
	private $options = [];

	/**
	 * @return SubscriptionHandler
	 */
	public static function getInstance() {
		if ( isset( self::$self ) ) {
			return self::$self;
		}

		self::$self          = new self();
		self::$self->options = get_option( Account::OPTION_NAME );

		return self::$self;
	}

	/**
	 * Is Woocommerce Subscription Plugin installed and active?
	 * @return bool
	 */
	public static function is_wcs_active() {
		return (bool) in_array(
			'woocommerce-subscriptions/woocommerce-subscriptions.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		);
	}

	/**
	 * @return void
	 */
	public function init() {
		add_action(
			'woocommerce_subscription_renewal_payment_failed',
			[ $this, 'process_woocommerce_subscription_renewal_payment_failed' ],
			10,
			2
		);
	}

	/**
	 * @param WC_Subscription $subscription
	 * @param WC_Order $last_order
	 *
	 * @return void
	 */
	public function process_woocommerce_subscription_renewal_payment_failed( $subscription, $last_order ) {
		if ( ! $subscription instanceof WC_Subscription || ! $last_order instanceof WC_Order ) {
			return;
		}

		//Do not handle anything else except our payment gateways that are aware of subscriptions.
		if ( ! $this->is_payone_gateway_is_available_and_subscritpion_aware( $last_order->get_payment_method() ) ) {
			return;
		}

		$payone_renewal_payment_fails = (int) $subscription->get_meta( '_payone_renewal_payment_fails' );
		$payone_renewal_payment_fails ++;

		$subscription->update_meta_data( '_payone_renewal_payment_fails', $payone_renewal_payment_fails );

		//If order already uses Invoice, just skip it.
		if ( $last_order->get_payment_method() === Invoice::GATEWAY_ID ) {
			return;
		}

		//Do we need to do anything at all (is the option selected)?
		if ( ! $this->is_payone_subscription_auto_failover_enabled() ) {
			return;
		}

		//Can we use PayOne Invoice payment method?
		if ( ! $this->is_payone_gateway_is_available_and_subscritpion_aware( Invoice::GATEWAY_ID ) ) {
			return;
		}

		//Are there any retries left, if retry manager is turned on? If yes, do nothing.
		//At the time of writing this, there are total of 5 (by default, without plugins) tries.
		//See \WCS_Retry_Rules::__construct and https://docs.woocommerce.com/document/subscriptions/develop/failed-payment-retry/ for more info.
		//Once there are no more retries (mandated by Subscriptions Plugin or by other plugins), we add our own business logic.
		if ( WCS_Retry_Manager::is_retry_enabled() ) {
			/** @var WCS_Retry_Store $retry_store */
			$retry_store = WCS_Retry_Manager::store();
			/** @var WCS_Retry_Rules $retry_rules */
			$retry_rules = WCS_Retry_Manager::rules();
			/** @var int $retry_count */
			$retry_count = $retry_store->get_retry_count_for_order( $last_order->get_id() );
			if ( (bool) $retry_rules->has_rule( $retry_count, $last_order->get_id() ) ) {
				return;
			}
		}

		//Leave the old order as failed, we do not care about it. But update subscription status back to active,
		//and update next_payment to run again in 10 minutes.
		$subscription->update_status( 'active' );
		$subscription->set_payment_method( new Invoice() );
		$subscription->save();
		$subscription->add_order_note( __( 'Subscription payment method is changed to Invoice.', 'payone-woocommerce-3' ) );

		$dateTime = ( new DateTimeImmutable() )->add( new DateInterval( 'PT10M' ) );
		$subscription->update_dates( array( 'next_payment' => $dateTime->format( 'Y-m-d H:i:s' ) ) );
	}

	/**
	 * @param string $gateway_id
	 *
	 * @return bool
	 */
	public function is_payone_gateway_is_available_and_subscritpion_aware( $gateway_id ) {
		foreach ( WC()->payment_gateways()->payment_gateways() as $payment_gateway_id => $payment_gateway ) {
			/**
			 * @var string $payment_gateway_id
			 * @var WC_Payment_Gateway $payment_gateway
			 */
			if (
				$gateway_id === $payment_gateway_id &&
				$payment_gateway instanceof SubscriptionAwareInterface &&
				trim( $payment_gateway->get_option( 'enabled' ) ) === 'yes'
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function is_payone_subscription_auto_failover_enabled() {
		if ( isset( $this->options['payone_subscription_auto_failover'] ) ) {
			return (bool) $this->options['payone_subscription_auto_failover'];
		}

		return false;
	}
}
