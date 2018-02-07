<?php

namespace Payone\Admin\Option;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class CreditCheck extends Helper {
	const OPTION_NAME = 'payone_credit_check';

	public function __construct() {
		$this->options = get_option( self::OPTION_NAME );
	}

	public function register() {
		register_setting( 'payone_credit_check', self::OPTION_NAME, [ $this, 'sanitize' ] );

		add_settings_section( 'payone_credit_check',
			__( 'Credit assessment', 'payone' ),
			[ $this, 'account_info' ],
			'payone-credit-check' );
		add_settings_field( 'active',
			__( 'Active', 'payone' ),
			[ $this, 'field_active' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'mode',
			__( 'Mode', 'payone' ),
			[ $this, 'field_mode' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'countries',
			__( 'Country (invoice address)', 'payone' ),
			[ $this, 'field_countries' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'moment_of_assessment',
			__( 'Moment of assessment', 'payone' ),
			[ $this, 'field_moment_of_assessment' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'type_of_assessment',
			__( 'Type of assessment', 'payone' ),
			[ $this, 'field_type_of_assessment' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'default_score',
			__( 'Default score for new customers', 'payone' ),
			[ $this, 'field_default_score' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'validity',
			__( 'Validity', 'payone' ),
			[ $this, 'field_validity' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'minimum_cart_value',
			__( 'Minimum cart value', 'payone' ),
			[ $this, 'field_minimum_cart_value' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'maximum_cart_value',
			__( 'Maximum cart value', 'payone' ),
			[ $this, 'field_maximum_cart_value' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'on_error',
			__( 'On error', 'payone' ),
			[ $this, 'field_on_error' ],
			'payone-credit-check',
			'payone_credit_check' );
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

	public function field_moment_of_assessment() {
		$this->selectField( self::OPTION_NAME,
			'moment_of_assessment',
			[
				'before' => __( 'Before choosing payment method', 'payone' ),
				'after' => __( 'After choosing payment method', 'payone' ),
			] );
	}

	public function field_type_of_assessment() {
		$this->selectField( self::OPTION_NAME,
			'type_of_assessment',
			[
				'infoscore-hard' => __( 'Infoscore ("hard" criterions)', 'payone' ),
				'infoscore-all' => __( 'Infoscore (all features)', 'payone' ),
				'infoscore-boniscore' => __( 'Infoscore (all features + Boniscore)', 'payone' ),
			] );
	}

	public function field_default_score() {
		$this->selectField( self::OPTION_NAME,
			'auto_correct',
			[
				'red' => __( 'Red', 'payone' ),
				'yellow' => __( 'Yellow', 'payone' ),
				'green' => __( 'Green', 'payone' ),
			] );
	}

	public function field_on_error() {
		$this->selectField( self::OPTION_NAME,
			'on_error',
			[
				'abort' => __( 'Abort checkout', 'payone' ),
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

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include PAYONE_VIEW_PATH . '/admin/credit-check.php';
	}
}