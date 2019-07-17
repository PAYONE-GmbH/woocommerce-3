<?php

namespace Payone\Transaction;

class SepaDirectDebit extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'mandate_identification', $_POST['direct_debit_reference'] );
		$this->set( 'clearingtype', 'elv' );
		$this->set( 'iban', $_POST['direct_debit_iban'] );
	}
}
