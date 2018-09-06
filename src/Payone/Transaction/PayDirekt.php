<?php

namespace Payone\Transaction;

class PayDirekt extends OrderBase {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PDT' );
	}

	/**
	 * @param \WC_Order $order
	 */
	public function add_execution_parameters_for_order( \WC_Order $order ) {
		$this->set( 'shipping_lastname', $order->get_billing_last_name() );
		$this->set( 'shipping_firstname', $order->get_billing_first_name() );
		$this->set( 'shipping_street', $order->get_billing_address_1() );
		$this->set( 'shipping_zip', $order->get_billing_postcode() );
		$this->set( 'shipping_city', $order->get_billing_city() );
		$this->set( 'shipping_country', $order->get_billing_country() );

		$this->add_callback_urls( $order );
	}
}
