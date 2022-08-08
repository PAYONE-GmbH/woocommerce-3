<?php

namespace Payone\Transaction;

use Payone\Gateway\RatepayBase;

class RatepayInstallments extends Base {
    /**
     * @var RatepayBase
     */
    private $gateway;

	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
     * @param string $authorization_method
	 */
	public function __construct( $gateway  ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

        $this->set( 'clearingtype', 'fnc' );
        $this->set( 'financingtype', $gateway->get_financingtype() );
        $this->set( 'add_paydata[debit_paytype]', 'DIRECT-DEBIT' );
        $this->set( 'add_paydata[customer_allow_credit_inquiry]', 'yes' );
        $this->set( 'add_paydata[merchant_consumer_id]',  WC()->customer->get_id() );
        $this->set( 'birthday', RatepayBase::convert_birthday( $_POST['ratepay_installments_birthday'] ) );
        $this->set( 'iban', $_POST['ratepay_installments_iban'] );

        $this->set( 'add_paydata[installment_amount]', $_POST['ratepay_installments_installment_amount'] );
        $this->set( 'add_paydata[installment_number]', $_POST['ratepay_installments_installment_number'] );
        $this->set( 'add_paydata[last_installment_amount]', $_POST['ratepay_installments_last_installment_amount'] );
        $this->set( 'add_paydata[interest_rate]', $_POST['ratepay_installments_interest_rate'] );
        $this->set( 'add_paydata[amount]', $_POST['ratepay_installments_amount'] );

        $this->gateway = $gateway;
    }

	/**
     * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
        $this->set( 'add_paydata[shop_id]', $this->gateway->determine_shop_id( $order ) );
        $this->set( 'add_paydata[device_token]', $this->gateway->get_device_fingerprint() );

        $this->set_reference( $order );
        $this->set( 'amount', $order->get_total() * 100 );
        $this->set( 'currency', strtoupper( $order->get_currency() ) );
        $this->set_personal_data_from_order( $order );
        $this->set_shipping_data_from_order( $order );
        $this->set_customer_ip_from_order( $order );
        $this->add_article_list_to_transaction( $order );

		return $this->submit();
	}
}
