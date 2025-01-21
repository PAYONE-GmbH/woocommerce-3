<?php

namespace Payone\Transaction;

use Payone\Gateway\AmazonPayBase;
use Payone\Plugin;

class AmazonPay extends Base {
	/**
	 * @param \Payone\Gateway\AmazonPayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'AMP' );

		$workorderid = Plugin::get_session_value( AmazonPayBase::SESSION_KEY_WORKORDERID );
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
		if ( $this->should_submit_cart() ) {
			$this->add_article_list_to_transaction( $order );
		}
		$this->set_reference( $order );
		$this->set_once( 'amount', $order->get_total('edit') * 100 );
		$this->set( 'currency', strtoupper( get_woocommerce_currency() ) );
		$this->set_personal_data_from_order( $order );
		$this->set_shipping_data_from_order( $order );
		if ( $this->has_no_shipping_data() ) {
			$this->copy_shipping_data_from_personal_data();
		}

		$this->set( 'add_paydata[checkoutMode]', 'ProcessOrder' );
		$this->set( 'add_paydata[productType]', 'PayAndShip' );

		$this->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'success', 'oid' => $order->get_id() ] ) );
		$this->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'error', 'oid' => $order->get_id() ] ) );
		$this->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'back', 'oid' => $order->get_id() ] ) );

		return $this->submit();
	}
}
