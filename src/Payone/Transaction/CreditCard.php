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
		$card_pseudopan = isset( $_POST['card_pseudopan'] ) ? $_POST['card_pseudopan'] : '';
		$this->set( 'pseudocardpan', $card_pseudopan );
		$card_holder = isset( $_POST['card_holder'] ) ? $_POST['card_holder'] : '';
		$this->set( 'cardholder', $card_holder );
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
		$this->set_once( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );
		$this->set_shipping_data_from_order( $order );
		$this->set_customer_ip_from_order( $order );
		$this->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'success', 'oid' => $order->get_id() ] ) );
		$this->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'error', 'oid' => $order->get_id() ] ) );
		$this->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back', 'oid' => $order->get_id() ] ) );

		return $this->submit();
	}

	/**
	 * @return bool
	 */
	public function test_request_successful() {
		$this->set( 'request', 'preauthorization' );
		$this->set( 'reference', 'test' . $this->get( 'clearingtype' ) . '_' . ( random_int( time() - 1000, time() ) ) );
		$this->set( 'amount', 100 );
		$this->set( 'currency', 'EUR' );
		$this->set( 'pseudocardpan', '5500000000099999' );
		$this->set( 'cardtype', 'M' );
		$this->set( 'cardexpiredate', date( 'y' ) . '12' );
		$this->set( 'cardpan', '5500000000000004' );
		$this->set( 'lastname', 'Tester' );
		$this->set( 'firstname', 'Tim' );
		$this->set( 'country', 'DE' );
		$this->set( 'ecommercemode', 'internet' );

		return $this->submit()->is_approved();
	}
}
