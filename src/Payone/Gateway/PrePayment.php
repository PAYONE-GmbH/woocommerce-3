<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class PrePayment extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_prepayment';

	public function __construct() {
		parent::__construct(self::GATEWAY_ID);

		$this->icon               = '';
		$this->method_title       = 'BS PAYONE Vorkasse';
		$this->method_description = 'method_description';
		$this->supports           = [ 'products', 'refunds' ];

		$this->add_email_meta_hook([$this, 'email_meta_action']);
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
		$clearing_info = [
			'bankaccount'       => $response->get( 'clearing_bankaccount' ),
			'bankcode'          => $response->get( 'clearing_bankcode' ),
			'bankcountry'       => $response->get( 'clearing_bankcountry' ),
			'bankname'          => $response->get( 'clearing_bankname' ),
			'bankaccountholder' => $response->get( 'clearing_bankaccountholder' ),
			'bankcity'          => $response->get( 'clearing_bankcity' ),
			'bankiban'          => $response->get( 'clearing_bankiban' ),
			'bankbic'           => $response->get( 'clearing_bankbic' ),
		];
		$order->update_meta_data( '_clearing_info', json_encode( $clearing_info ) );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status( 'on-hold', __( 'Waiting for payment.', 'payone-woocommerce-3' ) );

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
		if ($transaction_status->is_overpaid()) {
			$order->add_order_note( __( 'Payment received. Customer overpaid!', 'payone-woocommerce-3' ) );
		} elseif ($transaction_status->is_underpaid()) {
			$order->add_order_note(__( 'Payment received. Customer underpaid!', 'payone-woocommerce-3' ));
		} elseif ($transaction_status->is_paid()) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		if ( $from_status === 'on-hold' && $to_status === 'processing' ) {
			// @todo Reagieren, wenn Capture fehlschlägt?
			$this->capture( $order );
		}
	}

	/**
	 * @param \WC_Order $order
	 * @param bool $sent_to_admin
	 * @param string $plain_text
	 * @param string $email
	 */
	public function email_meta_action(\WC_Order $order, $sent_to_admin, $plain_text, $email = '') {
		$clearing_info = json_decode($order->get_meta('clearing_info'), true);
		echo print_r($clearing_info, 1);
	}
}