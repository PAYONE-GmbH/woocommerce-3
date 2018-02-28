<?php

namespace Payone\Payone\Api;

class DataTransfer {
	/**
	 * @var array
	 */
	private $parameter_bag;

	/**
	 * @todo Alle Felder herausfinden
	 *
	 * @var array
	 */
	private $fields_to_anonymize = [
		'cardpan' => [ 4, 4 ],
		'iban'    => [ 4, 3 ],
		'street'  => [ 1, 1 ],
	];

	/**
	 * DataTransfer constructor.
	 *
	 * @param array|null $parameter_bag
	 */
	public function __construct( $parameter_bag = null ) {
		$this->clear();

		if ( $parameter_bag !== null && is_array( $parameter_bag ) ) {
			$this->parameter_bag = $parameter_bag;
		}
	}

	/**
	 * @param string $json_data
	 *
	 * @return DataTransfer
	 */
	public static function construct_from_json( $json_data ) {
		$dataTransfer = new DataTransfer();
		$dataTransfer->unserialize_parameters( $json_data );

		return $dataTransfer;
	}

	/**
	 * @param array $data
	 *
	 * @return DataTransfer
	 */
	public static function constructFromArray( $data ) {
		return new DataTransfer( $data );
	}

	public function clear() {
		$this->parameter_bag = [];
	}

	/**
	 * @todo Wenn ein $key erneut gesetzt wird, kann es auch sein, dass ein Array gespeichert wird.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param DataTransfer
	 *
	 * @return mixed
	 */
	public function set( $key, $value ) {
		$this->parameter_bag[ $key ] = $value;

		return $this;
	}

	/**
	 * @param string $key
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public function get( $key, $default = null ) {
		if ( array_key_exists( $key, $this->parameter_bag ) ) {
			return $this->parameter_bag[ $key ];
		}

		return $default;
	}

	/**
	 * @param string $key
	 * @param float $default
	 *
	 * @return float
	 */
	public function get_float( $key, $default = 0.0 ) {
		return (float) $this->get( $key, $default );
	}

	public function getAll() {
		return $this->parameter_bag;
	}

	/**
	 * @return string
	 */
	public function get_postfields_from_parameters() {
		return http_build_query( $this->parameter_bag );
	}

	public function get_serialized_parameters() {
		return json_encode( $this->parameter_bag );
	}

	public function unserialize_parameters( $serialized ) {
		$this->parameter_bag = json_decode( $serialized, true );
	}

	public function anonymize_parameters() {
		foreach ( $this->parameter_bag as $key => $value ) {
			$this->parameter_bag[ $key ] = $this->anonymize( $key, $value );
		}
	}

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	private function anonymize( $key, $value ) {
		$anonymization_rule = isset( $this->fields_to_anonymize[ $key ] ) ? $this->fields_to_anonymize[ $key ] : null;

		if ( $anonymization_rule ) {
			$number_first_characters = $anonymization_rule[0];
			$number_last_characters  = $anonymization_rule[1];

			$value = substr( $value, 0, $number_first_characters )
			         . str_repeat( 'x', strlen( $value ) - $number_first_characters - $number_last_characters )
			         . substr( $value, - $number_last_characters );
		}

		return $value;
	}
}