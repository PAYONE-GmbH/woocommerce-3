<?php

namespace Payone\Gateway;

class SecuredDirectDebit extends PaylaBase {

	const GATEWAY_ID = 'payone_secured_direct_debit';

	protected $min_amount_validation = 10;
	protected $max_amount_validation = 1500;

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-secured-lastschrift.png';
		$this->method_title       = 'PAYONE ' . __( 'Secured Direct Debit', 'payone-woocommerce-3' );
		$this->method_description = '';
		$this->hide_when_b2b      = true;
	}

	public function init_form_fields() {
		$this->supported_countries = [
			'DE' => __( 'Germany', 'woocommerce' ),
			'AT' => __( 'Austria', 'woocommerce' ),
		];
		$this->init_common_form_fields( 'PAYONE ' . __( 'Secured Direct Debit', 'payone-woocommerce-3' ) );
		$this->form_fields['countries']['default'] = [ 'DE', 'AT' ];
		$this->add_allow_different_shipping_address_field();
		$this->form_fields['allow_different_shopping_address']['description'] = __( 'Attention: has to be enabled in the PAYONE account', 'payone-woocommerce-3' );
	}

	public function payment_fields() {
		$environment = $this->get_mode() === 'live' ? 'p' : 't';
		$snippet_token = self::PAYLA_PARTNER_ID . '_' . $this->get_merchant_id() . '_' . md5(uniqid('payone_secured_invoice', true));

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/payla/secured-direct-debit-payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\SecuredDirectDebit( $this );

		$response = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( $this->get_error_message( $response ), 'error' );

			$this->payla_request_failed();

			return;
		}

		$order->set_transaction_id( $response->get( 'txid' ) );
		$order->add_meta_data( '_payone_userid', $response->get( 'userid', '' ) );

		$authorization_method = $transaction->get( 'request' );
		$order->update_meta_data( '_authorization_method', $authorization_method );
		$order->save_meta_data();
		$order->save();

		if ( $authorization_method === 'preauthorization' ) {
			$order->update_status( 'on-hold', __( 'Waiting for payment.', 'payone-woocommerce-3' ) );
		} elseif ( $authorization_method === 'authorization' ) {
			$order->update_status( 'processing',
				__( 'Payment is authorized and captured.', 'payone-woocommerce-3' ) );
		}

		wc_reduce_stock_levels( $order_id );
		$woocommerce->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
