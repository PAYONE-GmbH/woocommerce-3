<?php

namespace Payone\Transaction;

class ManageMandate extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway, $user_id ) {
		parent::__construct( 'managemandate' );
		$this->set_data_from_gateway( $gateway );
		$this->set( 'clearingtype', 'elv' );
		$this->set( 'userid', $user_id );
		$this->set( 'language', 'de' ); // @todo
	}

	public function execute( \WC_Order $order ) {
		$this->set( 'currency', strtoupper( $order->get_currency() ) );

		return $this->submit();
	}
}