<?php

namespace Payone\Gateway;

class PayPalBillingAgreement extends RedirectGatewayBase {
	const GATEWAY_ID = 'bs_payone_paypal_billing_agreement';

	protected function human_readable_name() {
		return __( 'PayPal Billing Agreement', 'payone-woocommerce-3' );
	}
}
