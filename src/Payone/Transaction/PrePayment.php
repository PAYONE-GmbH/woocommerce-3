<?php

namespace Payone\Transaction;

class PrePayment extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'preauthorization' );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'vor' );
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
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );
		$this->set_shipping_data_from_order( $order );
		$this->set_customer_ip_from_order( $order );

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
		$this->set( 'country', 'DE' );
		$this->set( 'lastname', 'Tester' );
		$this->set( 'firstname', 'Tim' );

		return $this->submit()->is_approved();
	}
}
