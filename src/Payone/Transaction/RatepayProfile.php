<?php

namespace Payone\Transaction;

class RatepayProfile extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
     * @param string $authorization_method
	 */
	public function __construct( $gateway, $authorization_method = null ) {
		parent::__construct( 'genericpayment' );
		$this->set_data_from_gateway( $gateway );

        $this->set( 'clearingtype', 'fnc' );
        $this->set( 'financingtype', $gateway->get_financingtype() );
        $this->set( 'add_paydata[action]', 'profile' );
	}

	/**
	 * @param string $shop_id
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( $shop_id ) {
		$this->set( 'add_paydata[shop_id]', $shop_id );
        $this->set( 'currency', strtoupper( get_woocommerce_currency() ) );

		return $this->submit();
	}
}
