<?php

namespace Payone\Transaction;

class SafeInvoice extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'rec' );
		$this->set( 'clearingsubtype', 'POV' );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
        $this->set_reference( $order );
		$this->set_amount( $order );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );
        $this->set_shipping_data_from_order( $order );
        $this->set_customer_ip_from_order( $order );
		$this->add_article_list_to_transaction( $order );
		$this->set_business_relation( $order );

		return $this->submit();
	}

    /**
     * Sets the proper business relation of the customer according
     * to the provided billing address data.
     *
     * @param \WC_Order $order
     */
	protected function set_business_relation( \WC_Order $order ) {
        $company = $order->get_billing_company();

        // Set b2b if billing company is present, set b2c otherwise
        $this->set(
            'businessrelation',
            is_string($company) && !empty($company)
                ? 'b2b'
                : 'b2c'
        );
    }
}
