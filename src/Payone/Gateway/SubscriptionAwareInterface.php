<?php

namespace Payone\Gateway;

interface SubscriptionAwareInterface {
	/**
	 * Is Woocommerce Subscription Plugin installed and active?
	 * @return bool
	 */
	public static function is_wcs_active();

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
	public function process_scheduled_subscription_payment( $renewal_total, $renewal_order );

	/**
	 * @param \WC_Subscription $subscription
	 *
	 * @return void
	 */
	public function process_woocommerce_subscription_renewal_payment_failed( $subscription );

	/**
	 * @param \WC_Order $renewal_order
	 *
	 * @return \WC_Subscription|null
	 */
	public function get_subscriptions_for_renewal_order( $renewal_order );
}
