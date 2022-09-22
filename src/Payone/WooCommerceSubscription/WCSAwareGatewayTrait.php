<?php

namespace Payone\WooCommerceSubscription;

use Payone\Gateway\CreditCard;
use Payone\Gateway\Invoice;
use Payone\Gateway\PayPal;
use Payone\Gateway\SepaDirectDebit;

trait WCSAwareGatewayTrait {
	public function add_wcs_support() {
		$this->supports = array_merge( $this->supports, [
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
		] );

		add_action( 'woocommerce_scheduled_subscription_payment_' . (string) self::GATEWAY_ID,
			[ $this, 'wcs_process_scheduled_subscription_payment' ],
			10,
			2
		);
	}

	/**
	 * @param float $renewal_total
	 * @param \WC_Order $renewal_order
	 */
	public function wcs_process_scheduled_subscription_payment( $renewal_total, $renewal_order ) {
		/** @var \WC_Subscription[] $subscriptions */
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $renewal_order );

		if ( ! is_array( $subscriptions ) ) {
			return;
		}

		/** @var \WC_Subscription $subscription */
		$subscription = array_pop( $subscriptions );
		/** @var \WC_Order $parent_order */
		$parent_order = $subscription->get_parent();

		if ( $this instanceof CreditCard ) {
			$transaction = new \Payone\Transaction\CreditCard( $this, 'authorization' );
		} elseif ( $this instanceof PayPal ) {
			$transaction = new \Payone\Transaction\PayPal( $this, 'authorization' );
		} elseif ( $this instanceof Invoice ) {
			$transaction = new \Payone\Transaction\Invoice( $this, 'authorization' );
		} elseif ( $this instanceof SepaDirectDebit ) {
			$transaction = new \Payone\Transaction\SepaDirectDebit( $this, 'authorization' );
		} else {
			wp_die( sprintf( __( 'Unsupported payment gateway for subscription: %s', 'payone-woocommerce-3' ), get_class( $this ) ) );
		}

		$transaction->set( 'userid', $parent_order->get_meta( '_payone_userid' ) );
		$transaction->set( 'recurrence', 'recurring' );
		$transaction->set( 'customer_is_present', 'no' );
		$transaction->set( 'amount', (int) ( round( (float) $renewal_total, 2 ) * 100 ) );

		$response = $transaction->execute( $renewal_order );
		if ( $response->is_approved() ) {
			$subscription->payment_complete( $response->get( 'txid' ) );
			$renewal_order->add_order_note( sprintf(
				'PAYONE: %s (PAYONE Reference: %s)',
				__( 'Scheduled subscription payment successful.', 'payone-woocommerce-3' ),
				$transaction->get( 'reference', 'N/A' )
			) );

			return;
		}

		$renewal_order->add_order_note( sprintf(
			'PAYONE: %s (Error: %s)',
			__( 'Scheduled subscription payment failed.', 'payone-woocommerce-3' ),
			$response->get_error_message()
		) );
		$subscription->payment_failed();
	}
}
