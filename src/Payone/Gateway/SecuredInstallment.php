<?php

namespace Payone\Gateway;

class SecuredInstallment extends PaylaBase {

	const GATEWAY_ID = 'payone_secured_installment';

	protected $min_amount_validation = 200;
	protected $max_amount_validation = 3500;

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-rechnungskauf.png';
		$this->method_title       = 'PAYONE ' . __( 'Secured Installment', 'payone-woocommerce-3' );
		$this->method_description = '';
		$this->hide_when_b2b      = true;
	}

	public function init_form_fields() {
		$this->supported_countries = [
			'DE' => __( 'Germany', 'woocommerce' ),
			'AT' => __( 'Austria', 'woocommerce' ),
		];
		$this->init_common_form_fields( 'PAYONE ' . __( 'Secured Installment', 'payone-woocommerce-3' ) );
		$this->form_fields['countries']['default'] = [ 'DE', 'AT' ];
		$this->add_allow_different_shipping_address_field();
		$this->form_fields['allow_different_shopping_address']['description'] = __( 'Attention: has to be enabled in the PAYONE account', 'payone-woocommerce-3' );
	}

	public function get_snippet_token() {
		return self::PAYLA_PARTNER_ID . '_' . $this->get_merchant_id() . '_' . md5( uniqid( 'payone_secured_installment', true ) );
	}

	public function payment_fields() {
		$environment   = $this->get_environment();
		$snippet_token = $this->get_snippet_token();

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/payla/secured-installment-payment-form.php';
	}

	public function process_secured_installment_options() {
		$transaction = new \Payone\Transaction\SecuredInstallmentOptions( $this );
		$response    = $transaction->execute( WC()->cart );
		if ( $response->get( 'status' ) === 'OK' ) {
			$result = [];
			$i      = 0;
			while ( $response->get( 'add_paydata[installment_option_id_' . $i . ']' ) ) {
				$monthly_amount = round( $response->get( 'add_paydata[monthly_amount_value_' . $i . ']' ) / 100, 2 );
				$result[]       = [
					'workorderid'             => $response->get( 'workorderid' ),
					'option_id'               => $response->get( 'add_paydata[installment_option_id_' . $i . ']' ),
					'number_of_payments'      => $response->get( 'add_paydata[number_of_payments_' . $i . ']' ),
					'monthly_amount'          => number_format_i18n( $monthly_amount, 2 ) . ' €',
					'nominal_interest_rate'   => number_format_i18n( $response->get( 'add_paydata[nominal_interest_rate_' . $i . ']' ) / 100, 2 ) . '&nbsp;%',
					'effective_interest_rate' => number_format_i18n( $response->get( 'add_paydata[effective_interest_rate_' . $i . ']' ) / 100, 2 ) . '&nbsp;%',
					'total_amount_value'      => number_format_i18n( $response->get( 'add_paydata[total_amount_value_' . $i . ']' ) / 100, 2 ) . '&nbsp;€',
					'info_url'                => $response->get( 'add_paydata[link_credit_information_href_' . $i . ']' ),
				];
				$i ++;
			}

			echo json_encode( $result );
			exit;
		}

		return null;
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\SecuredInstallment( $this );

		$response = $transaction->execute( $order );

		if ( $response->has_error() ) {
			$order->update_status( 'failed', $this->get_error_message( $response ) );
			wc_add_notice( __( 'Payment failed.', 'payone-woocommerce-3' ), 'error' );

			$this->payla_request_failed();

			return null;
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
