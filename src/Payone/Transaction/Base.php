<?php

namespace Payone\Transaction;

use Payone\Payone\Api\Request;

class Base extends Request {
	public function __construct() {
		parent::__construct();
	}

	public function setDataFromOrder( \WC_Order $order ) {
		$this->setCommonDataFromOrder($order);
		$this->setPersonalDataFromOrder($order);
	}

	public function setCommonDataFromOrder( \WC_Order $order ) {
		$this->set( 'reference', $order->get_id() );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
	}

	public function setPersonalDataFromOrder( \WC_Order $order ) {
		$this->set('lastname', $order->get_billing_last_name());
		$this->set('firstname', $order->get_billing_first_name());
		$this->set('street', $order->get_billing_address_1());
		$this->set('adressaddition', $order->get_billing_address_2());
		$this->set('zip', $order->get_billing_postcode());
		$this->set('city', $order->get_billing_city());
		$this->set('country', $order->get_billing_country());
		$this->set('email', $order->get_billing_email());
		$this->set('telephonenumber', $order->get_billing_phone());
	}
}
