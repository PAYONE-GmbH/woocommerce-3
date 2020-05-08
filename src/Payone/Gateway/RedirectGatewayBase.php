<?php

namespace Payone\Gateway;

abstract class RedirectGatewayBase extends GatewayBase {
	/**
	 * @param int $order_id
	 * @param string $transaction_class
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_redirect( $order_id, $transaction_class ) {
		$order = new \WC_Order( $order_id );

		$is_success = false;
		$make_redirect = false;
		if ( $this->is_redirect( 'success' ) ) {
			$make_redirect = true;
			$is_success = $order->get_meta( '_appointed' ) > 0;
			if ( ! $is_success ) {
			    error_log("No appointed flag set for order {$order->get_id()} before the customer received the checkout success page.");
				// wc_add_notice( __( 'Payment error: ',
				// 		'payone-woocommerce-3' ) . __( 'Did not receive "appointed" callback',
				// 		'payone-woocommerce-3' ),
				// 	'error' );
			}
		} elseif ( $this->is_redirect( 'error' ) ) {
			$make_redirect = true;
			$is_success = false;
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . __( 'Payment provider returned error',
					'payone-woocommerce-3' ), 'error' );
		} elseif ( $this->is_redirect( 'back' ) ) {
			$make_redirect = true;
			$is_success = false;
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . __( 'Payment was canceled by user',
					'payone-woocommerce-3' ), 'error' );
		} else {
			/** @var \Payone\Transaction\Base $transaction */
			$transaction = new $transaction_class( $this );

			/** @var \Payone\Payone\Api\Response $response */
			$response = $transaction->execute( $order );

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
			$order->update_status( 'on-hold', __( 'Payment was pre-authorized by PAYONE, please initiate a capture to complete the payment.', 'payone-woocommerce-3' ) );
		} elseif ( $authorization_method === 'authorization' ) {
		    // todo: maybe add an option for the merchant to select if the payment should be completed at this point or later in the processing of a PAID TX status
			$order->add_order_note( __( 'Payment was authorized by PAYONE, the payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		}

		// todo: maybe add an option for the merchant to select whether or not the stock should be decreased at this point for pre-authorization payments
		wc_reduce_stock_levels( $order->get_id() );

		$woocommerce->cart->empty_cart();
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
