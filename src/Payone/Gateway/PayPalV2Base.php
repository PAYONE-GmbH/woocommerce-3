<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

/**
 * PayPal V2 and PayPal V2 Express share some functions. In order to not repeat the code, those functions are bundled
 * in this base class.
 */
class PayPalV2Base extends RedirectGatewayBase {

	const SESSION_KEY_WORKORDERID = 'payone_paypalv2_workorderid';
	const SESSION_KEY_ORDER_ID = 'payone_paypalv2_orderid';

	const PAYONE_CLIENT_ID_TEST = 'AUn5n-4qxBUkdzQBv6f8yd8F4AWdEvV6nLzbAifDILhKGCjOS62qQLiKbUbpIKH_O2Z3OL8CvX7ucZfh';
	const PAYONE_CLIENT_ID_LIVE = 'AVNBj3ypjSFZ8jE7shhaY2mVydsWsSrjmHk0qJxmgJoWgHESqyoG35jLOhH3GzgEPHmw7dMFnspH6vim';
	const PAYONE_MERCHANT_ID_TEST = '3QK84QGGJE5HW';

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\PayPalV2Express::class );
	}

	protected function after_payment_successful() {
		parent::after_payment_successful();

		Plugin::delete_session_value( self::SESSION_KEY_WORKORDERID );
		Plugin::delete_session_value( PayPalV2Express::SESSION_KEY_PAYPALV2_EXPRESS_USED );
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

	public function get_payone_client_id() {
		if ( $this->get_mode() === 'test' ) {
			return self::PAYONE_CLIENT_ID_TEST;
		}

		return self::PAYONE_CLIENT_ID_LIVE;
	}

	public function get_payone_merchant_id() {
		if ( $this->get_mode() === 'test' ) {
			return self::PAYONE_MERCHANT_ID_TEST;
		}

		return self::PAYONE_CLIENT_ID_LIVE;
	}

	protected function get_paypal_merchant_id() {
		return isset( $this->settings[ 'paypal_merchant_id' ] ) ? $this->settings[ 'paypal_merchant_id' ] : '';
	}

	protected function add_paypal_merchant_id_field() {
		$this->form_fields['paypal_merchant_id'] = [
			'title'   => __( 'PayPal Merchant ID', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => false,
		];
	}
}
