<?php

namespace Payone\Transaction;

class Capture extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'capture' );
		$this->set_data_from_gateway( $gateway );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return null|\Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		$this->set( 'txid', $order->get_transaction_id() );
		$this->set( 'sequencenumber', $this->get_next_sequencenumber( $order ) );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		// @todo narrative_text

		$is_already_captured = $order->get_meta('_captured');
		if ($is_already_captured) {
			$order->add_order_note( __( 'Capture already done', 'payone-woocommerce-3' ) );

			return null;
		}

		$response = $this->submit();

		if ($response->is_approved()) {
			$order->add_order_note( __( 'Capture successfull', 'payone-woocommerce-3' ) );
			$order->update_meta_data( '_captured', time() );
			$order->save_meta_data();
		} else {
			$order->add_order_note( __( 'Capture failed: ', 'payone-woocommerce-3' ) . $response->get_error_message() );
		}

		return $response;
	}
}