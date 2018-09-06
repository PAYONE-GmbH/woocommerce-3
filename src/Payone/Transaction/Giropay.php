<?php

namespace Payone\Transaction;

class Giropay extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'clearingtype', 'sb' );
		$this->set( 'onlinebanktransfertype', 'GPY' );
		$this->set( 'iban', isset( $_POST['giropay_iban'] ) ? $_POST['giropay_iban'] : '' );
		$this->set( 'bic', isset( $_POST['giropay_bic'] ) ? $_POST['giropay_bic'] : '' );
		$this->set( 'bankcountry', 'DE' ); // @todo Richtiges Land bestimmen
	}

	/**
	 * @param \WC_Order $order
	 */
	public function add_execution_parameters_for_order( \WC_Order $order ) {
		$this->add_callback_urls( $order );
	}
}