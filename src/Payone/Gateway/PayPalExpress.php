<?php

namespace Payone\Gateway;

use Payone\Plugin;

class PayPalExpress extends PayPalBase {

	const GATEWAY_ID = 'payone_paypal_express';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-paypal.png';
		$this->method_title       = 'PAYONE ' . __( 'PayPal Express', 'payone-woocommerce-3' );
		$this->method_description = '';

		$this->pay_button_id = 'payone-paypal-express-button';
		$this->supports[]    = 'pay_button';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && ! is_cart() ) {
			$is_available = get_transient( self::TRANSIENT_KEY_WORKORDERID ) !== false;
		}

		return $is_available;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'PayPal Express', 'payone-woocommerce-3' ) );
	}

	public function process_set_checkout() {
		$transaction = new \Payone\Transaction\PayPalExpressSetCheckout( $this );

		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'paypal-express-get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back' ] ) );

		$response = $transaction->execute( WC()->cart );

		if ( $response->get( 'status' ) === 'REDIRECT' ) {
			$result = [
				'status'      => 'ok',
				'token'       => $response->get( 'add_paydata[token]' ),
				'workorderid' => $response->get( 'workorderid' ),
				'url'         => $response->get( 'redirecturl' ),
			];
			set_transient( self::TRANSIENT_KEY_WORKORDERID, $result['workorderid'], 60 * 10 );
		} else {
			$result = [
				'status'  => 'error',
				'code'    => $response->get( 'errorcode' ),
				'message' => $response->get( 'customermessage' ),
			];
		}

		echo json_encode( $result );
		exit;
	}

	public function process_get_checkout( $workorderid ) {
		$transaction = ( new \Payone\Transaction\PayPalExpressGetCheckout( $this ) )
			->set( 'workorderid', $workorderid )
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'paypal-express-get-checkout-callback' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back' ] ) );

		$response = $transaction->execute( WC()->cart );

		$name      = $response->get( 'add_paydata[lastname]' );
		$nameParts = explode( ' ', $name );
		if ( count( $nameParts ) > 1 ) {
			$firstname = array_shift( $nameParts );
			$lastname  = implode( ' ', $nameParts );
		} else {
			$firstname = '';
			$lastname  = $name;
		}

		WC()->customer->set_billing_first_name( $firstname );
		WC()->customer->set_billing_last_name( $lastname );
		WC()->customer->set_billing_company( '' );
		WC()->customer->set_billing_address_1( $response->get( 'add_paydata[street]' ) );
		WC()->customer->set_billing_address_2( '' );
		WC()->customer->set_billing_city( $response->get( 'add_paydata[city]' ) );
		WC()->customer->set_billing_state( '' );
		WC()->customer->set_billing_postcode( $response->get( 'add_paydata[zip]' ) );
		WC()->customer->set_billing_country( $response->get( 'add_paydata[countrycode]' ) );
		WC()->customer->set_billing_phone( $response->get( 'add_paydata[telephonenumber]' ) );
		WC()->customer->set_billing_email( $response->get( 'add_paydata[email]' ) );

		WC()->customer->set_shipping_first_name( $response->get( 'add_paydata[shipping_firstname]' ) );
		WC()->customer->set_shipping_last_name( $response->get( 'add_paydata[shipping_lastname]' ) );
		WC()->customer->set_shipping_company( $response->get( 'add_paydata[shipping_company]' ) );

		WC()->customer->set_shipping_address_1( $response->get( 'add_paydata[shipping_street]' ) );
		WC()->customer->set_shipping_address_2( $response->get( 'add_paydata[shipping_addressaddition]' ) );
		WC()->customer->set_shipping_city( $response->get( 'add_paydata[shipping_city]' ) );
		WC()->customer->set_shipping_state( $response->get( 'add_paydata[shipping_state]' ) );
		WC()->customer->set_shipping_postcode( $response->get( 'add_paydata[shipping_zip]' ) );
		WC()->customer->set_shipping_country( $response->get( 'add_paydata[shipping_country]' ) );
		WC()->customer->set_shipping_phone( $response->get( 'add_paydata[telephonenumber]' ) );

		set_transient( self::TRANSIENT_KEY_SELECT_GATEWAY, self::GATEWAY_ID );

		wp_redirect( wc_get_checkout_url() );
		exit;
	}
}
