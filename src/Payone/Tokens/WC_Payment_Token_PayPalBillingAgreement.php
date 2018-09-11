<?php

class WC_Payment_Token_PayPalBillingAgreement extends WC_Payment_Token {

	protected $type = 'PayPalBillingAgreement';

	public function get_display_name( $deprecated = '' ) {
		return __( 'Existing PayPal Account', 'payone-woocommerce-3' );
	}

}