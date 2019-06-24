<?php

namespace Payone\Gateway;

class PayPal extends RedirectGatewayBase {
	const GATEWAY_ID = 'bs_payone_paypal';

	protected function human_readable_name() {
		return __( 'PayPal', 'payone-woocommerce-3' );
	}
}
