<?php

class WC_Payment_Token_PayPalBillingAgreement extends WC_Payment_Token {

	protected $type = 'PayPalBillingAgreement';

	public function get_display_name( $deprecated = '' ) {
		return __( 'Existing PayPal Account', 'payone-woocommerce-3' );
	}

	public static function create_for_customer( $customer_id, $gateway_id ) {
		if ( empty( WC_Payment_Tokens::get_customer_tokens( $customer_id, $gateway_id ) ) ) {
			$token = new \WC_Payment_Token_PayPalBillingAgreement();
			$token->set_token( 'has_agreement' );
			$token->set_gateway_id( $gateway_id );
			$token->set_user_id( $customer_id );
			$token->set_default( true );
			$token->save();
		}
	}

}