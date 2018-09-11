<?php

namespace Payone\Gateway;

class Sofort extends RedirectGatewayBase {
	const GATEWAY_ID = 'bs_payone_sofort';

	protected function human_readable_name() {
		return __( 'SOFORT.com', 'payone-woocommerce-3' );
	}
}
