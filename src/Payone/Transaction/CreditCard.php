<?php

namespace Payone\Transaction;

class CreditCard extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'clearingtype', 'cc' );
		$this->set( 'pseudocardpan', $_POST['card_pseudopan'] );
	}

	/**
	 * @param \WC_Order $order
	 *
	 */
	public function add_execution_parameters_for_order( \WC_Order $order ) {
		$this->add_callback_urls( $order );
	}
}