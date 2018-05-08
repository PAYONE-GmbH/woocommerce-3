<?php

namespace Payone\Payone\Api;

class Request extends DataTransfer {
	const API_URL = 'https://api.pay1.de/post-gateway/';
	const SOLUTION_NAME = 'payone-woocommerce-3';
	const INTEGRATOR_NAME = 'woocommerce';

	private $api_log_enabled = false;

	public function __construct() {
		parent::__construct();

		$options = get_option( 'payone_account',
			[
				'account_id'      => '',
				'merchant_id'     => '',
				'portal_id'       => '',
				'key'             => '',
				'mode'            => 'test',
				'api_log'         => 0,
				'transaction_log' => 0,
			] );

		$this->api_log_enabled = $options['api_log'] ? true : false;

		$this
			->set( 'api_version', '3.10' )
			->set_mode( $options['mode'] )
			->set_account_id( $options['account_id'] )
			->set_merchant_id( $options['merchant_id'] )
			->set_portal_id( $options['portal_id'] )
			->set_key( $options['key'] )
			->set( 'encoding', 'UTF-8' )
			->set( 'solution_name', self::SOLUTION_NAME )
			->set( 'solution_version', PAYONE_PLUGIN_VERSION )
			->set( 'integrator_name', self::INTEGRATOR_NAME )
			->set( 'integrator_version', $this->get_woocommerce_version_number() );
	}

	public function set_parameters( $key_values ) {
		foreach ( $key_values as $key => $value ) {
			$this->set( $key, $value );
		}
	}

	/**
	 * @return Response
	 */
	public function submit() {
		$ch = curl_init( self::API_URL );
		curl_setopt_array( $ch,
			[
				CURLOPT_CUSTOMREQUEST  => 'POST',
				CURLOPT_POST           => 1,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS     => $this->get_postfields_from_parameters(),
			] );

		$result = curl_exec( $ch );

		curl_close( $ch );

		$response = $this->create_response( $result );
		if ( $this->api_log_enabled ) {
			$log_entry = $this->create_log_entry( $this, $response );
			$log_entry->save();
		}

		return $response;
	}

	/**
	 * @return string
	 */
	public function get_account_id() {
		return $this->get( 'aid' );
	}

	/**
	 * @param string $accountId
	 *
	 * @return Request
	 */
	public function set_account_id( $accountId ) {
		$this->set( 'aid', $accountId );

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_merchant_id() {
		return $this->get( 'mid' );
	}

	/**
	 * @param string $merchantId
	 *
	 * @return Request
	 */
	public function set_merchant_id( $merchantId ) {
		$this->set( 'mid', $merchantId );

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_portal_id() {
		return $this->get( 'portalid' );
	}

	/**
	 * @param string $portalId
	 *
	 * @return Request
	 */
	public function set_portal_id( $portalId ) {
		$this->set( 'portalid', $portalId );

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->get( 'key' );
	}

	/**
	 * @param string $key
	 *
	 * @return Request
	 */
	public function set_key( $key ) {
		$this->set( 'key', hash( 'md5', $key ) );

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_api_version() {
		return $this->get( 'api_version' );
	}

	/**
	 * @param string $apiVersion
	 *
	 * @return Request
	 */
	public function set_api_version( $apiVersion ) {
		$this->set( 'api_version', $apiVersion );

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_mode() {
		return $this->get( 'mode' );
	}

	/**
	 * @param string $mode
	 *
	 * @return Request
	 */
	public function set_mode( $mode ) {
		$this->set( 'mode', $mode );

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_encoding() {
		return $this->get( 'encoding' );
	}

	/**
	 * @param string $encoding
	 *
	 * @return Request
	 */
	public function set_encoding( $encoding ) {
		$this->set( 'encoding', $encoding );

		return $this;
	}

	/**
	 * @param string $result
	 *
	 * @return Response
	 */
	public function create_response( $result ) {
		$response = new Response();

		if ( stripos( $result, '%PDF' ) === 0 ) {
			$response->set( 'status', 'OK' );
			$response->set( '_DATA', utf8_encode( $result ) );
		} else {
			$lines = explode( "\n", $result );
			foreach ( $lines as $line ) {
				$equal_sign = strpos( $line, '=' );
				$key        = substr( $line, 0, $equal_sign );
				$value      = substr( $line, $equal_sign + 1 );

				if ( $key ) {
					$response->set( $key, $value );
				}
			}
		}

		return $response;
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 *
	 * @return Log
	 */
	private function create_log_entry( $request, $response ) {
		$logEntry = new Log();
		$logEntry
			->set_request( $request )
			->set_response( $response );

		return $logEntry;
	}

	/**
	 * From: https://wpbackoffice.com/get-current-woocommerce-version-number/
	 *
	 * @return string|null
	 */
	private function get_woocommerce_version_number() {
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file   = 'woocommerce.php';

		// If the plugin version number is set, return it
		if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
			return $plugin_folder[ $plugin_file ]['Version'];
		}

		return null;
	}
}