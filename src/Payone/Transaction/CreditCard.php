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
		$this->set( 'pseudocardpan', $_POST['card_pseudopan'] );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		if ( $this->should_submit_cart() ) {
			$this->add_article_list_to_transaction( $order );
		}

		$this->set_reference( $order );
		$this->set( 'amount', (string) $this->get( 'amount', (string) ( $order->get_total() * 100 ) ) );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );
		$this->set_shipping_data_from_order( $order );
		$this->set_customer_ip_from_order( $order );
		$this->set( 'successurl', Plugin::get_callback_url( 'success' ) . '&oid=' . $order->get_id() );
		$this->set( 'errorurl', Plugin::get_callback_url( 'error' ) . '&oid=' . $order->get_id() );
		$this->set( 'backurl', Plugin::get_callback_url( 'back' ) . '&oid=' . $order->get_id() );

		return $this->submit();
	}
}
