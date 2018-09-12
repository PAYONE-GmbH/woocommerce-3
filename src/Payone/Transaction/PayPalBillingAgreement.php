<?php


namespace Payone\Transaction;


class PayPalBillingAgreement extends PayPal {

	public function __construct( \Payone\Gateway\GatewayBase $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'recurrence', 'recurring' );
	}

	private function requested_token_is_valid( $token_id_form_value ) {
		return 'new' !== $token_id_form_value && \WC_Payment_Tokens::get( $token_id_form_value ) !== null;
	}

	public function add_execution_parameters_for_order( \WC_Order $order ) {
		parent::add_execution_parameters_for_order( $order );

		$token_field_name      = 'wc-' . \Payone\Gateway\PayPalBillingAgreement::GATEWAY_ID . '-payment-token';
		$use_billing_agreement = isset( $_POST[ $token_field_name ] ) && $this->requested_token_is_valid( $_POST[ $token_field_name ] );

		$this->set( 'customer_is_present', $use_billing_agreement ? 'no' : 'yes' );
	}
}
