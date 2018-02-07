<?php

namespace Payone\Gateway;

class SepaDirectDebit extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_sepa';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'BS PAYONE Lastschrift (SEPA)';
		$this->method_description = 'method_description';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'SEPA Direct Debit', 'payone' ) );
		$this->form_fields['sepa_check_bank_data'] = [
			'title'   => __( 'Check bank data', 'payone' ),
			'type'    => 'select',
			'options' => [
				'basic'    => __( 'Basic', 'payone' ),
				'blacklist' => __( 'Check POS black list', 'payone' ),
				'none' => __( 'None (only possible if PAYONE Mandate Management is inactive)', 'payone' ),
			],
			'default' => 'basic',
		];
		$this->form_fields['sepa_ask_account_number'] = [
			'title'   => __( 'Ask account number/bank code (for german accounts only)', 'payone' ),
			'type'    => 'select',
			'options' => [
				'0' => __( 'No', 'payone' ),
				'1' => __( 'Yes', 'payone' ),
			],
			'default' => '1',
		];
		$this->form_fields['sepa_use_mandate_management'] = [
			'title'   => __( 'Use PAYONE Mandate Management', 'payone' ),
			'type'    => 'select',
			'options' => [
				'0' => __( 'No', 'payone' ),
				'1' => __( 'Yes', 'payone' ),
			],
			'default' => '1',
		];
		$this->form_fields['sepa_pdf_download_mandate'] = [
			'title'   => __( 'Download mandate as PDF', 'payone' ),
			'type'    => 'select',
			'options' => [
				'0' => __( 'No', 'payone' ),
				'1' => __( 'Yes', 'payone' ),
			],
			'default' => '1',
		];
		$this->form_fields['sepa_countries'] = [
			'title'   => __( 'List of supported bank countries', 'payone' ),
			'type'    => 'multiselect',
			'options' => [
				'DE' => __( 'Germany', 'payone' ),
				'AT' => __( 'Austria', 'payone' ),
				'CH' => __( 'Switzerland', 'payone' ),
			],
			'default' => [ 'DE', 'AT', 'CH' ],
		];
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/sepa-direct-debit/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status( 'on-hold', __( 'Awaiting cheque payment', 'woocommerce' ) );

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