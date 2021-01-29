<?php

namespace Payone\Gateway;

trait SubscriptionAwareTrait {

	/** @var bool $is_wcs_active */
	private $is_wcs_active = null;

	public function cart_contains_subscription() {
		if ( $this->is_wcs_active() ) {
			return (bool) \WC_Subscriptions_Cart::cart_contains_subscription();
		}

		return false;
	}

	public function is_wcs_active() {
		if ( $this->is_wcs_active !== null ) {
			return $this->is_wcs_active;
		}

		if ( ! (bool) class_exists( 'WC_Subscription' ) ) {
			return false;
		}

		$this->is_wcs_active = (bool) in_array(
			'woocommerce-subscriptions/woocommerce-subscriptions.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		);

		return $this->is_wcs_active;
	}

	public function append_subscription_hooks() {
		if ( ! $this instanceof \WC_Payment_Gateway ) {
			return;
		}

		add_action( 'woocommerce_scheduled_subscription_payment', [ $this, 'process_scheduled_subscription_payment' ] );
		add_action( 'woocommerce_subscription_payment_failed', [ $this, 'process_subscription_payment_failed' ] );
	}

	public function process_scheduled_subscription_payment( $subscription_id ) {
		//Dummy method. Please implement for each payment method.
	}

	public function process_subscription_payment_failed( $subscription, $new_status ) {
		//Dummy method. Please implement for each payment method.
	}

	public function append_subscription_supported_actions() {
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
}
