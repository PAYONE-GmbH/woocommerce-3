<?php

namespace Payone\Transaction;

class Sofort extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'clearingtype', 'sb' );
		$this->set( 'onlinebanktransfertype', 'PNT' );
	}

	/**
	 * @param \WC_Order $order
	 */
	public function add_execution_parameters_for_order( \WC_Order $order ) {
		$this->set( 'bankcountry', $this->get( 'country' ) );
		$this->add_callback_urls( $order );
	}
}
