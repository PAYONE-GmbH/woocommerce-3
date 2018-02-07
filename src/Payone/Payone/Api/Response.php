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
	 * @return string
	 */
	public function get_error_message() {
		return __( $this->get( 'errormessage' ), 'payone' ).' ['.$this->get('errorcode').']';
	}
}