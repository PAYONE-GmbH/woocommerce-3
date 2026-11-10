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
		add_settings_section( 'payone_order_settings',
			__( 'Orders and receipts', 'payone-woocommerce-3' ),
			'__return_false',
			'payone-settings-account' );
		add_settings_field( 'submit_cart',
			__( 'Submit cart', 'payone-woocommerce-3' ),
			[ $this, 'field_submit_cart' ],
			'payone-settings-account',
			'payone_order_settings' );
		add_settings_field( 'activate_pdf_download',
			__( 'Activate PDF download', 'payone-woocommerce-3' ),
			[ $this, 'field_activate_pdf_download' ],
			'payone-settings-account',
			'payone_order_settings' );
		add_settings_field( 'dynamic_invoice_text',
			__( 'Dynamic invoice text', 'payone-woocommerce-3' ),
			[ $this, 'field_dynamic_invoice_text' ],
			'payone-settings-account',
			'payone_order_settings' );
		add_settings_field( 'dynamic_refund_text',
			__( 'Dynamic refund text', 'payone-woocommerce-3' ),
			[ $this, 'field_dynamic_refund_text' ],
			'payone-settings-account',
			'payone_order_settings' );
		add_settings_field( 'text_on_booking_statement',
			__( 'Text on booking statement', 'payone-woocommerce-3' ),
			[ $this, 'field_text_on_booking_statement' ],
			'payone-settings-account',
			'payone_order_settings' );
		add_settings_section( 'payone_log_settings',
			__( 'Logging', 'payone-woocommerce-3' ),
			[ $this, 'log_info' ],
			'payone-settings-account' );
		add_settings_field( 'api_log',
			__( 'API-Log', 'payone-woocommerce-3' ),
			[ $this, 'field_api_log' ],
			'payone-settings-account',
			'payone_log_settings' );
		add_settings_field( 'transaction_log',
			__( 'Transaction Status Log', 'payone-woocommerce-3' ),
			[ $this, 'field_transaction_log' ],
			'payone-settings-account',
			'payone_log_settings' );
		add_settings_field( 'payone_invoice_module_enabled',
			__( 'Is PAYONE Invoice module enabled?', 'payone-woocommerce-3' ),
			[ $this, 'payone_invoice_module_enabled' ],
			'payone-settings-account',
			'payone_order_settings' );
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
		echo '<p>';
		_e( 'Don\'t have a PAYONE Account yet? Click <a href="https://www.payone.com/DE-de/kampagne/ecom-testaccount" target="_blank" rel="noopener">here</a> to apply for test credentials.', 'payone-woocommerce-3' );
		echo '</p>';
	}

	public function log_info() {
		echo '<p>' . esc_html__( 'The logs are shown under PAYONE > API-Log and PAYONE > Transaction Status Log.', 'payone-woocommerce-3' ) . '</p>';
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
		$this->secretField( self::OPTION_NAME, 'key' );
	}

	public function field_mode() {
		$this->segmentedField( self::OPTION_NAME,
			'mode',
			[
				'test' => __( 'Test', 'payone-woocommerce-3' ),
				'live' => __( 'Live', 'payone-woocommerce-3' ),
			] );
	}

	public function field_submit_cart() {
		$this->switchField( self::OPTION_NAME, 'submit_cart' );
	}

	public function field_activate_pdf_download() {
		$this->switchField( self::OPTION_NAME, 'activate_pdf_download' );
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
		$this->switchField( self::OPTION_NAME, 'api_log' );
	}

	public function field_transaction_log() {
		$this->switchField( self::OPTION_NAME, 'transaction_log' );
	}

	public function payone_invoice_module_enabled() {
		$this->switchField( self::OPTION_NAME, 'payone_invoice_module_enabled' );
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$testable_gateways  = [];
		$available_gateways = WC()->payment_gateways->payment_gateways();
		foreach ( $available_gateways as $gateway_id => $gateway ) {
			if ( method_exists( $gateway, 'payone_is_testable' )
			     && $gateway->payone_is_testable()
			     && $gateway->enabled === 'yes'
			) {
				$testable_gateways[ $gateway_id ] = $gateway->method_title;
			}
		}

		$options     = is_array( $this->options ) ? $this->options : [];
		$is_live     = isset( $options['mode'] ) && $options['mode'] === 'live';
		$is_complete = true;
		foreach ( [ 'merchant_id', 'portal_id', 'account_id', 'key' ] as $required ) {
			if ( empty( $options[ $required ] ) ) {
				$is_complete = false;
			}
		}

		include PAYONE_VIEW_PATH . '/admin/options.php';
	}
}
