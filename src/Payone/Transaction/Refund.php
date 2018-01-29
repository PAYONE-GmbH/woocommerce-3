<?php

namespace Payone\Transaction;

class refund extends Base {
	public function __construct() {
		parent::__construct();

		$this->set( 'clearingtype', 'rec' );
	}

	public function execute( \WC_Order $order, $amount ) {
		$this->set( 'request', 'refund' );
		$this->set( 'txid', $order->get_transaction_id() );
		$this->set( 'sequencenumber', 1 ); // @todo
		$this->set( 'amount', - $amount * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );

		return $this->submit();
	}
}