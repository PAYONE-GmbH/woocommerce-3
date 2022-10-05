<?php

namespace Payone\Gateway;

class PayPal extends PayPalBase {

	const GATEWAY_ID = 'bs_payone_paypal';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-paypal.png';
		$this->method_title       = 'PAYONE ' . __( 'PayPal', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'PayPal', 'payone-woocommerce-3' ) );
	}
}
