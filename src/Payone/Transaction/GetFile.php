<?php

namespace Payone\Transaction;

class GetFile extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 * @param array $data
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'getfile' );
		$this->set_data_from_gateway( $gateway );
		$this->remove( 'aid' ); // Der Request darf diesen Parameter nicht enthalten
		$this->set( 'file_type', 'SEPA_MANDATE' );
		$this->set( 'file_format', 'PDF' );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response|null
	 */
	public function execute( \WC_Order $order ) {
		$mandate_identification = $order->get_meta( '_mandate_identification' );
		if ( ! $mandate_identification ) {
			return null;
		}

		$this->set( 'file_reference', $mandate_identification );

		return $this->submit();
	}
}
