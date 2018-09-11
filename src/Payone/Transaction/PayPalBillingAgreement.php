<?php


namespace Payone\Transaction;


class PayPalBillingAgreement extends PayPal {

	public function __construct( \Payone\Gateway\GatewayBase $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'customer_is_present', 'yes' );
		$this->set( 'recurrence', 'recurring' );
	}

}
