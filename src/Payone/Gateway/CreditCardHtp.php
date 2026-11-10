<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;
use Payone\Transaction\GetJwt;

/**
 * "Kreditkarte neu": credit card with the PAYONE Hosted Tokenization SDK and optional Click to Pay.
 *
 * Runs side by side with the old credit card gateway (hosted iFrames) during the transition period.
 */
class CreditCardHtp extends RedirectGatewayBase {
	const GATEWAY_ID = 'payone_creditcard_htp';

	const SDK_VERSION   = '1.8.0';
	const SDK_URL       = 'https://sdk.tokenization.secure.payone.com/1.8.0/hosted-tokenization-sdk.js';
	const SDK_INTEGRITY = 'sha384-byt6K+poGVXms01bZrPUd3/k2fr6gF5NVYY1Rwsnru/34fdwZC2KJRZbSltKp8nE';

	/**
	 * The JWT is valid for 10 minutes and may be used several times.
	 * We cache it for 5 minutes, so a cached token is always valid for at least 5 more minutes.
	 */
	const JWT_CACHE_SECONDS = 300;

	const CARD_SCHEMES = [
		'visa'       => 'VISA',
		'mastercard' => 'Mastercard',
		'amex'       => 'American Express',
		'diners'     => 'Diners Club',
		'discover'   => 'Discover',
		'jcb'        => 'JCB',
		'maestro'    => 'Maestro',
		'unionpay'   => 'UnionPay',
	];

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-creditcard.png';
		$this->method_title       = 'PAYONE ' . __( 'Credit Card (new)', 'payone-woocommerce-3' );
		$this->method_description = __( 'Credit card payment with the PAYONE Hosted Tokenization form, optionally with Click to Pay.', 'payone-woocommerce-3' );

		$this->test_transaction_classname = \Payone\Transaction\CreditCard::class;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Credit Card', 'payone-woocommerce-3' ) );

		$yesno_options = [
			'0' => __( 'No', 'payone-woocommerce-3' ),
			'1' => __( 'Yes', 'payone-woocommerce-3' ),
		];

