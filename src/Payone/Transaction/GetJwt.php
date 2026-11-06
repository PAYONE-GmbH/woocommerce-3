<?php

namespace Payone\Transaction;

use Payone\Payone\Api\Request;

/**
 * Fetches the JWT that is needed to load the PAYONE Hosted Tokenization SDK.
 * The token is valid for 10 minutes and can be used multiple times.
 */
class GetJwt extends Request {
	/**
	 * Without an Accept header the API answers with HTTP 400.
	 * text/plain returns the usual key=value format.
	 *
	 * @var string
	 */
	protected $accept_header = 'text/plain';

	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct();

		$this
			->set( 'request', 'getJWT' )
			->set_mode( $gateway->get_mode() )
			->set_merchant_id( $gateway->get_merchant_id() )
			->set_portal_id( $gateway->get_portal_id() )
			->set_account_id( $gateway->get_account_id() )
			->set_key( $gateway->get_key() );
	}

	/**
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute() {
		return $this->submit();
	}
}
