<?php

namespace Payone\Gateway;

use Payone\Plugin;

class AmazonPay extends AmazonPayBase {

	const GATEWAY_ID = 'payone_amazonpay';
	const PUBLIC_KEY_ID = 'AE5E5B7B2SAERURYEH6DKDAZ';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-amazon-pay.png';
		$this->method_title       = 'PAYONE ' . __( 'Amazon Pay', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available ) {
			$is_available = Plugin::get_session_value( AmazonPayBase::SESSION_KEY_WORKORDERID ) === null
			  || Plugin::get_session_value( AmazonPayExpress::SESSION_KEY_AMAZONPAY_EXPRESS_USED ) === null;
		}

		return $is_available;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Amazon Pay', 'payone-woocommerce-3' ) );
		$this->add_amazon_merchant_id_field();
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/amazonpay/payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		Plugin::set_session_value( self::SESSION_KEY_ORDER_ID, $order_id );

		return [
			'result'   => 'success',
			'redirect' => Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'button' ] ),
		];
	}

	public function process_button( $order_id ) {
		$order = wc_get_order( $order_id );
		$button_config = $this->create_button_config( $order );

		include PAYONE_VIEW_PATH . '/gateway/amazonpay/button-payment-form.php';
		exit;
	}

	public function process_success( $order_id ) {
		$order = wc_get_order( $order_id );
		$this->handle_successfull_payment( $order );

		$target_url = $this->get_return_url( $order );

		wp_redirect( $target_url );
		exit;
	}

	public function create_button_config( \WC_Order $order) {
		$transaction = new \Payone\Transaction\AmazonPay( $this );
		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'back' ] ) );

		$response = $transaction->execute( $order );
		Plugin::set_session_value( self::SESSION_KEY_WORKORDERID, $response->get( 'workorderid' ) );
		Plugin::delete_session_value( AmazonPayExpress::SESSION_KEY_AMAZONPAY_EXPRESS_USED );

		$order->update_meta_data( '_authorization_method', $transaction->get( 'request' ) );
		$order->set_transaction_id( $response->get( 'txid' ) );
		$order->save();

		return [
			'sandbox' => $this->get_mode() === 'test',
			'merchantId' => $this->get_amazon_merchant_id(),
			'publicKeyId' => self::PUBLIC_KEY_ID,
			'ledgerCurrency' => get_woocommerce_currency(),
			'checkoutLanguage' => get_locale(),
			'productType' => 'PayAndShip',
			'placement' => 'Cart',
			'buttonColor' => $this->get_button_color(),
			'estimatedOrderAmount' => [
				'amount' => $order->get_total( 'number' ),
				'currencyCode' => get_woocommerce_currency(),
			],
			'createCheckoutSessionConfig' => [
				'payloadJSON' => $response->get( 'add_paydata[payload]' ),
				'signature' => $response->get( 'add_paydata[signature]' ),
			],
		];
	}
}
