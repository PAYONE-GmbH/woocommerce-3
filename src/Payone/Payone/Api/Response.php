<?php

namespace Payone\Payone\Api;

class Response extends DataTransfer {
	/**
	 * @return bool
	 */
	public function is_approved() {
		return $this->get( 'status' ) === 'APPROVED';
	}

	/**
	 * @return bool
	 */
	public function has_error() {
		return $this->get( 'status' ) === 'ERROR';
	}

	/**
	 * @return bool
	 */
	public function is_redirect() {
		return $this->get( 'status' ) === 'REDIRECT';
	}

	/**
	 * @return mixed|null
	 */
	public function get_redirect_url() {
		return $this->get( 'redirecturl' );
	}

	/**
	 * @return string
	 */
	public function get_error_message() {
		return __( $this->get( 'errormessage' ), 'payone' ).' ['.$this->get('errorcode').']';
	}
}