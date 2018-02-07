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
		$name = $optionName . '[' . $fieldName . ']';
		if ($type === 'multiple') {
			$multiple = ' multiple="multiple"';
			$name .= '[]';
		}

		echo '<select id="' . $fieldName . '" name="' . $name .'"'.$multiple.'>';
		foreach ($options as $value => $label) {
			$selected = false;
			if (is_array($selectedValue) && $type === 'multiple') {
				if (in_array($value, $selectedValue)) {
					$selected = true;
				}
			} elseif ($selectedValue == $value) {
				$selected = true;
			}

			if ($selected) {
				$selected = ' selected="selected"';
			}
			echo '<option value="'.esc_attr($value).'"'.$selected.'>'.esc_html($label).'</option>';
		}
		echo '</select>';
	}
}