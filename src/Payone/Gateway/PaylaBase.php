<?php

namespace Payone\Gateway;

use Payone\Plugin;
use Payone\Payone\Api\TransactionStatus;

class PaylaBase extends GatewayBase {
	const PAYLA_PARTNER_ID = 'e7yeryF2of8X';
	const SESSION_KEY_PAYLA_FAILED = 'payone_payla_failed';

	public function __construct( $id ) {
		parent::__construct( $id );

		$this->supported_currencies  = [ 'EUR' ];
	}

	public function is_available() {
		$is_available = parent::is_available();

		$failed_before = Plugin::get_session_value( self::SESSION_KEY_PAYLA_FAILED );
		if ( $failed_before ) {
			$is_available = false;
		}

		if ( $is_available && $this->get_option( 'allow_different_shopping_address', 'no' ) === 'no' ) {
			if ( $this->has_divergent_shipping_address() ) {
				$is_available = false;
			}
		}

		return $is_available;
	}

	public function payla_request_failed() {
		global $woocommerce;

		Plugin::set_session_value( self::SESSION_KEY_PAYLA_FAILED, 1 );
		$woocommerce->session->set( 'reload_checkout ', 'true' );
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->no_further_action_necessary() ) {
			return;
		}

		$order = $transaction_status->get_order();

		if ( $transaction_status->is_overpaid() ) {
			$order->add_order_note( __( 'Payment received. Customer overpaid!', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $transaction_status->is_underpaid() ) {
			$order->add_order_note( __( 'Payment received. Customer underpaid!', 'payone-woocommerce-3' ) );
		} elseif ( $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );

		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}

	protected function get_error_message( \Payone\Payone\Api\Response $response ) {
		if ( $response->get_error_code() === 307 ) {
			return __( 'This payment method is not available. Please select another.', 'payone-woocommerce-3' );
		}

		return $response->get_error_message();
	}
}