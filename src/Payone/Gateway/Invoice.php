<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\WooCommerceSubscription\WCSAwareGatewayTrait;
use Payone\WooCommerceSubscription\WCSHandler;

class Invoice extends GatewayBase {

	use WCSAwareGatewayTrait;

	const GATEWAY_ID = 'bs_payone_invoice';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		if ( WCSHandler::is_wcs_active() ) {
			$this->add_wcs_support();
		}

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-rechnungskauf.png';
		$this->method_title       = 'PAYONE ' . __( 'Invoice', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Invoice', 'payone-woocommerce-3' ) );
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/invoice/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		if ( WCSHandler::is_wcs_active() && WCSHandler::is_subscription( $order ) ) {
			if ( (int) $order->get_total() === 0 ) {
				// We don't need to do anything. This is just the start of the trial period without any upfront cost.
				$order->add_order_note( __( 'Subscription started. No invoice necessary at the moment.', 'payone-woocommerce-3' ) );
				$order->payment_complete();

				// Return thankyou redirect
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}

			if ( method_exists( $this, 'wcs_get_transaction_for_subscription_signup' ) ) {
				$transaction = $this->wcs_get_transaction_for_subscription_signup( $order );
			}
		} else {
			$transaction = new \Payone\Transaction\Invoice( $this );
		}

		$response = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(),
				'error' );

			return;
		}
		// @todo Bei Kauf auf Rechnung anderer Status und Order abschließen?

		$order->set_transaction_id( $response->get( 'txid' ) );
		$order->add_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
		$response->store_clearing_info( $order );
		$this->add_email_meta_hook( [ $this, 'email_meta_action' ] );
		$order->update_meta_data( '_authorization_method', $transaction->get( 'request' ) );
		$order->update_status( 'on-hold', __( 'Invoice has been sent', 'payone-woocommerce-3' ) );

		// Reduce stock levels
		wc_reduce_stock_levels( $order_id );

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Transaction\Invoice
	 */
	public function wcs_get_transaction_for_subscription_signup( \WC_Order $order ) {
		$transaction = new \Payone\Transaction\Invoice( $this );

		$transaction->set_reference( $order );
		$transaction->set( 'recurrence', 'recurring' );
		$transaction->set( 'customer_is_present', 'yes' );

		return $transaction;
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
}
