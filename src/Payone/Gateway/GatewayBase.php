<?php

namespace Payone\Gateway;

abstract class GatewayBase extends \WC_Payment_Gateway {
	/**
	 * @var array
	 */
	private $global_settings;

	/**
	 * @var string 0 or 1
	 */
	private $use_global_settings;

	/**
	 * @var string
	 */
	private $authorization_method;

	/**
	 * @var float
	 */
	private $min_amount;

	/**
	 * @var string
	 */
	private $merchant_id;

	/**
	 * @var string
	 */
	private $portal_id;

	/**
	 * @var string
	 */
	private $account_id;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string 0 or 1
	 */
	private $submit_cart;

	/**
	 * @var string 0 or 1
	 */
	private $activate_pdf_download;

	/**
	 * @var string
	 */
	private $dynamic_invoice_text;

	/**
	 * @var string
	 */
	private $dynamic_refund_text;

	/**
	 * @var string
	 */
	private $text_on_booking_statement;

	public function __construct( $id ) {
		$this->id              = $id;
		$this->has_fields      = true;
		$this->global_settings = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		$this->init_settings();
		$this->init_form_fields();

		$this->title                = $this->get_option( 'title' );
		$this->authorization_method = $this->settings['authorization_method'];
		$this->min_amount           = $this->settings['min_amount'];
		$this->max_amount           = $this->settings['max_amount'];
		$this->countries            = $this->settings['countries'];

		$this->process_global_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public function add( $methods ) {
		$methods[] = get_class( $this );

		return $methods;
	}

	/**
	 * @todo Es ist nicht klar, warum das nicht ohne eigenen Code funktioniert. Die Doku zu $this->countries sieht
	 *       eigentlich so aus, als ob es funktionieren müsste.
	 * @todo Soll die billing_country oder shipping_country genommen werden?
	 *
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && $this->min_amount > $this->get_order_total() ) {
			$is_available = false;
		}

		if ( $is_available ) {
			$order_id = absint( get_query_var( 'order-pay' ) );

			if ( $order_id ) {
				$order   = wc_get_order( $order_id );
				$country = (string) $order->get_billing_country();
			} elseif ( WC()->customer->get_billing_country() ) {
				$country = (string) WC()->customer->get_billing_country();
			}

			$is_available = in_array( $country, $this->countries );
		}

		return $is_available;
	}

	/**
	 * @param string $label
	 */
	public function init_common_form_fields( $label ) {
		$default_merchant_id = $this->global_settings['merchant_id'];
		$default_portal_id   = $this->global_settings['portal_id'];
		$default_account_id  = $this->global_settings['account_id'];
		$default_key         = $this->global_settings['key'];

		$this->form_fields = [
			'enabled'                   => [
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this payment method', 'payone' ),
				'default' => 'yes',
			],
			'title'                     => [
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => $label,
				'desc_tip'    => true,
			],
			'description'               => [
				'title'   => __( 'Customer Message', 'woocommerce' ),
				'type'    => 'textarea',
				'default' => '',
			],
			'min_amount'                => [
				'title'   => __( 'Minimum order value', 'payone' ),
				'type'    => 'text',
				'default' => '0',
			],
			'max_amount'                => [
				'title'   => __( 'Maximum order value', 'payone' ),
				'type'    => 'text',
				'default' => '0',
			],
			'authorization_method'              => [
				'title'   => __( 'Method of Authorization', 'payone' ),
				'type'    => 'select',
				'options' => [
					'authorization'    => __( 'Authorization', 'payone' ),
					'preauthorization' => __( 'Preauthorization', 'payone' ),
				],
				'default' => 'authorization',
			],
			'countries'                 => [
				'title'   => __( 'Active Countries', 'payone' ),
				'type'    => 'multiselect',
				'options' => [
					'DE' => __( 'Germany', 'payone' ),
					'AT' => __( 'Austria', 'payone' ),
					'CH' => __( 'Switzerland', 'payone' ),
				],
				'default' => [ 'DE', 'AT', 'CH' ],
			],
			'use_global_settings'       => [
				'title'   => __( 'Use global settings', 'payone' ),
				'type'    => 'select',
				'options' => [
					'0' => __( 'No', 'payone' ),
					'1' => __( 'Yes', 'payone' ),
				],
				'default' => '1',
			],
			'merchant_id'               => [
				'title'   => __( 'Merchant ID', 'payone' ),
				'type'    => 'text',
				'default' => $default_merchant_id,
			],
			'portal_id'                 => [
				'title'   => __( 'Portal ID', 'payone' ),
				'type'    => 'text',
				'default' => $default_portal_id,
			],
			'account_id'                => [
				'title'   => __( 'Account ID', 'payone' ),
				'type'    => 'text',
				'default' => $default_account_id,
			],
			'key'                       => [
				'title'   => __( 'Key', 'payone' ),
				'type'    => 'text',
				'default' => $default_key,
			],
			'submit_cart'               => [
				'title'   => __( 'Submit cart', 'payone' ),
				'type'    => 'select',
				'options' => [
					'0' => __( 'No', 'payone' ),
					'1' => __( 'Yes', 'payone' ),
				],
				'default' => '0',
			],
			'activate_pdf_download'     => [
				'title'   => __( 'Activate PDF download', 'payone' ),
				'type'    => 'select',
				'options' => [
					'0' => __( 'No', 'payone' ),
					'1' => __( 'Yes', 'payone' ),
				],
				'default' => '0',
			],
			'dynamic_invoice_text'      => [
				'title'   => __( 'Dynamic invoice text', 'payone' ),
				'type'    => 'text',
				'default' => __( 'Your order No. {{order}}', 'payone' ),
			],
			'dynamic_refund_text'       => [
				'title'   => __( 'Dynamic refund text', 'payone' ),
				'type'    => 'text',
				'default' => __( 'Your refund No. {{order}}', 'payone' ),
			],
			'text_on_booking_statement' => [
				'title'   => __( 'Text on booking statement', 'payone' ),
				'type'    => 'text',
				'default' => __( 'Your refund No. {{order}}', 'payone' ),
			],
		];

		if ( $this->id === PrePayment::GATEWAY_ID ) {
			unset( $this->form_fields['authorization_method']['options']['authorization'] );
			$this->form_fields['authorization_method']['default'] = 'preauthorization';
		}
	}

	/**
	 * @return string
	 */
	public function get_authorization_method() {
		return $this->authorization_method;
	}

	/**
	 * @return string
	 */
	public function get_merchant_id() {
		return $this->merchant_id;
	}

	/**
	 * @return string
	 */
	public function get_portal_id() {
		return $this->portal_id;
	}

	/**
	 * @return string
	 */
	public function get_account_id() {
		return $this->account_id;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function get_submit_cart() {
		return $this->submit_cart;
	}

	/**
	 * @return string
	 */
	public function get_activate_pdf_download() {
		return $this->activate_pdf_download;
	}

	/**
	 * @return string
	 */
	public function get_dynamic_invoice_text() {
		return $this->dynamic_invoice_text;
	}

	/**
	 * @return string
	 */
	public function get_dynamic_refund_text() {
		return $this->dynamic_refund_text;
	}

	/**
	 * @return string
	 */
	public function get_text_on_booking_statement() {
		return $this->text_on_booking_statement;
	}

	private function process_global_settings() {
		$this->use_global_settings = $this->settings['use_global_settings'];
		if ( $this->use_global_settings ) {
			unset (
				$this->form_fields['merchant_id'],
				$this->form_fields['portal_id'],
				$this->form_fields['account_id'],
				$this->form_fields['key'],
				$this->form_fields['submit_cart'],
				$this->form_fields['activate_pdf_download'],
				$this->form_fields['dynamic_invoice_text'],
				$this->form_fields['dynamic_refund_text'],
				$this->form_fields['text_on_booking_statement']
			);
		}
		if ( $this->use_global_settings ) {
			$this->merchant_id               = $this->global_settings['merchant_id'];
			$this->portal_id                 = $this->global_settings['portal_id'];
			$this->account_id                = $this->global_settings['account_id'];
			$this->key                       = $this->global_settings['key'];
			$this->submit_cart               = $this->global_settings['submit_cart'];
			$this->activate_pdf_download     = $this->global_settings['activate_pdf_download'];
			$this->dynamic_invoice_text      = $this->global_settings['dynamic_invoice_text'];
			$this->dynamic_refund_text       = $this->global_settings['dynamic_refund_text'];
			$this->text_on_booking_statement = $this->global_settings['text_on_booking_statement'];
		} else {
			$this->merchant_id               = isset( $this->settings['merchant_id'] ) ? $this->settings['merchant_id'] : '';
			$this->portal_id                 = isset( $this->settings['portal_id'] ) ? $this->settings['portal_id'] : '';
			$this->account_id                = isset( $this->settings['account_id'] ) ? $this->settings['account_id'] : '';
			$this->key                       = isset( $this->settings['key'] ) ? $this->settings['key'] : '';
			$this->submit_cart               = isset( $this->settings['submit_cart'] ) ? $this->settings['submit_cart'] : '';
			$this->activate_pdf_download     = isset( $this->settings['activate_pdf_download'] ) ? $this->settings['activate_pdf_download'] : '';
			$this->dynamic_invoice_text      = isset( $this->settings['dynamic_invoice_text'] ) ? $this->settings['dynamic_invoice_text'] : '';
			$this->dynamic_refund_text       = isset( $this->settings['dynamic_refund_text'] ) ? $this->settings['dynamic_refund_text'] : '';
			$this->text_on_booking_statement = isset( $this->settings['text_on_booking_statement'] ) ? $this->settings['text_on_booking_statement'] : '';
		}
	}
}