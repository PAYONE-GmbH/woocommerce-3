<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class PrePayment extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_prepayment';

	public function __construct() {
		parent::__construct(self::GATEWAY_ID);

		$this->icon               = '';
		$this->method_title       = 'Payone ' . __( 'Prepayment', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Prepayment', 'payone-woocommerce-3' ) );
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

		// @todo Fehler abfangen

		$order->set_transaction_id( $response->get( 'txid' ) );
		$response->store_clearing_info( $order );
		$this->add_email_meta_hook( [ $this, 'email_meta_action' ] );

		$order->update_status( 'on-hold', __( 'Waiting for payment.', 'payone-woocommerce-3' ) );

		wc_reduce_stock_levels( $order_id );

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
			$order->add_order_note(__( 'Payment received. Customer underpaid!', 'payone-woocommerce-3' ));
		} elseif ( $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		if ( $from_status === 'on-hold' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}
}