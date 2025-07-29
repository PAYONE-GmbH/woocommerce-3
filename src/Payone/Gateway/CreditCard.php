<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class CreditCard extends RedirectGatewayBase {

	const GATEWAY_ID = 'bs_payone_creditcard';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-creditcard.png';
		$this->method_title       = 'PAYONE ' . __( 'Credit Card', 'payone-woocommerce-3' );
		$this->method_description = '';

		$this->test_transaction_classname = \Payone\Transaction\CreditCard::class;
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && count( $this->settings['cc_brands'] ) === 0 ) {
			$is_available = false;
		}

		return $is_available;
	}

	public function javascript_payone_config() {
        $baseStyle = 'width: 100%; min-height: 30px; min-width: 100px;';

		$cardnumber_css = '';
		if ( $this->get_option( 'cc_field_cardnumber_style' ) === 'custom' ) {
			$cardnumber_css = $this->get_option( 'cc_field_cardnumber_css' );
		}
        $cardholder_css = '';
        if ( $this->get_option( 'cc_field_cardholder_style' ) === 'custom' ) {
            $cardholder_css = $this->get_option( 'cc_field_cardholder_css' );
        }
		$cvc2_css = '';
		if ( $this->get_option( 'cc_field_cvc2_style' ) === 'custom' ) {
			$cvc2_css = $this->get_option( 'cc_field_cvc2_css' );
		}
		$month_css = '';
		if ( $this->get_option( 'cc_field_month_style' ) === 'custom' ) {
			$month_css = $this->get_option( 'cc_field_month_css' );
		}
		$year_css = '';
		if ( $this->get_option( 'cc_field_year_style' ) === 'custom' ) {
			$year_css = $this->get_option( 'cc_field_year_css' );
		}

		$cardpan = [
			'selector'  => 'cardpan',
			'type'      => $this->get_option( 'cc_field_cardnumber_type' ),
			'style'     => $baseStyle . $cardnumber_css,
			'size'      => $this->get_option( 'cc_field_cardnumber_length' ),
			'maxlength' => $this->get_option( 'cc_field_cardnumber_maxchars' ),
		];

		if ( $this->get_option( 'cc_field_cardnumber_iframe' ) === 'custom' ) {
			$cardpan['iframe'] = [
				'width'  => $this->get_option( 'cc_field_cardnumber_width' ),
				'height' => $this->get_option( 'cc_field_cardnumber_height' ),
			];
		}

        $cardholder = [
            'selector'  => 'cardholder',
            'type'      => $this->get_option( 'cc_field_cardholder_type' ),
            'style'     => $baseStyle . $cardholder_css,
            'size'      => $this->get_option( 'cc_field_cardholder_length' ),
            'maxlength' => $this->get_option( 'cc_field_cardholder_maxchars' ),
        ];

		$cardcvc2 = [
			'selector'  => 'cardcvc2',
			'type'      => $this->get_option( 'cc_field_cvc2_type' ),
			'style'     => $baseStyle . $cvc2_css,
			'size'      => $this->get_option( 'cc_field_cvc2_length' ),
			'maxlength' => $this->get_option( 'cc_field_cvc2_maxchars' ),
			'length'    => [
				"V" => 3,
				"M" => 3,
				"A" => 4,
				"D" => 3,
				"J" => 0,
				"O" => 3,
				"P" => 3,
				"U" => 3
			],
		];

		if ( $this->get_option( 'cc_field_cvc2_iframe' ) === 'custom' ) {
			$cardcvc2['iframe'] = [
				'width'  => $this->get_option( 'cc_field_cvc2_width' ),
				'height' => $this->get_option( 'cc_field_cvc2_height' ),
			];
		}

		$cardexpiremonth = [
			'selector'  => 'cardexpiremonth',
			'type'      => $this->get_option( 'cc_field_month_type' ),
			'style'     => $baseStyle . $month_css,
			'size'      => $this->get_option( 'cc_field_month_length' ),
			'maxlength' => $this->get_option( 'cc_field_month_maxchars' ),
		];

		if ( $this->get_option( 'cc_field_month_iframe' ) === 'custom' ) {
			$cardexpiremonth['iframe'] = [
				'width'  => $this->get_option( 'cc_field_month_width' ),
				'height' => $this->get_option( 'cc_field_month_height' ),
			];
		}

		$cardexpireyear = [
			'selector'  => 'cardexpireyear',
			'type'      => $this->get_option( 'cc_field_year_type' ),
			'style'     => $baseStyle . $year_css,
			'size'      => $this->get_option( 'cc_field_year_length' ),
			'maxlength' => $this->get_option( 'cc_field_year_maxchars' ),
		];

		if ( $this->get_option( 'cc_field_year_iframe' ) === 'custom' ) {
			$cardexpireyear['iframe'] = [
				'width'  => $this->get_option( 'cc_field_year_width' ),
				'height' => $this->get_option( 'cc_field_year_height' ),
			];
		}

		return [
			'fields'       => [
				'cardpan'         => $cardpan,
				'cardholder'      => $cardholder,
				'cardcvc2'        => $cardcvc2,
				'cardexpiremonth' => $cardexpiremonth,
				'cardexpireyear'  => $cardexpireyear,
			],
			'defaultStyle' => [
				'input'  => $this->get_option( 'cc_default_style_input' ),
				'select' => $this->get_option( 'cc_default_style_select' ),
				'iframe' => [
					'width'  => $this->get_option( 'cc_default_style_iframe_width' ),
					'height' => $this->get_option( 'cc_default_style_iframe_height' ),
				],
			],
			'error'        => $this->get_option( 'cc_error_output_active' ) ? 'errorOutput' : '',
			'language'     => $this->get_option( 'cc_error_output_language' ),
			'cardType'     => esc_attr( $this->get_option( 'cc_brands' )[0] ),
		];
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Credit Card', 'payone-woocommerce-3' ) );
		$yesno_options    = [
			'0' => __( 'No', 'payone-woocommerce-3' ),
			'1' => __( 'Yes', 'payone-woocommerce-3' ),
		];
		$type_options     = [
			'tel'      => __( 'Numeric', 'payone-woocommerce-3' ),
			'password' => __( 'Password', 'payone-woocommerce-3' ),
			'text'     => __( 'Text', 'payone-woocommerce-3' ),
			'select'   => __( 'Select', 'payone-woocommerce-3' ),
		];
		$iframe_options   = [
			'default' => __( 'Default', 'payone-woocommerce-3' ),
			'custom'  => __( 'Custom', 'payone-woocommerce-3' )
		];
		$style_options    = $iframe_options;
		$language_options = [
			'de' => __( 'German', 'payone-woocommerce-3' ),
			'en' => __( 'English', 'payone-woocommerce-3' ),
		];

		$this->form_fields['credit_card_configuration_label'] = [
			'title' => __( 'Credit card settings', 'payone-woocommerce-3' ),
			'type'  => 'title',
		];
		$this->form_fields['cc_brands']                       = [
			'title'   => __( 'Credit card brands', 'payone-woocommerce-3' ),
			'type'    => 'cc_brands',
			'options' => [
				'V' => __( 'VISA', 'payone-woocommerce-3' ),
				'M' => __( 'Mastercard', 'payone-woocommerce-3' ),
				'A' => __( 'AMEX', 'payone-woocommerce-3' ),
				'D' => __( 'Diners', 'payone-woocommerce-3' ),
				'J' => __( 'JCB', 'payone-woocommerce-3' ),
				'C' => __( 'Discover', 'payone-woocommerce-3' ),
				'B' => __( 'Carte Bleue', 'payone-woocommerce-3' ),
				'P' => __( 'China Union Pay', 'payone-woocommerce-3' ),
			],
			'default' => [],
		];
		$this->form_fields['cc_brand_label_V']                = [
			'type'    => 'no_display',
			'default' => 'VISA',
		];
		$this->form_fields['cc_brand_label_M']                = [
			'type'    => 'no_display',
			'default' => 'Mastercard',
		];
		$this->form_fields['cc_brand_label_A']                = [
			'type'    => 'no_display',
			'default' => 'American Express',
		];
		$this->form_fields['cc_brand_label_D']                = [
			'type'    => 'no_display',
			'default' => 'Diners Club',
		];
		$this->form_fields['cc_brand_label_J']                = [
			'type'    => 'no_display',
			'default' => 'Japan Credit Bureau',
		];
		$this->form_fields['cc_brand_label_C']                = [
			'type'    => 'no_display',
			'default' => 'Discover',
		];
		$this->form_fields['cc_brand_label_B']                = [
			'type'    => 'no_display',
			'default' => 'CarteBleue',
		];
		$this->form_fields['cc_brand_label_P']                = [
			'type'    => 'no_display',
			'default' => 'China Union Pay',
		];
		$this->form_fields['minimum_validity_of_card']        = [
			'title'   => __( 'Minimum validity of card', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => '30',
		];

		$this->form_fields['input_fields_configuration_label'] = [
			'title' => __( 'Configuration of input fields', 'payone-woocommerce-3' ),
			'type'  => 'title',
		];

		$this->form_fields['cc_field_cardnumber_type']     = [
			'title'   => __( 'Card number', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'numeric',
		];
		$this->form_fields['cc_field_cardnumber_length']   = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '20',
		];
		$this->form_fields['cc_field_cardnumber_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '20',
		];
		$this->form_fields['cc_field_cardnumber_iframe']   = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'select',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cardnumber_width']    = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '100px',
		];
		$this->form_fields['cc_field_cardnumber_height']   = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '20px',
		];
		$this->form_fields['cc_field_cardnumber_style']    = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'select',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cardnumber_css']      = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display', // 'text',
			'default' => '',
		];

        $this->form_fields['cc_field_cardholder_type']     = [
            'title'   => __( 'Card Holder', 'payone-woocommerce-3' ),
            'type'    => 'style_input',
            'options' => $type_options,
            'default' => 'text',
        ];
        $this->form_fields['cc_field_cardholder_length']   = [
            'title'   => __( 'Length', 'payone-woocommerce-3' ),
            'type'    => 'no_display',
            'default' => '20',
        ];
        $this->form_fields['cc_field_cardholder_maxchars'] = [
            'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
            'type'    => 'no_display',
            'default' => '20',
        ];
        $this->form_fields['cc_field_cardholder_style']    = [
            'title'   => __( 'Style', 'payone-woocommerce-3' ),
            'type'    => 'no_display',
            'options' => $style_options,
            'default' => 'default',
        ];
        $this->form_fields['cc_field_cardholder_css']      = [
            'title'   => __( 'CSS', 'payone-woocommerce-3' ),
            'type'    => 'no_display',
            'default' => '',
        ];

		$this->form_fields['cc_field_cvc2_type']     = [
			'title'   => __( 'CVC2', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'password',
		];
		$this->form_fields['cc_field_cvc2_length']   = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '4',
		];
		$this->form_fields['cc_field_cvc2_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '4',
		];
		$this->form_fields['cc_field_cvc2_iframe']   = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cvc2_width']    = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '30px',
		];
		$this->form_fields['cc_field_cvc2_height']   = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_cvc2_style']    = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_cvc2_css']      = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '',
		];

		$this->form_fields['cc_field_month_type']     = [
			'title'   => __( 'Valid month', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'select',
		];
		$this->form_fields['cc_field_month_length']   = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_month_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_month_iframe']   = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_month_width']    = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_month_height']   = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_month_style']    = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_month_css']      = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '',
		];

		$this->form_fields['cc_field_year_type']     = [
			'title'   => __( 'Valid year', 'payone-woocommerce-3' ),
			'type'    => 'style_input',
			'options' => $type_options,
			'default' => 'select',
		];
		$this->form_fields['cc_field_year_length']   = [
			'title'   => __( 'Length', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_year_maxchars'] = [
			'title'   => __( 'Max. chars', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20',
		];
		$this->form_fields['cc_field_year_iframe']   = [
			'title'   => __( 'Iframe', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $iframe_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_year_width']    = [
			'title'   => __( 'Width', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_year_height']   = [
			'title'   => __( 'Height', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '20px',
		];
		$this->form_fields['cc_field_year_style']    = [
			'title'   => __( 'Style', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'options' => $style_options,
			'default' => 'default',
		];
		$this->form_fields['cc_field_year_css']      = [
			'title'   => __( 'CSS', 'payone-woocommerce-3' ),
			'type'    => 'no_display',
			'default' => '',
		];

		$this->form_fields['cc_default_style_label'] = [
			'title' => __( 'Default style', 'payone-woocommerce-3' ),
			'type'  => 'title',
		];

		$this->form_fields['cc_default_style_input']         = [
			'title'   => __( 'Text input', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => 'font-size: 1em; border: 1px solid #000; width: 175px;',
		];
		$this->form_fields['cc_default_style_select']        = [
			'title'   => __( 'Select', 'payone-woocommerce-3' ),
			'type'    => 'text',
			'default' => 'font-size: 1em; border: 1px solid #000;',
		];
		$this->form_fields['cc_default_style_iframe_width']  = [
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

		$this->form_fields['cc_error_output_active']   = [
			'title'   => __( 'Error output active', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $yesno_options,
			'default' => '1',
		];
		$this->form_fields['cc_error_output_language'] = [
			'title'   => __( 'Error output language', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => $language_options,
			'default' => 'de',
		];
	}

	public function generate_cc_brands_html( $key, $data ) {
		$out = '<tr valign="top">';
		$out .= '<th scope="row" class="titledesc">';
		$out .= '</th><td class="forminp">';

		$selected_brands = (array) $this->get_option( $key );
		$out             .= '<details><summary>' . __( 'Credit card brands', 'payone-woocommerce-3' ) . ' ' . __( 'configuration', 'payone-woocommerce-3' ) . '</summary><div class="cc_brands_wrapper"><table class="table fixed">';

		foreach ( $data['options'] as $brand_key => $brand_label ) {
			if ( in_array( $brand_key, $selected_brands, true ) ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}

			$checkbox_id   = $this->get_field_key( $key );
			$checkbox_name = $checkbox_id . '[]';
			$out           .= '<tr><td><label>';
			$out           .= '<input type="checkbox" name="' . $checkbox_name . '" id="' . $checkbox_id . '" value="' . esc_attr( $brand_key ) . '"' . $checked . '>';
			$out           .= $brand_label . '</label></td>';

			$text_input_name = 'cc_brand_label_' . $brand_key;
			$value           = $this->get_option( $text_input_name );
			$text_input_name = $this->plugin_id . $this->id . '_' . $text_input_name;
			$out             .= '<td><input class="input-text regular-input" type="text" name="' . $text_input_name . '" id="' . $text_input_name . '" value="' . esc_attr( $value ) . '"></td>';

			$out .= '</tr>';
		}
		$out .= '</table></div></details></td></tr>';

		return $out;
	}

	public function generate_no_display_html( $key, $data ) {
	}

	public function validate_cc_brands_field( $key, $value ) {
		return $this->validate_multiselect_field( $key, (array) $value );
	}

	public function generate_style_input_html( $key, $data ) {
		if ( preg_match( '/cc_field_(.*)_/', $key, $matches ) ) {
			$field = $matches[1];
		} else {
			return '';
		}

		// Werden für die Übersetzung benötigt
		$out = '<tr valign="top">';
		$out .= '<th scope="row" class="titledesc">';
		$out .= '</th><td class="forminp"><details><summary>' . ' "' . $data['title'] . '" ' . __( 'configuration', 'payone-woocommerce-3' ) . '</summary><table><tr>';

		$out .= '<td>' . $this->generate_select_html_without_table_markup( $key, $data, __( 'Type', 'payone-woocommerce-3' ) ) . '</td>';

		$key  = 'cc_field_' . $field . '_length';
		$data = $this->form_fields[ $key ];
		$out  .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$key  = 'cc_field_' . $field . '_maxchars';
		$data = $this->form_fields[ $key ];
		$out  .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$out .= '</tr><tr>';

		$key  = 'cc_field_' . $field . '_iframe';
		$data = $this->form_fields[ $key ];
        if (!empty($data)) {
		    $out  .= '<td>' . $this->generate_select_html_without_table_markup( $key, $data ) . '</td>';
        }

		$key  = 'cc_field_' . $field . '_width';
		$data = $this->form_fields[ $key ];
        if (!empty($data)) {
            $out .= '<td>' . $this->generate_text_html_without_table_markup($key, $data) . '</td>';
        }

		$key  = 'cc_field_' . $field . '_height';
		$data = $this->form_fields[ $key ];
        if (!empty($data)) {
            $out .= '<td>' . $this->generate_text_html_without_table_markup($key, $data) . '</td>';
        }

		$out .= '</tr><tr>';

		$key  = 'cc_field_' . $field . '_style';
		$data = $this->form_fields[ $key ];
		$out  .= '<td>' . $this->generate_select_html_without_table_markup( $key, $data ) . '</td>';

		$key  = 'cc_field_' . $field . '_css';
		$data = $this->form_fields[ $key ];
		$out  .= '<td>' . $this->generate_text_html_without_table_markup( $key, $data ) . '</td>';

		$out .= '</tr></table></details></td></tr>';

		return $out;
	}

	public function payment_fields() {
		$options = [
			'mode'        => $this->get_mode(),
			'merchant_id' => $this->get_merchant_id(),
			'account_id'  => $this->get_account_id(),
			'portal_id'   => $this->get_portal_id(),
			'key'         => $this->get_key(),
		];
		$hash    = $this->calculate_hash( $options );

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
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

	/**
	 * @param array $options
	 *
	 * @return string
	 */
	public function calculate_hash( $options ) {
		return hash_hmac(
			'sha384',
			$options['account_id']
			. 'UTF-8'
			. $options['merchant_id']
			. $options['mode']
			. $options['portal_id']
			. 'creditcardcheck'
			. 'JSON'
			. 'yes',
			$options['key']
		);
	}
}
