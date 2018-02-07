<?php

namespace Payone\Transaction;

use Payone\Payone\Api\Request;

class Base extends Request {
	public function __construct( $type ) {
		parent::__construct();

		$this->set( 'request', $type );
	}

	/**
	 * Requests like "debit" don't have an authorization method. Therefore only "" is returned.
	 *
	 * @return string
	 */
	public function get_authorization_method() {
		$authorization_method = $this->get( 'request' );
		if ( ! in_array( $authorization_method, [ 'authorization', 'preauthorization' ], true ) ) {
			$authorization_method = '';
		}

		return $authorization_method;
	}

	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function set_data_from_gateway( $gateway ) {
		$this
			->setAccountId( $gateway->get_account_id() )
			->setMerchantId( $gateway->get_merchant_id() )
			->setPortalId( $gateway->get_portal_id() )
			->setKey( $gateway->get_key() );
	}

	public function setPersonalDataFromOrder( \WC_Order $order ) {
		$this->set( 'lastname', $order->get_billing_last_name() );
		$this->set( 'firstname', $order->get_billing_first_name() );
		$this->set( 'street', $order->get_billing_address_1() );
		$this->set( 'adressaddition', $order->get_billing_address_2() );
		$this->set( 'zip', $order->get_billing_postcode() );
		$this->set( 'city', $order->get_billing_city() );
		$this->set( 'country', $order->get_billing_country() );
		$this->set( 'email', $order->get_billing_email() );
		$this->set( 'telephonenumber', $order->get_billing_phone() );
	}
}
