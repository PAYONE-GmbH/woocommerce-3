<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class Wero extends RedirectGatewayBase {
	const GATEWAY_ID = 'payone_wero';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-wero.png';
		$this->method_title       = 'PAYONE ' . __( 'Wero', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->supported_countries = [
			'DE' => __( 'Germany', 'woocommerce' ),
			'BE' => __( 'Belgium', 'woocommerce' ),
		];
		$this->init_common_form_fields( 'PAYONE ' . __( 'Wero', 'payone-woocommerce-3' ) );
		$this->form_fields['countries']['default'] = [ 'DE', 'BE' ];
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/wero/payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\Wero::class );
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
		if ( $from_status === 'on-hold' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}
}
