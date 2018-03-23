<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class Invoice extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_invoice';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'BS PAYONE Rechnung';
		$this->method_description = 'method_description';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Invoice', 'payone-woocommerce-3' ) );
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/invoice/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\Invoice( $this );
		$response    = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(),
				'error' );

			return;
		}
		// @todo Bei Kauf auf Rechnung anderer Status und Order abschließen?

		$order->set_transaction_id( $response->get( 'txid' ) );
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
	}
}