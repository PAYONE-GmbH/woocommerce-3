<?php

namespace Payone\Gateway;

abstract class GatewayBase extends \WC_Payment_Gateway {
	public function add( $methods ) {
		$methods[] = get_class($this);

		return $methods;
	}
}