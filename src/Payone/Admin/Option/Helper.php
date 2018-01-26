<?php

namespace Payone\Admin\Option;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

abstract class Helper {
	protected $options;

	protected function textField( $optionName, $fieldName ) {
		printf(
			'<input type="text" id="' . $fieldName . '" name="' . $optionName . '[' . $fieldName . ']" value="%s" />',
			isset( $this->options[ $fieldName ] ) ? esc_attr( $this->options[ $fieldName ] ) : ''
		);
	}

	protected function selectField( $optionName, $fieldName, $options ) {
		$selectedValue = isset( $this->options[ $fieldName ] ) ? $this->options[ $fieldName ] : '';

		echo '<select id="' . $fieldName . '" name="' . $optionName . '[' . $fieldName . ']">';
		foreach ($options as $value => $label) {
			$selected = '';
			if ($selectedValue == $value) {
				$selected = ' selected="selected"';
			}
			echo '<option value="'.esc_attr($value).'"'.$selected.'>'.esc_html($label).'</option>';
		}
		echo '</select>';
	}
}