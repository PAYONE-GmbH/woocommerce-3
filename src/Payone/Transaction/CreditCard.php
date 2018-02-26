<?php

namespace Payone\Transaction;

class CreditCard extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'cc' );
		$this->set( 'card_pseudopan', $_POST['card_pseudopan'] );
		$this->set( 'card_truncatedpan', $_POST['card_truncatedpan'] );
		$this->set( 'card_firstname', $_POST['card_firstname'] );
		$this->set( 'card_lastname', $_POST['card_lastname'] );
		$this->set( 'card_type', $_POST['card_type'] );
		$this->set( 'card_expiredate', $_POST['card_expiredate'] );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		$this->set( 'reference', $order->get_id() );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->setPersonalDataFromOrder( $order );
		$this->set( 'pseudocardpan', $this->get('card_pseudopan' ) );

		return $this->submit();
	}
}