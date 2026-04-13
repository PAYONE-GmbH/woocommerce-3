<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class GooglePay extends RedirectGatewayBase {
	const GATEWAY_ID = 'payone_googlepay';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-googlepay.png';
		$this->method_title       = 'PAYONE ' . __( 'Google Pay', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Google Pay', 'payone-woocommerce-3' ) );
		$this->add_googlepay_merchant_info_fields();
		$this->form_fields['countries']['default'] = [ 'DE', 'AT' ];
	}

	public function payment_fields() {
		$environment = $this->get_mode() === 'test' ? 'TEST' : 'PRODUCTION';
		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/googlepay/payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\GooglePay::class );
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

	public function get_googlepay_merchant_id() {
		$default = '';
		if ( $this->get_mode() === 'test' ) {
			$default = $this->get_merchant_id();
		}

		$value = isset( $this->settings[ 'googlepay_merchant_id' ] ) ? $this->settings[ 'googlepay_merchant_id' ] : '';
		if ( ! $value ) {
			$value = $default;
		}

		return $value;
	}

	public function get_googlepay_merchant_name() {
		$default = '';
		if ( $this->get_mode() === 'test' ) {
			$default = 'payonegmbh';
		}

		$value = isset( $this->settings[ 'googlepay_merchant_name' ] ) ? $this->settings[ 'googlepay_merchant_name' ] : '';
		if ( ! $value ) {
			$value = $default;
		}

		return $value;
	}

	protected function add_googlepay_merchant_info_fields() {
		$this->form_fields['googlepay_merchant_id'] = [
			'title'   => __( 'Google Pay Merchant ID', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => false,
		];
		$this->form_fields['googlepay_merchant_name'] = [
			'title'   => __( 'Google Pay Merchant Name', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => false,
		];
	}
}
