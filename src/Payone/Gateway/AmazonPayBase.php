<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

/**
 * Amazon Pay and Amazon Pay Express share some functions. In order to not repeat the code, those functions are bundled
 * in this base class.
 */
class AmazonPayBase extends RedirectGatewayBase {

	const SESSION_KEY_WORKORDERID = 'payone_amazonpay_workorderid';
	const SESSION_KEY_ORDER_ID = 'payone_amazonpay_orderid';

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\AmazonPayExpress::class );
	}

	protected function after_payment_successful() {
		parent::after_payment_successful();

		Plugin::delete_session_value( self::SESSION_KEY_WORKORDERID );
		Plugin::delete_session_value( AmazonPayExpress::SESSION_KEY_AMAZONPAY_EXPRESS_USED );
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

	public function get_restrictions() {
		$restrictions = [];
		if ( ! $this->get_allow_packstations() ) {
			$restrictions[] = 'RestrictPackstations';
		}
		if ( ! $this->get_allow_po_box() ) {
			$restrictions[] = 'RestrictPOBoxes';
		}

		return implode( ',', $restrictions );
	}

	/**
	 * Get setting from this gateway, fallback to parent AmazonPay gateway if empty
	 * and this is the Express gateway.
	 *
	 * @param string $key Setting key to retrieve
	 * @param mixed $default Default value if not found
	 * @return mixed Setting value
	 */
	protected function get_amazon_setting( $key, $default = '' ) {
		$value = isset( $this->settings[$key] ) ? $this->settings[$key] : $default;

		// If empty and this is Express gateway, try parent AmazonPay gateway
		if ( empty( $value ) && $this instanceof AmazonPayExpress ) {
			$parent_settings = get_option( 'woocommerce_payone_amazonpay_settings', [] );
			$value = isset( $parent_settings[$key] ) ? $parent_settings[$key] : $default;
		}

		return $value;
	}

	public function get_amazon_merchant_id() {
		return $this->get_amazon_setting( 'amazon_merchant_id', '' );
	}

	protected function add_amazon_merchant_id_field() {
		$this->form_fields['amazon_merchant_id'] = [
			'title'   => __( 'Amazon Merchant ID', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => false,
		];
	}

	protected function get_allow_packstations() {
		$value = $this->get_amazon_setting( 'amazon_allow_packstations', '1' );
		return '1' === $value;
	}

	protected function add_allow_packstations_field() {
		$this->form_fields['amazon_allow_packstations'] = [
			'title'   => __( 'Allow Packstations', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => [
				'0' => __( 'No', 'payone-woocommerce-3' ),
				'1' => __( 'Yes', 'payone-woocommerce-3' ),
			],
			'default' => '1',
		];
	}

	protected function get_allow_po_box() {
		$value = $this->get_amazon_setting( 'amazon_allow_po_box', '1' );
		return '1' === $value;
	}

	protected function add_allow_po_box_field() {
		$this->form_fields['amazon_allow_po_box'] = [
			'title'   => __( 'Allow P.O. Boxes', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => [
				'0' => __( 'No', 'payone-woocommerce-3' ),
				'1' => __( 'Yes', 'payone-woocommerce-3' ),
			],
			'default' => '1',
		];
	}

	public function get_button_color() {
		return $this->get_amazon_setting( 'amazon_button_color', 'Gold' );
	}

	protected function add_button_color_field() {
		$this->form_fields['amazon_button_color'] = [
			'title'   => __( 'Button Color', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => [
				'Gold' => __( 'Gold', 'payone-woocommerce-3' ),
				'LightGray' => __( 'Light Gray', 'payone-woocommerce-3' ),
				'DarkGray' => __( 'DarkGray', 'payone-woocommerce-3' ),
			],
			'default' => 'Gold',
		];
	}
}
