<?php

namespace Payone\Transaction;

class PrePayment extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );
		$this->set( 'clearingtype', 'vor' );
	}
}
