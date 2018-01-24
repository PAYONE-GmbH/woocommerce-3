<?php

namespace Payone\Admin\Option;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Account extends Helper {
	public function __construct() {
		$this->options = get_option( 'payone_account' );
	}

	public function register() {
		register_setting( 'payone', 'payone_account', [ $this, 'sanitize' ] );

		add_settings_section( 'payone_account_settings', 'Kontoinformationen', [ $this, 'accountInfo' ], 'payone-settings-account' );
		add_settings_field( 'account_id', 'Account-ID', [ $this, 'fieldAccountId' ], 'payone-settings-account', 'payone_account_settings' );
		add_settings_field( 'merchant_id', 'Merchant-ID', [ $this, 'fieldMerchantId' ], 'payone-settings-account', 'payone_account_settings' );
		add_settings_field( 'portal_id', 'Portal ID', [ $this, 'fieldPortalId' ], 'payone-settings-account', 'payone_account_settings' );
		add_settings_field( 'key', 'Key', [ $this, 'fieldKey' ], 'payone-settings-account', 'payone_account_settings' );

		add_settings_section( 'payone_settings', 'Einstellungen', [ $this, 'settingsInfo' ], 'payone-settings-account' );
		add_settings_field( 'mode', 'Modus', [ $this, 'fieldMode' ], 'payone-settings-account', 'payone_settings' );
		add_settings_field( 'api_log', 'API-Log', [ $this, 'fieldApiLog' ], 'payone-settings-account', 'payone_settings' );
		add_settings_field( 'transaction_log', 'Transaction-Log', [ $this, 'fieldTransactionLog' ], 'payone-settings-account', 'payone_settings' );
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
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

	public function accountInfo() {
		print 'Enter your account settings below:';
	}

	public function settingsInfo() {
		print 'Enter your settings below:';
	}

	public function fieldAccountId() {
		$this->textField( 'payone_account', 'account_id' );
	}

	public function fieldMerchantId() {
		$this->textField( 'payone_account', 'merchant_id' );
	}

	public function fieldPortalId() {
		$this->textField( 'payone_account', 'portal_id' );
	}

	public function fieldKey() {
		$this->textField( 'payone_account', 'key' );
	}

	public function fieldMode() {
		$this->textField( 'payone_account', 'mode' );
	}

	public function fieldApiLog() {
		$this->textField( 'payone_account', 'api_log' );
	}

	public function fieldTransactionLog() {
		$this->textField( 'payone_account', 'transaction_log' );
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include PAYONE_VIEW_PATH . '/admin/options.php';
	}
}