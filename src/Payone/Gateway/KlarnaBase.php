<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;
use Payone\Transaction\Capture;
use Payone\Transaction\Debit;

abstract class KlarnaBase extends RedirectGatewayBase {

	const SESSION_KEY_SESSION_STARTED = 'payone_klarna_session_started';

	public function __construct( $id ) {
		parent::__construct( $id );

		$this->icon                  = PAYONE_PLUGIN_URL . 'assets/icon-klarna.png';
		$this->hide_when_no_shipping = true;
	}

	public function process_start_session( $data ) {
		$transaction = new \Payone\Transaction\KlarnaStartSession( $this );

		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'success' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back' ] ) );

		$transaction->set( 'add_paydata[shipping_telephonenumber]', $data['shipping_telephonenumber'] );
		unset( $data['shipping_telephonenumber'] );
		$transaction->set( 'add_paydata[shipping_email]', $data['shipping_email'] );
		unset( $data['shipping_email'] );

		foreach ( $data as $field => $value ) {
			$transaction->set( $field, $value );
		}

		$response = $transaction->execute( WC()->cart );

		if ( $response->get( 'status' ) === 'OK' ) {
			$result = [
				'status'                 => 'ok',
				'client_token'           => $response->get( 'add_paydata[client_token]' ),
				'workorderid'            => $response->get( 'workorderid' ),
				'data_for_authorization' => $transaction->get_data_for_authorization( WC()->cart ),
			];

		} else {
			$result = [
				'status'  => 'error',
				'code'    => $response->get( 'errorcode' ),
				'message' => $response->get( 'customermessage' ),
			];
		}

		echo json_encode( $result );
		exit;
	}

	protected function add_data_to_capture( Capture $capture, \WC_Order $order ) {
		$capture->set( 'capturemode', 'completed' );
	}

	protected function add_data_to_debit( Debit $debit, \WC_Order $order ) {
		$debit->set( 'settleaccount', 'auto' );
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->no_further_action_necessary() ) {
			return;
		}

		$order                = $transaction_status->get_order();
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'authorization' && $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment is authorized by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_capture() ) {
			$order->add_order_note( __( 'Payment is captured by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} else {
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone-woocommerce-3' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );

		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}
}
