<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class Invoice extends GatewayBase {

	const GATEWAY_ID = 'bs_payone_invoice';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon                       = PAYONE_PLUGIN_URL . 'assets/icon-rechnungskauf.png';
		$this->method_title               = 'PAYONE ' . __( 'Invoice', 'payone-woocommerce-3' );
		$this->method_description         = '';
		$this->test_transaction_classname = \Payone\Transaction\Invoice::class;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Invoice', 'payone-woocommerce-3' ) );
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/invoice/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\Invoice( $this );

		$response = $transaction->execute( $order );

		if ( $response->has_error() ) {
			$order->update_status( 'failed', $this->get_error_message( $response ) );
			wc_add_notice( __( 'Payment failed.', 'payone-woocommerce-3' ) , 'error' );

			return null;
		}

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
