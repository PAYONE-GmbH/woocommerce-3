<?php

namespace Payone\Transaction;

class ManageMandate extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 * @param array $data
	 */
	public function __construct( $gateway, $data ) {
		parent::__construct( 'managemandate' );
		$this->set_data_from_gateway( $gateway );
		$this->set( 'clearingtype', 'elv' );
		$this->set( 'language', 'de' ); // @todo

		$this->set( 'currency', $data['currency'] );
		$this->set( 'country', $data['country'] );
		$this->set( 'city', $data['city'] );
		$this->set( 'lastname', $data['lastname'] );
		$this->set( 'iban', $data['iban'] );
		$this->set( 'bic', $data['bic'] );
	}

	public function execute() {
		return $this->submit();
	}
}