<?php

namespace Payone\Transaction;

class SepaDirectDebit extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'elv' );
		$this->set( 'iban', $_POST['direct_debit_iban'] );
		$this->set( 'bic', $_POST['direct_debit_bic'] );
		$this->set( 'bankaccount', $_POST['direct_debit_account_number'] );
		$this->set( 'bankcode', $_POST['direct_debit_bank_code'] );
		$this->set( 'bankaccountholder', $_POST['direct_debit_account_holder'] );
		$this->set( 'bankcountry', 'DE' ); // @todo Richtiges Land bestimmen
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		$this->set( 'reference', $order->get_id() );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->setPersonalDataFromOrder( $order );

		return $this->submit();
	}
}