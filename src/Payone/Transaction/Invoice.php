<?php

namespace Payone\Transaction;

class Invoice extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
		$this->set( 'clearingtype', 'rec' );
	}
}