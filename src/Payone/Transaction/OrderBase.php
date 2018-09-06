<?php

namespace Payone\Transaction;


abstract class OrderBase extends Base {

	/**
	 * OrderBase constructor.
	 *
	 * @param $gateway \Payone\Gateway\GatewayBase
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );
	}

	/**
	 * @param $order \WC_Order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( $order ) {
		$this->set_reference( $order );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );

		if ( $this->should_submit_cart() ) {
			$this->add_article_list_to_transaction( $order );
		}

		$this->add_execution_parameters_for_order( $order );

		return $this->submit();
	}

	/**
	 * @param $order \WC_Order
	 */
	protected function add_callback_urls( $order ) {
		$this->set( 'successurl', Plugin::get_callback_url( 'success' ) . '&oid=' . $order->get_id() );
		$this->set( 'errorurl', Plugin::get_callback_url( 'error' ) . '&oid=' . $order->get_id() );
		$this->set( 'backurl', Plugin::get_callback_url( 'back' ) . '&oid=' . $order->get_id() );
	}

	/**
	 * @param $order \WC_Order
	 *
	 */
	protected function add_execution_parameters_for_order( \WC_Order $order ) {
	}
}
