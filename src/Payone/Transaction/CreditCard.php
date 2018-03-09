<?php

namespace Payone\Transaction;

use Payone\Plugin;

class CreditCard extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'cc' );
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
		$this->set( 'successurl', Plugin::get_callback_url('success') . '&oid=' . $order->get_id() );
		$this->set( 'errorurl', Plugin::get_callback_url('error') . '&oid=' . $order->get_id() );
		$this->set( 'backurl', Plugin::get_callback_url('back') . '&oid=' . $order->get_id() );

		return $this->submit();
	}
}