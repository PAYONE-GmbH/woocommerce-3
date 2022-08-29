<?php

namespace Payone\Transaction;

class RatepayCalculate extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 * @param string $authorization_method
	 */
	public function __construct( $gateway, $authorization_method = null ) {
		parent::__construct( 'genericpayment' );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'fnc' );
		$this->set( 'financingtype', 'RPS' );
		$this->set( 'add_paydata[action]', 'calculation' );
		$this->set( 'add_paydata[customer_allow_credit_inquiry]', 'yes' );
	}

	/**
	 * @param string $shop_id
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( $shop_id ) {
		$this->set( 'add_paydata[shop_id]', $shop_id );
		$this->set( 'currency', strtoupper( get_woocommerce_currency() ) );
		$this->set( 'amount', 100 * WC()->cart->get_total( 'non-view' ) );

		return $this->submit();
	}
}
