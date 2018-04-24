<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class Sofort extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_sofort';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'BS PAYONE Sofortüberweisung';
		$this->method_description = 'method_description';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'SOFORT.com', 'payone-woocommerce-3' ) );
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
		$hash    = $this->calculate_hash( $options );

		include PAYONE_VIEW_PATH . '/gateway/sofort/payment-form.php';
	}

	// @todo Der Ablauf ist hier tatsächlich (bis auf die Texte) identisch zur Kreditkarte
	public function process_payment( $order_id ) {
		$order = new \WC_Order( $order_id );

		$is_success = false;
		$make_redirect = false;
		if ( $this->is_redirect( 'success' ) ) {
			$make_redirect = true;
			$is_success = $order->get_meta( '_appointed' ) > 0;
			if ( ! $is_success ) {
				wc_add_notice( __( 'Payment error: ',
						'payone-woocommerce-3' ) . __( 'Did not receive "appointed" callback',
						'payone-woocommerce-3' ),
					'error' );
			}
		} elseif ( $this->is_redirect( 'error' ) ) {
			$make_redirect = true;
			$is_success = false;
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . __( 'SOFORT.com returned error',
					'payone-woocommerce-3' ), 'error' );
		} else {
			$transaction = new \Payone\Transaction\Sofort( $this );
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
				wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(), 'error' );
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
			wp_redirect( wc_get_checkout_url() );
			exit;
		}

		return [
			'result'   => 'error',
			'redirect' => wc_get_checkout_url(),
		];
	}

	private function handle_successfull_payment( \WC_Order $order ) {
		global $woocommerce;

		$authorization_method = $order->get_meta( '_authorization_method' );

		if ( $authorization_method === 'preauthorization' ) {
			$order->update_status( 'on-hold', __( 'SOFORT.com payment is preauthorized.', 'woocommerce' ) );
		} elseif ( $authorization_method === 'authorization' ) {
			$order->add_order_note( __( 'SOFORT.com payment is authorized and captured.', 'woocommerce' ) );
			$order->payment_complete();
		}

		wc_reduce_stock_levels( $order->get_id() );
		$woocommerce->cart->empty_cart();
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->is_appointed() ) {
			return;
		}

		$order = $transaction_status->get_order();
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'authorization' && $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_capture() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_paid() ) {
			// Do nothing. Everything already happened.
		} else {
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone-woocommerce-3' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
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
		return isset( $_GET['type'] ) && $_GET['type'] === $type;
	}
}