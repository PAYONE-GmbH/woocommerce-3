<?php

namespace Payone\Subscription\PaymentMethod;

interface SubscriptionInterface {
	/**
	 * @param \WC_Subscription $subscription
	 * @param \WC_Order $order
	 */
	public function process_scheduled_subscription_payment( $subscription, $order );
}
