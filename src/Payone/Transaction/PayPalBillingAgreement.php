<?php


namespace Payone\Transaction;


class PayPalBillingAgreement extends PayPal {

	public function __construct( \Payone\Gateway\GatewayBase $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'recurrence', 'recurring' );
	}

	public function add_execution_parameters_for_order( \WC_Order $order ) {
		parent::add_execution_parameters_for_order( $order );

		$token_field_name      = 'wc-' . \Payone\Gateway\PayPalBillingAgreement::GATEWAY_ID . '-payment-token';
		$use_billing_agreement = isset( $_POST[ $token_field_name ] ) && 'new' !== $_POST[ $token_field_name ];

		// We do not need to load a token from the database since Payone will check if this customer has a valid
		// billing agreement (== token) for us.
		$this->set( 'customer_is_present', $use_billing_agreement ? 'no' : 'yes' );
	}
}
