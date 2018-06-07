<?php

namespace Payone\Transaction;

class Debit extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'debit' );
		$this->set_data_from_gateway( $gateway );
	}

	/**
	 * @param \WC_Order $order
	 * @param float $amount
	 *
	 * @return bool
	 */
	public function execute( \WC_Order $order, $amount ) {
		$current_sequencenumber = $order->get_meta( '_sequencenumber' );

		$this->set( 'txid', $order->get_transaction_id() );
		$this->set( 'sequencenumber', $this->get_next_sequencenumber( $order ) );
		$this->set( 'amount', $amount * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );

		$response = $this->submit();

		if ( $response->has_error() ) {
			$this->set_sequencenumber( $order, $current_sequencenumber );
			$order->add_order_note( __('Refund could not be processed: ', 'payone-woocommerce-3') . $response->get_error_message() );

			return false;
		}

		$order->update_meta_data( '_refunded', time() );
		$order->save_meta_data();

		return true;
	}
}