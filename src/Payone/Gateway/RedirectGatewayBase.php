<?php

namespace Payone\Gateway;

abstract class RedirectGatewayBase extends GatewayBase {
	/**
	 * This method is responsible for handling both the payment initiation
	 * and the processing of returning customers which are coming from redirect payment
	 * processes via the success-, error- and backurl parameters.
	 *
	 * @param int $order_id
	 * @param string $transaction_class
	 *
	 * @return array|void
	 * @throws \WC_Data_Exception
	 */
	public function process_redirect( $order_id, $transaction_class ) {
		$order = new \WC_Order( $order_id );

		if ( $this->is_redirect( 'success' ) ) {
			// We are back in the shop via the provided successurl
			// Log missing appointed flag for this order.
			// Maybe the shop was too slow returning TSOK for the APPOINTED
			// TX status notification.
			if ( $order->get_meta( '_appointed' ) > 0 ) {
				error_log( "No appointed flag set for order {$order->get_id()} before the customer received the checkout success page." );
			}

			$this->handle_successfull_payment( $order );
			$target_url = $this->get_return_url( $order );

			wp_redirect( $target_url );
			exit;
		}

		if ( $this->is_redirect( 'error' ) ) {
			// We are back in the shop via the provided errorurl
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . __( 'Payment provider returned error', 'payone-woocommerce-3' ), 'error' );
			wp_redirect( wc_get_checkout_url() );
			exit;
		}

		if ( $this->is_redirect( 'back' ) ) {
			// We are back in the shop via the provided backurl
			$order->update_status(
				'cancelled',
				__( 'Payment was canceled by user', 'payone-woocommerce-3' ) . '.'
			);
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . __( 'Payment was canceled by user', 'payone-woocommerce-3' ), 'error' );
			wp_redirect( wc_get_checkout_url() );
			exit;
		}

		// We are initiating the payment and may redirect the customer depending on the
		// PAYONE API response. Especially for credit cards sometimes redirects occur
		// sometimes not.

		/** @var \Payone\Transaction\Base $transaction */
		$transaction = new $transaction_class( $this );

		// According to https://docs.woocommerce.com/document/subscriptions/develop/payment-gateway-integration/
		// If order passed to process_redirect() is actual subscription itself, that means that this is a payment
        // method change request.
		// We need to set some specific parameters for PayOne request that are described in
        // https://docs.payone.com/display/public/PLATFORM/Special+remarks+-+PayPal
		if ( $this->order_is_subscription( $order ) ) {
			$transaction->set( 'amount', 0 );
			// PMCR stands for (P)ayment (M)ethod (C)hange (R)equest
			// There is a possibility that this particular order ID was already used and failed. If that is
			// the case, PayOne would always return that "Reference ID already exists" error.
			$transaction->set( 'reference', sprintf( '%d-PMCR', (int) $order->get_id() ) );
			if ( $transaction_class === \Payone\Transaction\PayPal::class ) {
				$transaction->set( 'amount', 1 );
			}
		}

		if ( $this->order_contains_subscription( $order ) ) {
			// This is initial payment for (possibly multiple) subscription.
			$transaction->set( 'recurrence', 'recurring' );
			$transaction->set( 'customer_is_present', 'yes' );
		}

		/** @var \Payone\Payone\Api\Response $response */
		$response = $transaction->execute( $order );

		$order->set_transaction_id( $response->get( 'txid' ) );

		$authorization_method = $transaction->get( 'request' );
		$order->update_meta_data( '_authorization_method', $authorization_method );

		if ( $this->order_is_subscription( $order ) ) {
			// We need to get and save some data from PayOne response if this is payment method change request.
			// See comment above, on the first "if ( $this->order_is_subscription( $order ) )" clause.
			$order->update_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
			$order->update_meta_data( '_payone_pseudocardpan', (string) $_POST['card_pseudopan'] );
			$order->save_meta_data();
		}

		if ( $this->order_contains_subscription( $order ) ) {
			// Set necessary data for (possible multiple) subscription, so we can charge recurring payments.
			foreach ( wcs_get_subscriptions_for_order( $order ) as $subscription ) {
				/** @var \WC_Subscription $subscription */
				$subscription->update_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
				$subscription->update_meta_data( '_payone_pseudocardpan', (string) $_POST['card_pseudopan'] );
				$subscription->save_meta_data();
			}
		}

		$order->save_meta_data();
		$order->save();

		// Perform the redirect if we need to
		if ( $response->is_redirect() ) {
			return [
				'result'   => 'success',
				'redirect' => $response->get_redirect_url(),
			];
		}

		// At this point a redirect was not required,
		// we are processing the payment as regular.
		if ( $response->has_error() ) {
			// According to the WooCommerce docs we just return nothing to indicate a payment error
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(), 'error' );
		} else {
			// No error, complete the payment
			$this->handle_successfull_payment( $order );
			$target_url = $this->get_return_url( $order );

			return [
				'result'   => 'success',
				'redirect' => $target_url,
			];
		}
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
