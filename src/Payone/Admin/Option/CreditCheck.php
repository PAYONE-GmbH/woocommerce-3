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
			__( 'Credit assessment', 'payone-woocommerce-3' ),
			[ $this, 'account_info' ],
			'payone-credit-check' );
		add_settings_field( 'active',
			__( 'Active', 'payone-woocommerce-3' ),
			[ $this, 'field_active' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'mode',
			__( 'Mode', 'payone-woocommerce-3' ),
			[ $this, 'field_mode' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'countries',
			__( 'Country (invoice address)', 'payone-woocommerce-3' ),
			[ $this, 'field_countries' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'moment_of_assessment',
			__( 'Moment of assessment', 'payone-woocommerce-3' ),
			[ $this, 'field_moment_of_assessment' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'type_of_assessment',
			__( 'Type of assessment', 'payone-woocommerce-3' ),
			[ $this, 'field_type_of_assessment' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'default_score',
			__( 'Default score for new customers', 'payone-woocommerce-3' ),
			[ $this, 'field_default_score' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'validity',
			__( 'Validity', 'payone-woocommerce-3' ),
			[ $this, 'field_validity' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'minimum_cart_value',
			__( 'Minimum cart value', 'payone-woocommerce-3' ),
			[ $this, 'field_minimum_cart_value' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'maximum_cart_value',
			__( 'Maximum cart value', 'payone-woocommerce-3' ),
			[ $this, 'field_maximum_cart_value' ],
			'payone-credit-check',
			'payone_credit_check' );
		add_settings_field( 'on_error',
			__( 'On error', 'payone-woocommerce-3' ),
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
		_e( 'plugin.credit_check.info', 'payone-woocommerce-3' );
	}

	public function field_active() {
		$this->selectField( self::OPTION_NAME,
			'active',
			[
				'0' => __( 'No', 'payone-woocommerce-3' ),
				'1' => __( 'Yes', 'payone-woocommerce-3' ),
			] );
	}

	public function field_mode() {
		$this->selectField( self::OPTION_NAME,
			'mode',
			[
				'test' => __( 'Test', 'payone-woocommerce-3' ),
				'live' => __( 'Live', 'payone-woocommerce-3' ),
			] );
	}

	public function field_countries() {
		$this->selectField( self::OPTION_NAME,
			'countries',
			[
				'DE' => __( 'Germany', 'payone-woocommerce-3' ),
				'AT' => __( 'Austria', 'payone-woocommerce-3' ),
				'CH' => __( 'Switzerland', 'payone-woocommerce-3' ),
			],
			'multiple'
		);
	}

	public function field_moment_of_assessment() {
		$this->selectField( self::OPTION_NAME,
			'moment_of_assessment',
			[
				'before' => __( 'Before choosing payment method', 'payone-woocommerce-3' ),
				'after' => __( 'After choosing payment method', 'payone-woocommerce-3' ),
			] );
	}

	public function field_type_of_assessment() {
		$this->selectField( self::OPTION_NAME,
			'type_of_assessment',
			[
				'infoscore-hard' => __( 'Infoscore ("hard" criterions)', 'payone-woocommerce-3' ),
				'infoscore-all' => __( 'Infoscore (all features)', 'payone-woocommerce-3' ),
				'infoscore-boniscore' => __( 'Infoscore (all features + Boniscore)', 'payone-woocommerce-3' ),
			] );
	}

	public function field_default_score() {
		$this->selectField( self::OPTION_NAME,
			'auto_correct',
			[
				'red' => __( 'Red', 'payone-woocommerce-3' ),
				'yellow' => __( 'Yellow', 'payone-woocommerce-3' ),
				'green' => __( 'Green', 'payone-woocommerce-3' ),
			] );
	}

	public function field_on_error() {
		$this->selectField( self::OPTION_NAME,
			'on_error',
			[
				'abort' => __( 'Abort checkout', 'payone-woocommerce-3' ),
				'continue' => __( 'Continue', 'payone-woocommerce-3' ),
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