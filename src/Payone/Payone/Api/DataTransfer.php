<?php

namespace Payone\Payone\Api;

class DataTransfer {
	/**
	 * @var array
	 */
	private $parameterBag;

	/**
	 * @todo Alle Felder herausfinden
	 *
	 * @var array
	 */
	private $fieldsToAnonymize = [
		'cardpan' => [ 4, 4 ],
		'iban'    => [ 4, 3 ],
		'street'  => [ 1, 1 ],
	];

	/**
	 * DataTransfer constructor.
	 *
	 * @param array|null $parameterBag
	 */
	public function __construct($parameterBag = null) {
		$this->clear();

		if ($parameterBag !== null && is_array($parameterBag)) {
			$this->parameterBag = $parameterBag;
		}
	}

	/**
	 * @param string $jsonData
	 *
	 * @return DataTransfer
	 */
	public static function constructFromJson( $jsonData ) {
		$dataTransfer = new DataTransfer();
		$dataTransfer->unserializeParameters( $jsonData );

		return $dataTransfer;
	}

	/**
	 * @param array $data
	 *
	 * @return DataTransfer
	 */
	public static function constructFromArray( $data ) {
		return new DataTransfer($data);
	}

	public function clear() {
		$this->parameterBag = [];
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
	public function set( $key, $value) {
		$this->parameterBag[ $key ] = $value;

		return $this;
	}

	public function get( $key ) {
		if ( array_key_exists( $key, $this->parameterBag ) ) {
			return $this->parameterBag[ $key ];
		}

		return null;
	}

	public function getAll() {
		return $this->parameterBag;
	}

	/**
	 * @return string
	 */
	public function getPostfieldsFromParameters() {
		return http_build_query( $this->parameterBag );
	}

	public function getSerializedParameters() {
		return json_encode( $this->parameterBag );
	}

	public function unserializeParameters( $serialized ) {
		$this->parameterBag = json_decode( $serialized, true );
	}

	public function anonymizeParameters() {
		foreach ( $this->parameterBag as $key => $value ) {
			$this->parameterBag[ $key ] = $this->anonymize( $key, $value );
		}
	}

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	private function anonymize( $key, $value ) {
		$anonymizationRule = isset( $this->fieldsToAnonymize[ $key ] ) ? $this->fieldsToAnonymize[ $key ] : null;

		if ( $anonymizationRule ) {
			$numberFirstCharacters = $anonymizationRule[0];
			$numberLastCharacters  = $anonymizationRule[1];

			$value = substr( $value, 0, $numberFirstCharacters )
			         . str_repeat( 'x', strlen( $value ) - $numberFirstCharacters - $numberLastCharacters )
			         . substr( $value, - $numberLastCharacters );
		}

		return $value;
	}
}