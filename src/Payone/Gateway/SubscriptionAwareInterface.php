<?php

namespace Payone\Gateway;

interface SubscriptionAwareInterface {
	/**
	 * @return bool
	 */
	public function cart_contains_subscription();

	/**
	 * @return void
	 */
	public function append_subscription_supported_actions();

	/**
	 * @return void
	 */
	public function append_subscription_hooks();

	/**
	 * @see https://docs.woocommerce.com/document/subscriptions/develop/action-reference/
	 *
	 * @param int $subscription_id
	 */
	public function process_scheduled_subscription_payment( $subscription_id );

	/**
	 * @see https://docs.woocommerce.com/document/subscriptions/develop/action-reference/
	 *
	 * @param \WC_Subscription $subscription
	 * @param string $new_status
	 */
	public function process_subscription_payment_failed( $subscription, $new_status );

	/**
	 * Is Woocommerce Subscription Plugin installed and active?
	 * @return bool
	 */
	public function is_wcs_active();
}
