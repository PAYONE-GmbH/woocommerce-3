<?php

namespace Payone\Transaction;

class PrePayment extends Base {
	public function __construct() {
		parent::__construct();

		$this->set( 'clearingtype', 'vor' );
	}

	public function execute( \WC_Order $order ) {
		$this->set( 'request', 'preauthorization' );
		$this->setDataFromOrder( $order );

		return $this->submit();
	}
}