		$this->form_fields['htp_label'] = [
			'title' => __( 'Credit card settings', 'payone-woocommerce-3' ),
			'type'  => 'title',
		];
		$this->form_fields['htp_card_schemes'] = [
			'title'   => __( 'Credit card brands', 'payone-woocommerce-3' ),
			'type'    => 'multiselect',
			'class'   => 'wc-enhanced-select',
			'options' => self::CARD_SCHEMES,
			'default' => [ 'visa', 'mastercard', 'amex' ],
		];
		$this->form_fields['htp_show_cardholder'] = [
			'title'   => __( 'Show card holder field', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $yesno_options,
			'default' => '1',
		];

		$this->form_fields['htp_ctp_label'] = [
			'title'       => __( 'Click to Pay', 'payone-woocommerce-3' ),
			'type'        => 'title',
			'description' => __( 'Click to Pay is only shown in live mode.', 'payone-woocommerce-3' ),
		];
		$this->form_fields['htp_ctp_enabled'] = [
			'title'   => __( 'Enable Click to Pay', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $yesno_options,
			'default' => '0',
		];
		$this->form_fields['htp_ctp_customer_onboarding'] = [
			'title'   => __( 'Allow customer registration for Click to Pay', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $yesno_options,
			'default' => '1',
		];
		$this->form_fields['htp_ctp_merchant_name'] = [
			'title'   => __( 'Merchant presentation name', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => get_bloginfo( 'name' ),
		];
		$this->form_fields['htp_style_label'] = [
			'title'       => __( 'Appearance of the credit card form', 'payone-woocommerce-3' ),
			'type'        => 'title',
			'description' => __( 'Empty fields use the PAYONE default. Colors as hex code, e.g. #3B82F6.', 'payone-woocommerce-3' ),
			'payone_collapsed' => true,
		];
		foreach ( self::get_style_fields() as $key => $field ) {
			$this->form_fields[ $key ] = [
				'title'       => $field['title'],
				'type'        => isset( $field['options'] ) ? 'select' : ( self::is_color_field( $field ) ? 'payone_color' : 'text' ),
				'options'     => isset( $field['options'] ) ? $field['options'] : [],
				'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
				'default'     => '',
			];
		}
	}

	/**
	 * Appearance settings: setting key => SDK option.
	 * "form" options go to uiConfig, "ctp" options to CTPConfig.uiConfig.
	 *
	 * @return array
	 */
	public static function get_style_fields() {
		return [
			'htp_style_form_bg_color'          => [ 'target' => 'form', 'option' => 'formBgColor', 'title' => __( 'Form background color', 'payone-woocommerce-3' ), 'placeholder' => '#FFFFFF' ],
			'htp_style_field_bg_color'         => [ 'target' => 'form', 'option' => 'fieldBgColor', 'title' => __( 'Field background color', 'payone-woocommerce-3' ), 'placeholder' => '#FFFFFF' ],
			'htp_style_field_border'           => [ 'target' => 'form', 'option' => 'fieldBorder', 'title' => __( 'Field border (CSS)', 'payone-woocommerce-3' ), 'placeholder' => '1px solid #8FA8C8' ],
			'htp_style_field_outline'          => [ 'target' => 'form', 'option' => 'fieldOutline', 'title' => __( 'Field outline on focus (CSS)', 'payone-woocommerce-3' ), 'placeholder' => '#3B82F6 solid 2px' ],
			'htp_style_field_label_color'      => [ 'target' => 'form', 'option' => 'fieldLabelColor', 'title' => __( 'Label color', 'payone-woocommerce-3' ), 'placeholder' => '#334155' ],
			'htp_style_field_placeholder_color' => [ 'target' => 'form', 'option' => 'fieldPlaceholderColor', 'title' => __( 'Placeholder color', 'payone-woocommerce-3' ), 'placeholder' => '#6B7280' ],
			'htp_style_field_text_color'       => [ 'target' => 'form', 'option' => 'fieldTextColor', 'title' => __( 'Text color', 'payone-woocommerce-3' ), 'placeholder' => '#1E293B' ],
			'htp_style_field_error_color'      => [ 'target' => 'form', 'option' => 'fieldErrorCodeColor', 'title' => __( 'Error color', 'payone-woocommerce-3' ), 'placeholder' => '#D97706' ],
			'htp_style_input_border_radius'    => [ 'target' => 'form', 'option' => 'inputBorderRadius', 'title' => __( 'Field border radius', 'payone-woocommerce-3' ), 'placeholder' => '8px' ],
			'htp_style_font_family'            => [ 'target' => 'form', 'option' => 'fontFamily', 'title' => __( 'Font family', 'payone-woocommerce-3' ), 'placeholder' => 'Roboto' ],
			'htp_style_font_url'               => [ 'target' => 'form', 'option' => 'fontUrl', 'title' => __( 'Font URL', 'payone-woocommerce-3' ), 'placeholder' => 'https://fonts.googleapis.com/css2?family=Roboto' ],
			'htp_style_ctp_button_style'       => [ 'target' => 'ctp', 'option' => 'buttonStyle', 'title' => __( 'Click to Pay button style', 'payone-woocommerce-3' ), 'options' => [ '' => __( 'Default', 'payone-woocommerce-3' ), 'FILLED' => __( 'Filled', 'payone-woocommerce-3' ), 'OUTLINED' => __( 'Outlined', 'payone-woocommerce-3' ) ] ],
			'htp_style_ctp_button_color'       => [ 'target' => 'ctp', 'option' => 'buttonAndBadgeColor', 'title' => __( 'Click to Pay button color', 'payone-woocommerce-3' ), 'placeholder' => '#3B82F6' ],
			'htp_style_ctp_button_text_color'  => [ 'target' => 'ctp', 'option' => 'buttonAndBadgeTextColor', 'title' => __( 'Click to Pay button text color', 'payone-woocommerce-3' ), 'placeholder' => '#FFFFFF' ],
			'htp_style_ctp_link_color'         => [ 'target' => 'ctp', 'option' => 'linkTextColor', 'title' => __( 'Click to Pay link color', 'payone-woocommerce-3' ), 'placeholder' => '#3B82F6' ],
			'htp_style_ctp_accent_color'       => [ 'target' => 'ctp', 'option' => 'accentColor', 'title' => __( 'Click to Pay accent color', 'payone-woocommerce-3' ), 'placeholder' => '#6390F2' ],
			'htp_style_ctp_radius'             => [ 'target' => 'ctp', 'option' => 'buttonAndInputRadius', 'title' => __( 'Click to Pay button radius', 'payone-woocommerce-3' ), 'placeholder' => '1rem' ],
		];
	}

	/**
	 * @param array $field Entry of get_style_fields()
	 *
	 * @return bool
	 */
	private static function is_color_field( $field ) {
		return isset( $field['placeholder'] ) && preg_match( '/^#[0-9a-f]{6}$/i', $field['placeholder'] );
	}

	/**
	 * @param string $target "form" or "ctp"
	 *
	 * @return array Only the options that are set
	 */
	public function get_ui_config( $target ) {
		$ui_config = [];
		foreach ( self::get_style_fields() as $key => $field ) {
			$value = trim( (string) $this->get_option( $key, '' ) );
			if ( $field['target'] === $target && $value !== '' ) {
				$ui_config[ $field['option'] ] = $value;
			}
		}

		return $ui_config;
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && count( $this->get_card_schemes() ) === 0 ) {
			$is_available = false;
		}

		return $is_available;
	}

	/**
	 * @return string[]
	 */
	public function get_card_schemes() {
		$schemes = (array) $this->get_option( 'htp_card_schemes', [] );

		return array_values( array_intersect( $schemes, array_keys( self::CARD_SCHEMES ) ) );
	}

	/**
	 * @return bool
	 */
	public function is_click_to_pay_enabled() {
		return $this->get_option( 'htp_ctp_enabled' ) === '1';
	}

	/**
	 * Configuration for the Hosted Tokenization SDK, shared by classic and block checkout.
	 * The JWT and the transaction amount are added client side.
	 *
	 * @return array
	 */
	public function get_htp_config() {
		$config = [
			'title'              => $this->get_title(),
			'sdkUrl'            => apply_filters( 'payone_htp_sdk_url', self::SDK_URL ),
			'sdkIntegrity'       => apply_filters( 'payone_htp_sdk_integrity', self::SDK_INTEGRITY ),
			'jwtUrl'             => Plugin::get_callback_url( [ 'type' => 'ajax-htp-jwt' ] ),
			'mode'               => $this->get_mode() === 'live' ? 'live' : 'test',
			'locale'             => strpos( get_locale(), 'de' ) === 0 ? 'de_DE' : 'en_US',
			'allowedCardSchemes' => $this->get_card_schemes(),
			'showCardholderName' => $this->get_option( 'htp_show_cardholder' ) === '1',
			'uiConfig'           => (object) $this->get_ui_config( 'form' ),
			'ctp'                => null,
		];

		if ( $this->is_click_to_pay_enabled() ) {
			$merchant_name = (string) $this->get_option( 'htp_ctp_merchant_name' );

			// No schemeConfig: the payment page only fetches the Visa/Mastercard SRC credentials
			// for the portal when schemeConfig is empty. Any value there replaces them and Click to Pay
			// fails with "No SRC attributes were passed".
			$config['ctp'] = [
				'enableCTP'                => true,
				'enableCustomerOnboarding' => $this->get_option( 'htp_ctp_customer_onboarding' ) === '1',
				'shopName'                 => $merchant_name,
				'uiConfig'                 => (object) $this->get_ui_config( 'ctp' ),
			];
		}

		return apply_filters( 'payone_htp_config', $config, $this );
	}

	/**
	 * Returns a JWT for the Hosted Tokenization SDK, cached for a few minutes.
	 *
	 * @return string Empty string on error
	 */
	public function get_jwt() {
		$cache_key = 'payone_htp_jwt_' . md5( $this->get_mode() . '|' . $this->get_merchant_id() . '|' . $this->get_portal_id() . '|' . $this->get_account_id() );
		$token     = get_transient( $cache_key );
		if ( is_string( $token ) && $token !== '' ) {
			return $token;
		}

		$response = ( new GetJwt( $this ) )->execute();
		$token    = (string) $response->get( 'token', '' );
		if ( $token === '' ) {
			error_log( 'PAYONE: getJWT failed: ' . $response->get_error_code() . ' ' . $response->get_error_message() );

			return '';
		}

		set_transient( $cache_key, $token, self::JWT_CACHE_SECONDS );

		return $token;
	}

	/**
	 * Answers the AJAX request of the checkout for a JWT.
	 */
	public function process_jwt_callback() {
		$token = $this->enabled === 'yes' ? $this->get_jwt() : '';

		wp_send_json( [ 'token' => $token ], $token === '' ? 503 : 200 );
	}

	/**
	 * @return int
	 */
	public function get_cart_amount_in_cents() {
		if ( WC()->cart ) {
			return (int) round( 100 * (float) WC()->cart->get_total( 'edit' ) );
		}

		return 0;
	}

	public function payment_fields() {
		$htp_config = $this->get_htp_config();
		$amount     = $this->get_cart_amount_in_cents();
		$currency   = get_woocommerce_currency();

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/creditcard-htp/payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\CreditCardHtp::class );
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->no_further_action_necessary() ) {
			return;
		}

		$order                = $transaction_status->get_order();
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'authorization' && $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment is authorized by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_capture() ) {
			$order->add_order_note( __( 'Payment is captured by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} else {
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone-woocommerce-3' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}
}
