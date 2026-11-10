<?php

namespace Payone\Admin\Option;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

abstract class Helper {
	protected $options;

	protected function textField( $optionName, $fieldName, $default = '' ) {
		printf(
			'<input type="text" id="' . $fieldName . '" name="' . $optionName . '[' . $fieldName . ']" value="%s" />',
			isset( $this->options[ $fieldName ] ) ? esc_attr( $this->options[ $fieldName ] ) : $default
		);
	}

	protected function selectField( $optionName, $fieldName, $options, $type = 'single' ) {
		$selectedValue = isset( $this->options[ $fieldName ] ) ? $this->options[ $fieldName ] : '';

		$multiple = '';
		$name     = $optionName . '[' . $fieldName . ']';
		if ( $type === 'multiple' ) {
			$multiple = ' multiple="multiple"';
			$name     .= '[]';
		}

		echo '<select id="' . $fieldName . '" name="' . $name . '"' . $multiple . '>';
		foreach ( $options as $value => $label ) {
			$selected = false;
			if ( is_array( $selectedValue ) && $type === 'multiple' ) {
				if ( in_array( $value, $selectedValue ) ) {
					$selected = true;
				}
			} elseif ( $selectedValue == $value ) {
				$selected = true;
			}

			if ( $selected ) {
				$selected = ' selected="selected"';
			}
			echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * On/off switch. The stored value stays "0" or "1".
	 */
	protected function switchField( $optionName, $fieldName ) {
		$name    = $optionName . '[' . $fieldName . ']';
		$checked = isset( $this->options[ $fieldName ] ) && (string) $this->options[ $fieldName ] === '1';

		echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0">';
		echo '<input type="checkbox" class="payone-switch" id="' . esc_attr( $fieldName ) . '" name="' . esc_attr( $name ) . '" value="1"' . ( $checked ? ' checked="checked"' : '' ) . '>';
	}

	/**
	 * Few options side by side, e.g. test / live. The first option is preselected.
	 */
	protected function segmentedField( $optionName, $fieldName, $options ) {
		$selectedValue = isset( $this->options[ $fieldName ] ) ? (string) $this->options[ $fieldName ] : '';
		if ( ! isset( $options[ $selectedValue ] ) ) {
			$keys          = array_keys( $options );
			$selectedValue = (string) $keys[0];
		}

		echo '<div class="payone-segmented" role="radiogroup" id="' . esc_attr( $fieldName ) . '">';
		foreach ( $options as $value => $label ) {
			echo '<label><input type="radio" name="' . esc_attr( $optionName . '[' . $fieldName . ']' ) . '" value="' . esc_attr( $value ) . '"'
			     . ( (string) $value === $selectedValue ? ' checked="checked"' : '' ) . '><span>' . esc_html( $label ) . '</span></label>';
		}
		echo '</div>';
	}

	/**
	 * Text field whose value is hidden until the user reveals it.
	 */
	protected function secretField( $optionName, $fieldName ) {
		$value = isset( $this->options[ $fieldName ] ) ? $this->options[ $fieldName ] : '';

		echo '<div class="payone-secret">';
		echo '<input type="password" id="' . esc_attr( $fieldName ) . '" name="' . esc_attr( $optionName . '[' . $fieldName . ']' ) . '" value="' . esc_attr( $value ) . '" autocomplete="off" spellcheck="false">';
		echo '<button type="button" class="button payone-secret__toggle" aria-pressed="false" data-target="' . esc_attr( $fieldName ) . '"'
		     . ' data-show="' . esc_attr__( 'Show', 'payone-woocommerce-3' ) . '" data-hide="' . esc_attr__( 'Hide', 'payone-woocommerce-3' ) . '">'
		     . esc_html__( 'Show', 'payone-woocommerce-3' ) . '</button>';
		echo '</div>';
	}
}
