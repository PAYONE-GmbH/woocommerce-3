<?php

namespace Payone\Transaction;

class Check3D extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( '3dscheck' );
		$this->set_data_from_gateway( $gateway );
	}

	public function execute( \WC_Order $order, $amount ) {
		$this->set( 'txid', $order->get_transaction_id() );
		$this->set( 'amount', $amount * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );

		return $this->submit();
	}
}