<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Capture;

class PrePayment extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_prepayment';

	public function __construct() {
		parent::__construct(self::GATEWAY_ID);

		$this->icon               = '';
		$this->method_title       = 'BS PAYONE Vorkasse';
		$this->method_description = 'method_description';
		$this->supports           = [ 'products', 'refunds' ];
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Prepayment', 'payone' ) );
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/pre-payment/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\PrePayment( $this );
		$response    = $transaction->execute( $order );

		// @todo Fehler abfangen und transaktions-ID in Order ablegen.

		$order->set_transaction_id( $response->get( 'txid' ) );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status( 'on-hold', __( 'Waiting for payment.', 'payone' ) );

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
	 * @param \WC_Order $order
	 */
	public function process_transaction_status( TransactionStatus $transaction_status, \WC_Order $order ) {
		if ($transaction_status->isOverpaid()) {
			$order->update_status( 'wc-processing', __( 'Payment received. Customer overpaid!', 'payone' ) );
		} elseif ($transaction_status->isUnderpaid()) {
			$order->add_order_note(__( 'Payment received. Customer underpaid!', 'payone' ));
		} elseif ($transaction_status->isPaid()) {
			$order->update_status( 'wc-processing', __( 'Payment received.', 'payone' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		if ( $from_status === 'on-hold' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}

	/**
	 * @param \WC_Order $order
	 */
	public function capture( \WC_Order $order ) {
		$capture = new Capture( $this );
		$capture->execute( $order );
	}
}