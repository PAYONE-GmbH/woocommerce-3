<?php

namespace Payone\Gateway;

interface SubscriptionAwareInterface {
	/**
	 * @return void
	 */
	public function add_subscription_support();

	/**
	 * Add all actions regarding Woocommerce Subscription Plugin.
	 * @return void
	 */
	public function add_subscription_actions();

	/**
	 * @param float $renewal_total
	 * @param \WC_Order $renewal_order
	 *
	 * @return void
	 */
	public function process_woocommerce_scheduled_subscription_payment( $renewal_total, $renewal_order );

	/**
	 * @param \WC_Order $renewal_order
	 *
	 * @return \WC_Subscription|null
	 */
	public function get_subscriptions_for_renewal_order( $renewal_order );
}
