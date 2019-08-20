<?php

namespace Payone\Transaction;

class SepaDirectDebit extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'mandate_identification', $_POST['direct_debit_reference'] );
		$this->set( 'clearingtype', 'elv' );
		$this->set( 'iban', $_POST['direct_debit_iban'] );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		if ($this->should_submit_cart() ) {
			$this->add_article_list_to_transaction( $order );
		}
		$this->set_reference( $order );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );
        $this->set_shipping_data_from_order( $order );
        $this->set_customer_ip_from_order( $order );

		return $this->submit();
	}
}
