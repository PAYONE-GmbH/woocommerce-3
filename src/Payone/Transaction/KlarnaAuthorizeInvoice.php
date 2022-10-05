<?php

namespace Payone\Transaction;

class KlarnaAuthorizeInvoice extends KlarnaAuthorizeBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'financingtype', 'KIV' );
	}
}
