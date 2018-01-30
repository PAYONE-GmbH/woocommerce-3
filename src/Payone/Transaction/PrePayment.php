<?php

namespace Payone\Transaction;

class PrePayment extends Base {
	public function __construct( $requestType ) {
		parent::__construct( $requestType );

		$this->set( 'clearingtype', 'vor' );
	}

	public function execute( \WC_Order $order ) {
		$this->set( 'reference', $order->get_id() );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->setPersonalDataFromOrder( $order );

		return $this->submit();
	}
}