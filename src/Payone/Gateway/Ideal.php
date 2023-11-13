<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class Ideal extends RedirectGatewayBase {
	const GATEWAY_ID = 'payone_ideal';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = 'https://cdn.pay1.de/clearingtypes/sb/idl/default.svg';
		$this->method_title       = 'PAYONE ' . __( 'iDEAL', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'iDEAL', 'payone-woocommerce-3' ) );
		$this->form_fields['countries']['default'] = [ 'NL' ];
	}

	public function payment_fields() {
		$bankgroups = [
			'ABNANL2A' => 'ABN Amro',
			'ASNBNL21' => 'ASN Bank',
			'BUNQNL2A' => 'Bunq',
			'INGBNL2A' => 'ING Bank',
			'KNABNL2H' => 'Knab',
			'NTSBDEB1' => 'N26',
			'NNBANL2G' => 'Nationale-Nederlanden',
			'RABONL2U' => 'Rabobank',
			'RBRBNL21' => 'RegioBank',
			'REVOLT21' => 'Revolut',
			'SNSBNL2A' => 'SNS',
			'TRIONL2U' => 'Triodos Bank',
			'FVLBNL22' => 'van Lanschot',
			'BITSNL2A' => 'Yoursafe',
		];

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/ideal/payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\Ideal::class );
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->no_further_action_necessary() ) {
			return;
		}

		$order                = $transaction_status->get_order();
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'authorization' && $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment is authorized by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_capture() ) {
			$order->add_order_note( __( 'Payment is captured by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
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
			$this->capture( $order );
		}
	}
}
