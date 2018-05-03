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
		return __( $this->get( 'errormessage' ), 'payone-woocommerce-3' ).' ['.$this->get('errorcode').']';
	}

	/**
	 * @param \WC_Order $order
	 */
	public function store_clearing_info( \WC_Order $order ) {
		$clearing_reference = $this->get( 'clearing_reference', $order->get_transaction_id() );
		$clearing_info = [
			'bankaccount'       => $this->get( 'clearing_bankaccount' ),
			'bankcode'          => $this->get( 'clearing_bankcode' ),
			'bankcountry'       => $this->get( 'clearing_bankcountry' ),
			'bankname'          => $this->get( 'clearing_bankname' ),
			'bankaccountholder' => $this->get( 'clearing_bankaccountholder' ),
			'bankcity'          => $this->get( 'clearing_bankcity' ),
			'bankiban'          => $this->get( 'clearing_bankiban' ),
			'bankbic'           => $this->get( 'clearing_bankbic' ),
			'reference'         => $clearing_reference,
		];
		$order->update_meta_data( '_clearing_info', json_encode( $clearing_info ) );
	}
}