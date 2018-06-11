<?php

namespace Payone\Transaction;

use Payone\Plugin;

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
		if ($this->should_submit_cart() ) {
			$this->add_article_list_to_transaction( $order );
		}

		$current_sequencenumber = $order->get_meta( '_sequencenumber' );

		$this->set( 'txid', $order->get_transaction_id() );
		$this->set( 'sequencenumber', $this->get_next_sequencenumber( $order ) );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		// @todo narrative_text

		$is_already_captured = $order->get_meta('_captured');
		if ($is_already_captured) {
			$this->set_sequencenumber( $order, $current_sequencenumber );
			$order->add_order_note( __( 'Capture already done', 'payone-woocommerce-3' ) );

			return null;
		}

		$response = $this->submit();

		Plugin::$send_mail_after_capture = false;
		if ( $response->is_approved() ) {
			$order->add_order_note( __( 'Capture successfull', 'payone-woocommerce-3' ) );
			$order->update_meta_data( '_captured', time() );
			$order->save_meta_data();
			Plugin::$send_mail_after_capture = true;
		} else {
			$this->set_sequencenumber( $order, $current_sequencenumber );
			$order->add_order_note( __( 'Capture failed: ', 'payone-woocommerce-3' ) . $response->get_error_message() );
		}

		$mail = new \WC_Email_Customer_Processing_Order();
		$mail->trigger( null, $order );
		Plugin::$send_mail_after_capture = false;

		return $response;
	}
}