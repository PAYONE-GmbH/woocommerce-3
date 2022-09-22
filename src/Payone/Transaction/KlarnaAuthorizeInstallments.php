<?php

namespace Payone\Transaction;

class KlarnaAuthorizeInstallments extends KlarnaAuthorizeBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 * @param string $authorization_method
	 */
	public function __construct( $gateway, $authorization_method = null ) {
		parent::__construct( $gateway, $authorization_method );

		$this->set( 'financingtype', 'KIS' );
	}
}
