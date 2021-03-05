<?php

namespace Payone\Gateway;

interface SubscriptionAwareInterface {
	/**
	 * @return void
	 */
	public function add_subscription_support();
}
