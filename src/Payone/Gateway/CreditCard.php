<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class CreditCard extends RedirectGatewayBase {
	const GATEWAY_ID = 'bs_payone_creditcard';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'Payone ' . __( 'Creditcard', 'payone-woocommerce-3' );;
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Creditcard', 'payone-woocommerce-3' ) );
		$yesno_options = [
			'0' => __( 'No', 'payone-woocommerce-3' ),
			'1' => __( 'Yes', 'payone-woocommerce-3' ),
		];
		$type_options = [
			'tel' => __( 'Numeric', 'payone-woocommerce-3' ),
			'password' => __( 'Password', 'payone-woocommerce-3' ),
			'text' => __( 'Text', 'payone-woocommerce-3' ),
			'select' => __( 'Select', 'payone-woocommerce-3' ),
		];
		$iframe_options = [
			'default' => __( 'Default', 'payone-woocommerce-3' ),
			'custom' => __( 'Custom', 'payone-woocommerce-3' )
		];
		$style_options = $iframe_options;
		$language_options = [
			'de' => __( 'German', 'payone-woocommerce-3' ),
			'en' => __( 'English', 'payone-woocommerce-3' ),
		];

		$this->form_fields['cc_brands'] = [
			'title'   => __( 'Credit card brands', 'payone-woocommerce-3' ),
			'type'    => 'cc_brands',
			'options' => [
				'V' => __( 'VISA', 'payone-woocommerce-3' ),
				'M' => __( 'Mastercard', 'payone-woocommerce-3' ),
				'A' => __( 'AmEX', 'payone-woocommerce-3' ),
				'D' => __( 'Diners', 'payone-woocommerce-3' ),
				'J' => __( 'JCB', 'payone-woocommerce-3' ),
				'O' => __( 'Maestro', 'payone-woocommerce-3' ),
				'C' => __( 'Discover', 'payone-woocommerce-3' ),
				'B' => __( 'Carte Bleue', 'payone-woocommerce-3' ),
				'P' => __( 'China Union Pay', 'payone-woocommerce-3' ),
			],
			'default' => ['V', 'M', 'A', 'D', 'J', 'O', 'C', 'B', 'P'],
		];
		$this->form_fields['cc_brand_label_V'] = [
			'type'    => 'no_display',
			'default' => 'VISA',
		];
		$this->form_fields['cc_brand_label_M'] = [
			'type'    => 'no_display',
			'default' => 'Mastercard',
		];
		$this->form_fields['cc_brand_label_A'] = [
			'type'    => 'no_display',
			'default' => 'American Express',
		];
		$this->form_fields['cc_brand_label_D'] = [
			'type'    => 'no_display',
			'default' => 'Diners Club',
		];
		$this->form_fields['cc_brand_label_J'] = [
			'type'    => 'no_display',
			'default' => 'Japan Credit Bureau',
		];
		$this->form_fields['cc_brand_label_O'] = [
			'type'    => 'no_display',
			'default' => 'Maestro International',
		];
		$this->form_fields['cc_brand_label_C'] = [
			'type'    => 'no_display',
			'default' => 'Discover',
		];
		$this->form_fields['cc_brand_label_B'] = [
			'type'    => 'no_display',
			'default' => 'CarteBleue',
		];
		$this->form_fields['cc_brand_label_P'] = [
			'type'    => 'no_display',
			'default' => 'China Union Pay',
		];
		$this->form_fields['ask_for_cvc2'] = [
			'title'   => __( 'Ask for CVC2', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $yesno_options,
			'default' => '1',
		];
		$this->form_fields['minimum_validity_of_card'] = [
			'title'   => __( 'Minimum validity of card', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => '30',
		];

		$this->form_fields['input_fields_configuration_label'] = [
			'title' => __( 'Configuration of input fields', 'payone-woocommerce-3' ),
			'type'  => 'title',
		];

		$this->form_fields['cc_field_cardnumber_type'] = [
			'title'   => __( 'Card number', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'numeric',
		];
		$this->form_fields['cc_field_cardnumber_length'] = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '20',
		];
		$this->form_fields['cc_field_cardnumber_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '20',
		];
		$this->form_fields['cc_field_cardnumber_iframe'] = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'select',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cardnumber_width'] = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '100px',
		];
		$this->form_fields['cc_field_cardnumber_height'] = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '20px',
		];
		$this->form_fields['cc_field_cardnumber_style'] = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'select',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cardnumber_css'] = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '',
		];

		$this->form_fields['cc_field_cvc2_type'] = [
			'title'   => __( 'CVC2', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'password',
		];
		$this->form_fields['cc_field_cvc2_length'] = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '4',
		];
		$this->form_fields['cc_field_cvc2_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '4',
		];
		$this->form_fields['cc_field_cvc2_iframe'] = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cvc2_width'] = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '30px',
		];
		$this->form_fields['cc_field_cvc2_height'] = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_cvc2_style'] = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cvc2_css'] = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '',
		];

		$this->form_fields['cc_field_month_type'] = [
			'title'   => __( 'Valid month', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'select',
		];
		$this->form_fields['cc_field_month_length'] = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_month_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_month_iframe'] = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_month_width'] = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_month_height'] = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_month_style'] = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_month_css'] = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '',
		];

		$this->form_fields['cc_field_year_type'] = [
			'title'   => __( 'Valid year', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'select',
		];
		$this->form_fields['cc_field_year_length'] = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_year_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_year_iframe'] = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_year_width'] = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_year_height'] = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_year_style'] = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_year_css'] = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '',
		];

		$this->form_fields['cc_default_style_label'] = [
			'title' => __( 'Default style', 'payone-woocommerce-3' ),
			'type'  => 'title',
		];

		$this->form_fields['cc_default_style_input'] = [
			'title'   => __( 'Text input', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => 'font-size: 1em; border: 1px solid #000; width: 175px;',
		];
		$this->form_fields['cc_default_style_select'] = [
			'title'   => __( 'Select', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => 'font-size: 1em; border: 1px solid #000;',
		];
		$this->form_fields['cc_default_style_iframe_width'] = [
			'title'   => __( 'Iframe width', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => '180px',
		];
		$this->form_fields['cc_default_style_iframe_height'] = [
			'title'   => __( 'Iframe height', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => '33px',
		];

		$this->form_fields['cc_error_output_label'] = [
			'title' => __( 'Error output', 'payone-woocommerce-3' ),
			'type'  => 'title',
		];

		$this->form_fields['cc_error_output_active'] = [
			'title'   => __( 'Error output active', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $yesno_options,
			'default' => '1',
		];
		$this->form_fields['cc_error_output_language'] = [
			'title'   => __( 'Error output active', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $language_options,
			'default' => 'de',
		];
	}

	public function generate_cc_brands_html( $key, $data ) {
		$out =  '<tr valign="top">';
		$out .= '<th scope="row" class="titledesc">';
		$out .= '<label>' . __( 'Credit card brand', 'payone-woocommerce-3' ) . '</label>';
		$out .= '</th><td class="forminp">';

		$selected_brands = (array)$this->get_option( $key );
		foreach ( $data[ 'options' ] as $brand_key => $brand_label ) {
			$out .= '<div class="cc_brands_wrapper">';

			if ( in_array( $brand_key, $selected_brands, true ) ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}

			$checkbox_id = $this->get_field_key($key);
			$checkbox_name = $checkbox_id . '[]';
			$out .= '<label for="' . $checkbox_id . '">';
			$out .= '<input type="checkbox" name="'. $checkbox_name . '" id="'. $checkbox_id . '" value="' . esc_attr( $brand_key  ) . '"'. $checked . '>';
			$out .=  $brand_label . '</label>';

			$text_input_name = 'cc_brand_label_' . $brand_key;
			$value = $this->get_option( $text_input_name );
			$text_input_name = $this->plugin_id . $this->id . '_' . $text_input_name;
			$out .= '<input class="input-text regular-input" type="text" name="' . $text_input_name . '" id="' . $text_input_name . '" value="' . esc_attr( $value ). '">';

			$out .= '</div>';
		}
		$out .= '</td></tr>';

		return $out;
	}

	public function generate_no_display_html( $key, $data ) {}

	public function validate_cc_brands_field( $key, $value  ) {
		return $this->validate_multiselect_field( $key, (array) $value );
	}

	public function generate_style_input_html( $key, $data ) {
		if ( preg_match( '/cc_field_(.*)_/', $key, $matches ) ) {
			$field = $matches[ 1 ];
		} else {
			return '';
		}

		// Werden für die Übersetzung benötigt
		$labels = [
			'cardnumber' => __( 'style.input.label.cardnumber', 'payone-woocommerce-3' ),
			'cvc2'       => __( 'style.input.label.cvc2', 'payone-woocommerce-3' ),
			'month'      => __( 'style.input.label.month', 'payone-woocommerce-3' ),
			'year'       => __( 'style.input.label.year', 'payone-woocommerce-3' ),
		];
		$out =  '<tr valign="top">';
		$out .= '<th scope="row" class="titledesc">';
		$out .= '<label>' . $labels[ $field ] . '</label>';
		$out .= '</th><td class="forminp"><table><tr>';
		$out .= '<th>' . __( 'Type' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '<th>' . __( 'Length' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '<th>' . __( 'Max. Chars' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '</tr><tr>';

		$out .= '<td>' . $this->generate_select_html_without_table_markup( $key, $data ) . '</td>';

		$key = 'cc_field_' . $field . '_length';
		$data = $this->form_fields[$key];
		$out .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$key = 'cc_field_' . $field . '_maxchars';
		$data = $this->form_fields[$key];
		$out .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$out .= '</tr><tr><th>' . __( 'Iframe' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '<th>' . __( 'Width' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '<th>' . __( 'Height' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '</tr><tr>';

		$key = 'cc_field_' . $field . '_iframe';
		$data = $this->form_fields[$key];
		$out .= '<td>' . $this->generate_select_html_without_table_markup( $key, $data ) . '</td>';

		$key = 'cc_field_' . $field . '_width';
		$data = $this->form_fields[$key];
		$out .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$key = 'cc_field_' . $field . '_height';
		$data = $this->form_fields[$key];
		$out .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$out .= '</tr><tr><th>' . __( 'Style' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '<th>' . __( 'CSS' , 'payone-woocommerce-3' ) . '</th>';
		$out .= '</tr><tr>';

		$key = 'cc_field_' . $field . '_style';
		$data = $this->form_fields[$key];
		$out .= '<td>' . $this->generate_select_html_without_table_markup( $key, $data ) . '</td>';

		$key = 'cc_field_' . $field . '_css';
		$data = $this->form_fields[$key];
		$out .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$out .= '</tr></table></td></tr>';

		return $out;
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
		$hash    = $this->calculate_hash( $options );

		include PAYONE_VIEW_PATH . '/gateway/creditcard/payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\CreditCard::class );
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->no_further_action_necessary() ) {
			return;
		}

		$order = $transaction_status->get_order();
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'authorization' && $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_capture() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
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

	/**
	 * @param array $options
	 *
	 * @return string
	 */
	public function calculate_hash( $options ) {
		return md5(
			$options['account_id']
			. 'UTF-8'
			. $options['merchant_id']
			. $options['mode']
			. $options['portal_id']
			. 'creditcardcheck'
			. 'JSON'
			. 'yes'
			. $options['key']
		);
	}
}