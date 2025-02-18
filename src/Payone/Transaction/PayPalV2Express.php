<?php

namespace Payone\Transaction;

use Payone\Gateway\PayPalV2Base;
use Payone\Plugin;

class PayPalV2Express extends Base {
	/**
	 * @param \Payone\Gateway\AmazonPayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PAL' );

		$workorderid = Plugin::get_session_value( PayPalV2Base::SESSION_KEY_WORKORDERID );
		if ( $workorderid ) {
			$this->set( 'workorderid', $workorderid );
		}
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		$this->add_article_list_to_transaction( $order );
		$this->set_reference( $order );
		$this->set_once( 'amount', $order->get_total('edit') * 100 );
		$this->set( 'currency', strtoupper( get_woocommerce_currency() ) );
		$this->set_personal_data_from_order( $order );
		$this->set_shipping_data_from_order( $order );

		$this->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'success', 'oid' => $order->get_id() ] ) );
		$this->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'error', 'oid' => $order->get_id() ] ) );
		$this->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'back', 'oid' => $order->get_id() ] ) );

		return $this->submit();
	}
}
