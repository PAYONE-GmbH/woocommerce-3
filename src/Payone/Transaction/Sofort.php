<?php

namespace Payone\Transaction;

use Payone\Plugin;

class Sofort extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'sb' );
		$this->set( 'onlinebanktransfertype', 'PNT' );
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
		$this->set( 'bankcountry', $this->get( 'country' ) );
		$this->set( 'successurl', Plugin::get_callback_url('success') . '&oid=' . $order->get_id() );
		$this->set( 'errorurl', Plugin::get_callback_url('error') . '&oid=' . $order->get_id() );
		$this->set( 'backurl', Plugin::get_callback_url('back') . '&oid=' . $order->get_id() );

		return $this->submit();
	}
}
