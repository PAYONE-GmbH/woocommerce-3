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
}
