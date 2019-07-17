<?php

namespace Payone\Transaction;

class PayPal extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PPE' );
	}

	/**
	 * @param \WC_Order $order
	 */
	public function add_execution_parameters_for_order( \WC_Order $order ) {
		$this->add_callback_urls( $order );
	}
}
