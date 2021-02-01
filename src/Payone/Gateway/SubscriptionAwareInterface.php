<?php

namespace Payone\Gateway;

interface SubscriptionAwareInterface {
	/**
	 * @return void
	 */
	public function append_subscription_supported_actions();
}
