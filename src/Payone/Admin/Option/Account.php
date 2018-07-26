<?php

namespace Payone\Admin\Option;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Account extends Helper {
	const OPTION_NAME = 'payone_account';

	public function __construct() {
		$this->options = get_option( self::OPTION_NAME );
	}

	public function register() {
		register_setting( 'payone', self::OPTION_NAME, [ $this, 'sanitize' ] );

		add_settings_section( 'payone_account_settings',
			__( 'Global Settings', 'payone-woocommerce-3' ),
			[ $this, 'account_info' ],
			'payone-settings-account' );
		add_settings_field( 'merchant_id',
			__( 'Merchant ID', 'payone-woocommerce-3' ),
			[ $this, 'field_merchant_id' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'portal_id',
			__( 'Portal ID', 'payone-woocommerce-3' ),
			[ $this, 'field_portal_id' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'account_id',
			__( 'Subaccount ID', 'payone-woocommerce-3' ),
			[ $this, 'field_account_id' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'key',
			__( 'Key', 'payone-woocommerce-3' ),
			[ $this, 'field_key' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'mode',
			__( 'Mode', 'payone-woocommerce-3' ),
			[ $this, 'field_mode' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'submit_cart',
			__( 'Submit cart', 'payone-woocommerce-3' ),
			[ $this, 'field_submit_cart' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'activate_pdf_download',
			__( 'Activate PDF download', 'payone-woocommerce-3' ),
			[ $this, 'field_activate_pdf_download' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'dynamic_invoice_text',
			__( 'Dynamic invoice text', 'payone-woocommerce-3' ),
			[ $this, 'field_dynamic_invoice_text' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'dynamic_refund_text',
			__( 'Dynamic refund text', 'payone-woocommerce-3' ),
			[ $this, 'field_dynamic_refund_text' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'text_on_booking_statement',
			__( 'Text on booking statement', 'payone-woocommerce-3' ),
			[ $this, 'field_text_on_booking_statement' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'api_log',
			__( 'API-Log', 'payone-woocommerce-3' ),
			[ $this, 'field_api_log' ],
			'payone-settings-account',
			'payone_account_settings' );
		add_settings_field( 'transaction_log',
			__( 'Transaction Status Log', 'payone-woocommerce-3' ),
			[ $this, 'field_transaction_log' ],
			'payone-settings-account',
			'payone_account_settings' );
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		return $input;

		$new_input = array();
		if ( isset( $input['id_number'] ) ) {
			$new_input['id_number'] = absint( $input['id_number'] );
		}

		if ( isset( $input['title'] ) ) {
			$new_input['title'] = sanitize_text_field( $input['title'] );
		}

		return $new_input;
	}

	public function account_info() {
		_e( 'plugin.settings.info', 'payone-woocommerce-3' );
	}

	public function field_account_id() {
		$this->textField( self::OPTION_NAME, 'account_id' );
	}

	public function field_merchant_id() {
		$this->textField( self::OPTION_NAME, 'merchant_id' );
	}

	public function field_portal_id() {
		$this->textField( self::OPTION_NAME, 'portal_id' );
	}

	public function field_key() {
		$this->textField( self::OPTION_NAME, 'key' );
	}

	public function field_mode() {
		$this->selectField( self::OPTION_NAME,
			'mode',
			[
				'test' => __( 'Test', 'payone-woocommerce-3' ),
				'live' => __( 'Live', 'payone-woocommerce-3' ),
			] );
	}

	public function field_submit_cart() {
		$this->selectField( self::OPTION_NAME,
			'submit_cart',
			[
				'0' => __( 'No', 'payone-woocommerce-3' ),
				'1' => __( 'Yes', 'payone-woocommerce-3' ),
			] );
	}

	public function field_activate_pdf_download() {
		$this->selectField( self::OPTION_NAME,
			'activate_pdf_download',
			[
				'0' => __( 'No', 'payone-woocommerce-3' ),
				'1' => __( 'Yes', 'payone-woocommerce-3' ),
			] );
	}

	public function field_dynamic_invoice_text() {
		$this->textField( self::OPTION_NAME, 'dynamic_invoice_text', __( 'Your order No. {{order}}', 'payone' ) );
	}

	public function field_dynamic_refund_text() {
		$this->textField( self::OPTION_NAME, 'dynamic_refund_text', __( 'Your refund No. {{order}}', 'payone' ) );
	}

	public function field_text_on_booking_statement() {
		$this->textField( self::OPTION_NAME, 'text_on_booking_statement', __( 'Your order No. {{order}}', 'payone' ) );
	}

	public function field_api_log() {
		$this->selectField( self::OPTION_NAME,
			'api_log',
			[
				'0' => __( 'Deactivated', 'payone-woocommerce-3' ),
				'1' => __( 'Activated', 'payone-woocommerce-3' ),
			] );
	}

	public function field_transaction_log() {
		$this->selectField( self::OPTION_NAME,
			'transaction_log',
			[
				'0' => __( 'Deactivated', 'payone-woocommerce-3' ),
				'1' => __( 'Activated', 'payone-woocommerce-3' ),
			] );
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include PAYONE_VIEW_PATH . '/admin/options.php';
	}
}