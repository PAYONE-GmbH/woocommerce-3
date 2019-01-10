<?php

namespace Payone\Payone\Api;

use Payone\Plugin;

class DataTransfer {
    const META_KEY_PAYONE_REFERENCE = '_payone_reference';

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
     * @param \WC_Order $order
     *
     * @return $this
     */
	public function set_reference( \WC_Order $order ) {
        $reference = $order->get_meta( self::META_KEY_PAYONE_REFERENCE );
        if ( ! $reference ) {
            $reference = Plugin::sanitize_reference( $order->get_order_number() );
            $order->update_meta_data( self::META_KEY_PAYONE_REFERENCE, $reference );
        }

        $this->set( 'reference', $reference );

        return $this;
    }

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function remove( $key ) {
		unset( $this->parameter_bag[ $key ] );

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
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return array_key_exists( $key, $this->parameter_bag );
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

	/**
	 * @param string $key
	 * @param int $default
	 *
	 * @return float
	 */
	public function get_int( $key, $default = 0 ) {
		return (int) $this->get( $key, $default );
	}

	public function get_all() {
		return $this->parameter_bag;
	}

	/**
	 * @return string
	 */
	public function get_postfields_from_parameters() {
		return http_build_query( self::remove_empty_parameters( $this->parameter_bag ) );
	}

	public function get_serialized_parameters() {
		if ( $this->has( '_DATA' ) ) {
			// Der Wert wird sonst zu lang, um im api_log abgespeichert zu werden. Und es muss auch nicht ein ganzes
			// PDF im Logfile landen.
			$parameter_bag = $this->parameter_bag;
			$parameter_bag[ '_DATA' ] = substr( $parameter_bag[ '_DATA' ], 0, 200 );

			return json_encode( $parameter_bag );
		}

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

    /**
     * @param array $parameters
     *
     * @return array
     */
	private static function remove_empty_parameters($parameters) {
	    $clearedParameters = [];
	    foreach ($parameters as $key => $value) {
	        if ($value !== null && $value !== '') {
	            $clearedParameters[$key] = $value;
            }
        }

        return $clearedParameters;
    }

    /**
     * @param string $reference
     *
     * @return int
     */
	protected static function get_order_id_for_reference( $reference ) {
        $args = array(
            'meta_key' => self::META_KEY_PAYONE_REFERENCE,
            'meta_value' => $reference,
            'post_type' => 'shop_order',
            'post_status' => 'any',
        );
        $posts = get_posts( $args );
        if ( count( $posts ) === 1 ) {
            $post = array_shift($posts);

            return $post->ID;
        }

        // Es wurde keine Order gefunden. Wir gehen jetzt davon aus, dass es sich bei $reference um eine Order-ID
        // handelt, die vor der Einführung von META_KEY_PAYONE_REFERENCE angelegt wurde. Um sicher zu gehen,
        // wird nun geprüft, ob die Order bereits einen Wert für META_KEY_PAYONE_REFERENCE hat. Nur wenn dem nicht
        // so ist, wird $reference als Order-ID zurück gegeben.
        if ( get_post_meta( $reference, self::META_KEY_PAYONE_REFERENCE ) ) {
            return 0;
        }

        return $reference;
    }
}