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
}