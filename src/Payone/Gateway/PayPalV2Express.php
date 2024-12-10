<?php

namespace Payone\Gateway;

use Payone\Plugin;

class PayPalV2Express extends PayPalV2Base {

	const GATEWAY_ID = 'payone_paypalv2_express';
	const PUBLIC_KEY_ID = 'AE5E5B7B2SAERURYEH6DKDAZ';
	const SESSION_KEY_PAYPALV2_EXPRESS_USED = 'payone_paypalv2_express_used';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-paypal.png';
		$this->method_title       = 'PAYONE ' . __( 'PayPal V2 Express', 'payone-woocommerce-3' );
		$this->method_description = '';

		$this->pay_button_id = 'payone-paypalv2-express-button';
		$this->supports[]    = 'pay_button';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && ! is_cart() ) {
			$is_available = Plugin::get_session_value( self::SESSION_KEY_WORKORDERID ) !== null
			  && Plugin::get_session_value( self::SESSION_KEY_PAYPALV2_EXPRESS_USED ) === true;
		}

		return $is_available;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'PayPal V2 Express', 'payone-woocommerce-3' ) );
		$this->add_paypal_merchant_id_field();
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/paypalv2/express-payment-form.php';
	}

	public function process_set_checkout_session() {
		$cart = WC()->cart;
		$transaction = new \Payone\Transaction\PayPalV2ExpressSetCheckoutSession( $this );

		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'express-get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'express-error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'express-back' ] ) );

		$response = $transaction->execute( WC()->cart );
		Plugin::set_session_value( self::SESSION_KEY_WORKORDERID, $response->get( 'workorderid' ) );
		Plugin::delete_session_value( PayPalV2Express::SESSION_KEY_PAYPALV2_EXPRESS_USED );

		echo $response->get( 'add_paydata[orderId]' );
		exit;
	}

	public function process_get_checkout( $workorderid ) {
		$transaction = ( new \Payone\Transaction\PayPalV2ExpressGetCheckoutDetails( $this ) )
			->set( 'workorderid', $workorderid )
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'express-get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'express-error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back', 'a' => 'express-back' ] ) );

		$response = $transaction->execute( WC()->cart );

		WC()->customer->set_billing_first_name( $response->get( 'add_paydata[firstname]' ) );
		WC()->customer->set_billing_last_name( $response->get( 'add_paydata[lastname]' ) );
		WC()->customer->set_billing_company( '' );
		WC()->customer->set_billing_address_1( $response->get( 'add_paydata[shipping_street]' ) );
		WC()->customer->set_billing_address_2( '' );
		WC()->customer->set_billing_city( $response->get( 'add_paydata[shipping_city]' ) );
		WC()->customer->set_billing_state( '' );
		WC()->customer->set_billing_postcode( $response->get( 'add_paydata[shipping_zip]' ) );
		WC()->customer->set_billing_country( $response->get( 'add_paydata[country]' ) );
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
		WC()->customer->save();

		Plugin::set_session_value( self::SESSION_KEY_SELECT_GATEWAY, self::GATEWAY_ID );
		Plugin::set_session_value( self::SESSION_KEY_PAYPALV2_EXPRESS_USED, true );

		wp_redirect( wc_get_checkout_url() );
		exit;
	}
}
