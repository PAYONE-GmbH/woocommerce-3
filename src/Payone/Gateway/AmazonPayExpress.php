<?php

namespace Payone\Gateway;

use Payone\Plugin;

class AmazonPayExpress extends AmazonPayBase {

	const GATEWAY_ID = 'payone_amazonpay_express';
	const PUBLIC_KEY_ID = 'AE5E5B7B2SAERURYEH6DKDAZ';
	const SESSION_KEY_AMAZONPAY_EXPRESS_USED = 'payone_amazonpay_express_used';
	const SESSION_KEY_AMAZONPAY_SESSION_ID = 'payone_amazonpay_express_session_id';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-amazon-pay.png';
		$this->method_title       = 'PAYONE ' . __( 'Amazon Pay Express', 'payone-woocommerce-3' );
		$this->method_description = '';

		$this->pay_button_id = 'payone-amazonpay-express-button';
		$this->supports[]    = 'pay_button';
		$this->supports[]    = 'blocks';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && ! is_cart() ) {
			$is_available = Plugin::get_session_value( self::SESSION_KEY_WORKORDERID ) !== null
			  && Plugin::get_session_value( self::SESSION_KEY_AMAZONPAY_EXPRESS_USED ) === true;
		}

		return $is_available;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Amazon Pay Express', 'payone-woocommerce-3' ) );
		$this->add_amazon_merchant_id_field();
		$this->add_button_color_field();
		$this->add_allow_packstations_field();
		$this->add_allow_po_box_field();
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/amazonpay/express-payment-form.php';
	}

	public function process_create_checkout_session( \WC_Cart $cart) {
		$transaction = new \Payone\Transaction\AmazonPayExpressCreateCheckoutSession( $this );

		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-back' ] ) );

		$response = $transaction->execute( WC()->cart );
		$workorderid = $response->get( 'workorderid' );
		Plugin::set_session_value( self::SESSION_KEY_WORKORDERID, $workorderid );
		Plugin::delete_session_value( AmazonPayExpress::SESSION_KEY_AMAZONPAY_EXPRESS_USED );

		return [
			'workorderId' => $workorderid,
			'sandbox' => $this->get_mode() === 'test',
			'merchantId' => $this->get_amazon_merchant_id(),
			'publicKeyId' => self::PUBLIC_KEY_ID,
			'ledgerCurrency' => get_woocommerce_currency(),
			'checkoutLanguage' => get_locale(),
			'productType' => 'PayAndShip',
			'placement' => 'Cart',
			'buttonColor' => $this->get_button_color(),
			'estimatedOrderAmount' => [
				'amount' => $cart->get_total( 'number' ),
				'currencyCode' => get_woocommerce_currency(),
			],
			'createCheckoutSessionConfig' => [
				'payloadJSON' => $response->get( 'add_paydata[payload]' ),
				'signature' => $response->get( 'add_paydata[signature]' ),
			],
		];
	}

	public function process_get_checkout( $workorderid ) {
		$transaction = ( new \Payone\Transaction\AmazonPayExpressGetCheckoutSession( $this ) )
			->set( 'workorderid', $workorderid )
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back', 'a' => 'express-back' ] ) );

		$response = $transaction->execute( WC()->cart );

		WC()->customer->set_billing_first_name( $response->get( 'add_paydata[billing_firstname]' ) );
		WC()->customer->set_billing_last_name( $response->get( 'add_paydata[billing_lastname]' ) );
		WC()->customer->set_billing_company( '' );
		WC()->customer->set_billing_address_1( $response->get( 'add_paydata[billing_street]' ) );
		WC()->customer->set_billing_address_2( '' );
		WC()->customer->set_billing_city( $response->get( 'add_paydata[billing_city]' ) );
		WC()->customer->set_billing_state( '' );
		WC()->customer->set_billing_postcode( $response->get( 'add_paydata[billing_zip]' ) );
		WC()->customer->set_billing_country( $response->get( 'add_paydata[billing_country]' ) );
		WC()->customer->set_billing_phone( $response->get( 'add_paydata[billing_telephonenumber]' ) );
		WC()->customer->set_billing_email( $response->get( 'add_paydata[email]' ) );

		WC()->customer->set_shipping_first_name( $response->get( 'add_paydata[shipping_firstname]' ) );
		WC()->customer->set_shipping_last_name( $response->get( 'add_paydata[shipping_lastname]' ) );
		WC()->customer->set_shipping_company( '' );

		WC()->customer->set_shipping_address_1( $response->get( 'add_paydata[shipping_street]' ) );
		WC()->customer->set_shipping_address_2( '' );
		WC()->customer->set_shipping_city( $response->get( 'add_paydata[shipping_city]' ) );
		WC()->customer->set_shipping_state( '' );
		WC()->customer->set_shipping_postcode( $response->get( 'add_paydata[shipping_zip]' ) );
		WC()->customer->set_shipping_country( $response->get( 'add_paydata[shipping_country]' ) );
		WC()->customer->set_shipping_phone( $response->get( 'add_paydata[shipping_telephonenumber]' ) );
		WC()->customer->save();

		Plugin::set_session_value( self::SESSION_KEY_SELECT_GATEWAY, self::GATEWAY_ID );
		Plugin::set_session_value( self::SESSION_KEY_AMAZONPAY_EXPRESS_USED, true );
		Plugin::set_session_value( self::SESSION_KEY_AMAZONPAY_SESSION_ID, $response->get( 'add_paydata[amazonCheckoutSessionId]' ) );

		wp_redirect( wc_get_checkout_url() );
		exit;
	}

	/**
	 * Process Blocks Express create session request.
	 * This is called via AJAX from the Express Block frontend.
	 *
	 * @return array Button configuration for Amazon Pay SDK
	 */
	public function process_blocks_express_create_session() {
		$cart = WC()->cart;
		if ( ! $cart ) {
			return [
				'error' => __( 'Cart not found', 'payone-woocommerce-3' ),
			];
		}

		// Create checkout session
		$transaction = new \Payone\Transaction\AmazonPayExpressCreateCheckoutSession( $this );
		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'express-back' ] ) );

		$response = $transaction->execute( $cart );

		// Validate response data
		$workorderid = $response->get( 'workorderid' );
		$payload = $response->get( 'add_paydata[payload]' );
		$signature = $response->get( 'add_paydata[signature]' );

		if ( ! $payload || ! $signature ) {
			error_log( 'PAYONE AmazonPay Express: Missing payload or signature. Response: ' . print_r( $response->toArray(), true ) );
			return [
				'error' => __( 'Payment configuration error. Please check your AmazonPay settings or try another payment method.', 'payone-woocommerce-3' ),
			];
		}

		Plugin::set_session_value( self::SESSION_KEY_WORKORDERID, $workorderid );
		Plugin::delete_session_value( self::SESSION_KEY_AMAZONPAY_EXPRESS_USED );

		return [
			'workorderId' => $workorderid,
			'sandbox' => $this->get_mode() === 'test',
			'merchantId' => $this->get_amazon_merchant_id(),
			'publicKeyId' => self::PUBLIC_KEY_ID,
			'ledgerCurrency' => get_woocommerce_currency(),
			'checkoutLanguage' => get_locale(),
			'productType' => 'PayAndShip',
			'placement' => 'Cart',
			'buttonColor' => $this->get_button_color(),
			'estimatedOrderAmount' => [
				'amount' => $cart->get_total( 'number' ),
				'currencyCode' => get_woocommerce_currency(),
			],
			'createCheckoutSessionConfig' => [
				'payloadJSON' => $payload,
				'signature' => $signature,
			],
		];
	}
}
