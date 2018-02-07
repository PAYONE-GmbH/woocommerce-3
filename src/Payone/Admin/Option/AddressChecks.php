<?php

namespace Payone\Admin\Option;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class AddressChecks extends Helper {
	const OPTION_NAME = 'payone_address_checks';

	public function __construct() {
		$this->options = get_option( self::OPTION_NAME );
	}

	public function register() {
		register_setting( 'payone_address_checks', self::OPTION_NAME, [ $this, 'sanitize' ] );

		add_settings_section( 'payone_address_checks',
			__( 'Address validation', 'payone' ),
			[ $this, 'account_info' ],
			'payone-address-checks' );
		add_settings_field( 'active',
			__( 'Active', 'payone' ),
			[ $this, 'field_active' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'mode',
			__( 'Mode', 'payone' ),
			[ $this, 'field_mode' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'address',
			__( 'Address', 'payone' ),
			[ $this, 'field_address' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'type',
			__( 'Type', 'payone' ),
			[ $this, 'field_type' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'countries',
			__( 'Countries', 'payone' ),
			[ $this, 'field_countries' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'auto_correct',
			__( 'Correct automatically', 'payone' ),
			[ $this, 'field_auto_correct' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'on_error',
			__( 'On error', 'payone' ),
			[ $this, 'field_on_error' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'minimum_cart_value',
			__( 'Minimum cart value', 'payone' ),
			[ $this, 'field_minimum_cart_value' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'maximum_cart_value',
			__( 'Maximum cart value', 'payone' ),
			[ $this, 'field_maximum_cart_value' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'validity',
			__( 'Validity', 'payone' ),
			[ $this, 'field_validity' ],
			'payone-address-checks',
			'payone_address_checks' );
		add_settings_field( 'error_message',
			__( 'Error message', 'payone' ),
			[ $this, 'field_error_message' ],
			'payone-address-checks',
			'payone_address_checks' );
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
		print 'Enter your account settings below:';
	}

	public function field_active() {
		$this->selectField( self::OPTION_NAME,
			'active',
			[
				'0' => __( 'No', 'payone' ),
				'1' => __( 'Yes', 'payone' ),
			] );
	}

	public function field_mode() {
		$this->selectField( self::OPTION_NAME,
			'mode',
			[
				'test' => __( 'Test', 'payone' ),
				'live' => __( 'Live', 'payone' ),
			] );
	}

	public function field_address() {
		$this->selectField( self::OPTION_NAME,
			'address',
			[
				'invoice' => __( 'Invoice address', 'payone' ),
				'delivery' => __( 'Delivery address', 'payone' ),
			] );
	}

	public function field_type() {
		$this->selectField( self::OPTION_NAME,
			'type',
			[
				'basic' => __( 'Basic', 'payone' ),
				'person' => __( 'Person (only available in Germany)', 'payone' ),
			] );
	}

	public function field_countries() {
		$this->selectField( self::OPTION_NAME,
			'countries',
			[
				'DE' => __( 'Germany', 'payone' ),
				'AT' => __( 'Austria', 'payone' ),
				'CH' => __( 'Switzerland', 'payone' ),
			],
			'multiple'
		);
	}

	public function field_auto_correct() {
		$this->selectField( self::OPTION_NAME,
			'auto_correct',
			[
				'no' => __( 'No', 'payone' ),
				'yes' => __( 'Yes', 'payone' ),
				'customer-decides' => __( 'Customer decides', 'payone' ),
			] );
	}

	public function field_on_error() {
		$this->selectField( self::OPTION_NAME,
			'on_error',
			[
				'cancel' => __( 'Cancel checkout', 'payone' ),
				're-entry' => __( 'Request re-entry of the address causing the problem', 'payone' ),
				'follow-up' => __( 'Perform follow-up credit check', 'payone' ),
				'continue' => __( 'Continue', 'payone' ),
			] );
	}

	public function field_minimum_cart_value() {
		$this->textField( self::OPTION_NAME, 'minimum_cart_value', 100 );
	}

	public function field_maximum_cart_value() {
		$this->textField( self::OPTION_NAME, 'maximum_cart_value', 1000 );
	}

	public function field_validity() {
		$this->textField( self::OPTION_NAME, 'validity', 99 );
	}

	public function field_error_message() {
		$this->textField( self::OPTION_NAME, 'error_message', 'The data you provided wa sinvalid {{payone_customermessage}}' );
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include PAYONE_VIEW_PATH . '/admin/address-checks.php';
	}
}