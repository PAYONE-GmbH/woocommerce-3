<?php

namespace Payone\Transaction;

use Payone\Plugin;

class PayDirekt extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PDT' );
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

		// todo: replace the following lines with $this->set_shipping_data_from_order()
		$this->set( 'shipping_lastname', $order->get_billing_last_name() );
		$this->set( 'shipping_firstname', $order->get_billing_first_name() );
		$this->set( 'shipping_street', $order->get_billing_address_1() );
		$this->set( 'shipping_zip', $order->get_billing_postcode() );
		$this->set( 'shipping_city', $order->get_billing_city() );
        $this->set( 'shipping_state', $order->get_billing_state() );
		$this->set( 'shipping_country', $order->get_billing_country() );
		
		$this->set_personal_data_from_order( $order );
        $this->set_customer_ip_from_order( $order );

		$this->set( 'successurl', Plugin::get_callback_url('success') . '&oid=' . $order->get_id() );
		$this->set( 'errorurl', Plugin::get_callback_url('error') . '&oid=' . $order->get_id() );
		$this->set( 'backurl', Plugin::get_callback_url('back') . '&oid=' . $order->get_id() );

		return $this->submit();
	}
}
