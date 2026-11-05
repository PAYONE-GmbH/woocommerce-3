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
			'description' => __( 'Click to Pay requires an onboarding with the card schemes. You receive the following values from PAYONE.', 'payone-woocommerce-3' ),
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
		$this->form_fields['htp_ctp_visa_initiator_id'] = [
			'title'   => 'Visa srcInitiatorId',
			'type'    => 'text',
			'default' => '',
		];
		$this->form_fields['htp_ctp_visa_dpa_id'] = [
			'title'   => 'Visa srcDpaId',
			'type'    => 'text',
			'default' => '',
		];
		$this->form_fields['htp_ctp_visa_encryption_key'] = [
			'title'   => 'Visa encryptionKey',
			'type'    => 'text',
			'default' => '',
		];
		$this->form_fields['htp_ctp_visa_n_modulus'] = [
			'title'   => 'Visa nModulus',
			'type'    => 'textarea',
			'default' => '',
		];
		$this->form_fields['htp_ctp_mastercard_initiator_id'] = [
			'title'   => 'Mastercard srcInitiatorId',
			'type'    => 'text',
			'default' => '',
		];
		$this->form_fields['htp_ctp_mastercard_dpa_id'] = [
			'title'   => 'Mastercard srcDpaId',
			'type'    => 'text',
			'default' => '',
		];
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
		if ( $this->get_option( 'htp_ctp_enabled' ) !== '1' ) {
			return false;
		}

		$has_visa       = $this->get_option( 'htp_ctp_visa_initiator_id' ) && $this->get_option( 'htp_ctp_visa_dpa_id' );
		$has_mastercard = $this->get_option( 'htp_ctp_mastercard_initiator_id' ) && $this->get_option( 'htp_ctp_mastercard_dpa_id' );

		return $has_visa || $has_mastercard;
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
			'ctp'                => null,
		];

		if ( $this->is_click_to_pay_enabled() ) {
			$scheme_config = [
				'merchantPresentationName' => (string) $this->get_option( 'htp_ctp_merchant_name' ),
			];
			if ( $this->get_option( 'htp_ctp_visa_initiator_id' ) ) {
				$scheme_config['visaConfig'] = [
					'srcInitiatorId' => (string) $this->get_option( 'htp_ctp_visa_initiator_id' ),
					'srcDpaId'       => (string) $this->get_option( 'htp_ctp_visa_dpa_id' ),
					'encryptionKey'  => (string) $this->get_option( 'htp_ctp_visa_encryption_key' ),
					'nModulus'       => (string) $this->get_option( 'htp_ctp_visa_n_modulus' ),
				];
			}
			if ( $this->get_option( 'htp_ctp_mastercard_initiator_id' ) ) {
				$scheme_config['mastercardConfig'] = [
					'srcInitiatorId' => (string) $this->get_option( 'htp_ctp_mastercard_initiator_id' ),
					'srcDpaId'       => (string) $this->get_option( 'htp_ctp_mastercard_dpa_id' ),
				];
			}

			$config['ctp'] = [
				'enableCTP'                => true,
				'enableCustomerOnboarding' => $this->get_option( 'htp_ctp_customer_onboarding' ) === '1',
				'schemeConfig'             => $scheme_config,
				'shopName'                 => (string) $this->get_option( 'htp_ctp_merchant_name' ),
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
