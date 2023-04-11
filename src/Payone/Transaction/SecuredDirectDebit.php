<?php

namespace Payone\Transaction;

class SecuredDirectDebit extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'fnc' );
		$this->set( 'financingtype', 'PDD' );
		$this->set( 'add_paydata[device_token]', $_POST['payone_secured_direct_debit_token'] );
		$this->set( 'birthday', Base::convert_birthday( $_POST['payone_secured_direct_debit_birthday'] ) );
		$this->set( 'iban', $_POST['payone_secured_direct_debit_iban'] );
		$this->set( 'businessrelation', 'b2c' );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		$this->add_article_list_to_transaction( $order );
		$this->set_reference( $order );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );
		$this->set_shipping_data_from_order( $order );
		$this->set_customer_ip_from_order( $order );

		$this->set( 'bankaccountholder', $this->get( 'firstname' ) . ' ' . $this->get( 'lastname' ) );

		return $this->submit();
	}
}
