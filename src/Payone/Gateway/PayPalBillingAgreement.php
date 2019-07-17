<?php

namespace Payone\Gateway;

class PayPalBillingAgreement extends RedirectGatewayBase {
	const GATEWAY_ID = 'bs_payone_paypal_billing_agreement';

	protected function human_readable_name() {
		return __( 'PayPal Billing Agreement', 'payone-woocommerce-3' );
	}

	public function __construct() {
		parent::__construct();

		$this->supports[] = 'tokenization';

		add_filter('woocommerce_payment_gateway_get_new_payment_method_option_html_label', [$this, 'use_new_paypal_account_text'], 10, 2);
	}

	public function payment_fields() {
		parent::payment_fields();

		if ( is_checkout() ) {
			$this->tokenization_script();
			$this->saved_payment_methods();
		}
	}

	protected function payment_successful( $order ) {
		\WC_Payment_Token_PayPalBillingAgreement::create_for_customer( $order->get_customer_id(), self::GATEWAY_ID );
	}

	protected function payment_error( $order ) {
		$customer_id = $order->get_customer_id();
		$tokens      = \WC_Payment_Tokens::get_customer_tokens( $customer_id, self::GATEWAY_ID );
		foreach ( $tokens as $token ) {
			$token->delete();
		}
	}

	public function use_new_paypal_account_text( $text, $gateway ) {
		if ( $gateway->id == self::GATEWAY_ID ) {
			$text = __( 'Connect new PayPal Account', 'payone-woocommerce-3' );
		}

		return $text;
	}

}
