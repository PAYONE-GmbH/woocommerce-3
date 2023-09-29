<?php

namespace Payone\Gateway;

class SecuredInvoice extends PaylaBase {

	const GATEWAY_ID = 'payone_secured_invoice';

	protected $min_amount_validation = 10;
	protected $max_amount_validation = 1500;

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-rechnungskauf.png';
		$this->method_title       = 'PAYONE ' . __( 'Secured Invoice', 'payone-woocommerce-3' );
		$this->method_description = '';
		$this->hide_when_b2b      = false;
	}

	public function init_form_fields() {
		$this->supported_countries = [
			'DE' => __( 'Germany', 'woocommerce' ),
			'AT' => __( 'Austria', 'woocommerce' ),
		];
		$this->init_common_form_fields( 'PAYONE ' . __( 'Secured Invoice', 'payone-woocommerce-3' ) );
		$this->form_fields['countries']['default'] = [ 'DE', 'AT' ];
		$this->add_allow_different_shipping_address_field();
		$this->form_fields['allow_different_shopping_address']['description'] = __( 'Attention: has to be enabled in the PAYONE account', 'payone-woocommerce-3' );
	}

	public function payment_fields() {
		$environment = $this->get_mode() === 'live' ? 'p' : 't';
		$snippet_token = self::PAYLA_PARTNER_ID . '_' . $this->get_merchant_id() . '_' . md5(uniqid('payone_secured_invoice', true));

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/payla/secured-invoice-payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\SecuredInvoice( $this );

		$response = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( $this->get_error_message( $response ), 'error' );

			$this->payla_request_failed();

			return;
		}

		$order->set_transaction_id( $response->get( 'txid' ) );
		$order->add_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
		$response->store_clearing_info( $order );
		$this->add_email_meta_hook( [ $this, 'email_meta_action' ] );
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
}
