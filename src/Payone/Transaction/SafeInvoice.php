<?php

namespace Payone\Transaction;

class SafeInvoice extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'clearingtype', 'rec' );
		$this->set( 'clearingsubtype', 'POV' );
		$this->set( 'businessrelation', 'b2c' ); // @todo
	}
}
