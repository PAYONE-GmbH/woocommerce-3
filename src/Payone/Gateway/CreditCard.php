<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Check3D;

class CreditCard extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_creditcard';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'BS PAYONE Kreditkarte';
		$this->method_description = 'method_description';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Creditcard', 'payone' ) );
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
		$hash    = $this->calculate_hash( $options );

		include PAYONE_VIEW_PATH . '/gateway/creditcard/payment-form.php';
	}

	public function process_payment( $order_id ) {
		$order = new \WC_Order( $order_id );

		$is_success = false;
		$make_redirect = false;
		if ( $this->is_redirect( 'success' ) ) {
			$make_redirect = true;
			$is_success = $order->get_meta( '_appointed' ) > 0;
		} elseif ( $this->is_redirect( 'error' ) ) {
			$make_redirect = true;
			$is_success = false;
			wc_add_notice( __( 'Payment error: ', 'payone' ), 'error' );
		} else {
			$transaction = new \Payone\Transaction\CreditCard( $this );
			$response    = $transaction->execute( $order );

			$order->set_transaction_id( $response->get( 'txid' ) );

			$authorization_method = $transaction->get( 'request' );
			$order->update_meta_data( '_authorization_method', $authorization_method );
			$order->save_meta_data();
			$order->save();

			if ( $response->is_redirect() ) {
				return [
					'result'   => 'success',
					'redirect' => $response->get_redirect_url(),
				];
			}

			if ( $response->has_error() ) {
				wc_add_notice( __( 'Payment error: ', 'payone' ) . $response->get_error_message(), 'error' );
			} else {
				$is_success = true;
			}
		}

		if ( $is_success ) {
			$this->handle_successfull_payment( $order );
			$target_url = $this->get_return_url( $order );

			if ( $make_redirect ) {
				wp_redirect( $target_url );
				exit;
			}

			return array(
				'result'   => 'success',
				'redirect' => $target_url,
			);
		}

		if ( $make_redirect ) {
			echo 'Hier muss zur Fehlerseite umgeleitet werden';
			exit;
		}

		return;
	}

	private function handle_successfull_payment( \WC_Order $order ) {
		global $woocommerce;

		$authorization_method = $order->get_meta( '_authorization_method' );

		if ( $authorization_method === 'preauthorization' ) {
			$order->update_status( 'on-hold', __( 'Credit card payment is preauthorized.', 'woocommerce' ) );
		} elseif ( $authorization_method === 'authorization' ) {
			$order->update_status( 'processing',
				__( 'Credit card payment is authorized and captured.', 'woocommerce' ) );
		}

		wc_reduce_stock_levels( $order->get_id() );
		$woocommerce->cart->empty_cart();
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		$order = $transaction_status->get_order();
		if ( $transaction_status->is_paid() || $transaction_status->is_capture() ) {
			$order->update_status( 'wc-processing', __( 'Payment received.', 'payone' ) );
		} else {
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		if ( $from_status === 'on-hold' && $to_status === 'processing' ) {
			// @todo Reagieren, wenn Capture fehlschlägt?
			$this->capture( $order );
		}
	}

	/**
	 * @param array $options
	 *
	 * @return string
	 */
	public function calculate_hash( $options ) {
		return md5(
			$options['account_id']
			. 'UTF-8'
			. $options['merchant_id']
			. $options['mode']
			. $options['portal_id']
			. 'creditcardcheck'
			. 'JSON'
			. 'yes'
			. $options['key']
		);
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	private function is_redirect( $type ) {
		return $_GET['type'] === $type;
	}
}