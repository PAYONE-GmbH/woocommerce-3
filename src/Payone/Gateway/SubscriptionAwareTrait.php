<?php

namespace Payone\Gateway;

trait SubscriptionAwareTrait {
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
			[ $this, 'process_woocommerce_scheduled_subscription_payment' ],
			10,
			2
		);
	}

	public function process_woocommerce_scheduled_subscription_payment( $renewal_total, $renewal_order ) {
		//Dummy method. Please implement for each payment method.
	}

	public function get_subscriptions_for_renewal_order( $renewal_order ) {
		if ( $renewal_order->get_status() !== 'pending' || ! $renewal_order->needs_payment() ) {
			return null;
		}

		/** @var \WC_Subscription[] $subscriptions */
		$subscriptions = wcs_get_subscriptions_for_renewal_order( $renewal_order );

		if ( ! is_array( $subscriptions ) ) {
			return null;
		}

		$subscription = array_pop( $subscriptions );

		if ( ! $subscription instanceof \WC_Subscription ) {
			return null;
		}

		return $subscription;
	}
}
