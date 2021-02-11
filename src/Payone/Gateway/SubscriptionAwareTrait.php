<?php

namespace Payone\Gateway;

trait SubscriptionAwareTrait {
	public static function is_wcs_active() {
		return (bool) in_array(
			'woocommerce-subscriptions/woocommerce-subscriptions.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		);
	}

	public function add_subscription_support() {
		if ( ! $this instanceof \WC_Payment_Gateway ) {
			return;
		}

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
			'multiple_subscriptions',
		] );
	}

	public function add_subscription_actions() {
		if ( ! $this instanceof SubscriptionAwareInterface ) {
			return;
		}

		add_action(
			sprintf( 'woocommerce_scheduled_subscription_payment_%s', (string) $this->id ),
			[ $this, 'process_scheduled_subscription_payment' ],
			10,
			2
		);

		add_action(
			'woocommerce_subscription_renewal_payment_failed',
			[ $this, 'process_woocommerce_subscription_renewal_payment_failed' ],
			10,
			2
		);
	}

	/**
	 * @param \WC_Subscription $subscription
	 *
	 * @return void
	 */
	public function process_woocommerce_subscription_renewal_payment_failed( $subscription ) {
		if ( ! $this instanceof GatewayBase ) {
			return;
		}

		$payone_renewal_payment_fails = (int) $subscription->get_meta( '_payone_renewal_payment_fails' );
		$payone_renewal_payment_fails ++;

		$subscription->update_meta_data( '_payone_renewal_payment_fails', $payone_renewal_payment_fails );

		if ( ! $this->is_payone_subscription_auto_failover_enabled() ) {
			return;
		}

		$subscription->set_payment_method( new Invoice() );
	}

	public function process_scheduled_subscription_payment( $renewal_total, $renewal_order ) {
		//Dummy method. Please implement for each payment method.
	}

	public function get_subscriptions_for_renewal_order( $renewal_order ) {
		if ( $renewal_order->get_status() !== 'pending' || ! $renewal_order->needs_payment() ) {
			return null;
		}

		/** @var \WC_Subscription[] $subscriptions */
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $renewal_order );
		$subscription  = reset( $subscriptions );

		if ( ! $subscription instanceof \WC_Subscription ) {
			return null;
		}

		return $subscription;
	}
}
