<?php

namespace Payone\Transaction;

use Payone\Gateway\PayPalV2Base;
use Payone\Plugin;

class PayPalV2 extends Base {
	/**
	 * @param \Payone\Gateway\PayPalV2Base $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PAL' );

		$workorderid = Plugin::get_session_value( PayPalV2Base::SESSION_KEY_WORKORDERID );
		$expressUsed = Plugin::get_session_value( \Payone\Gateway\PayPalV2Express::SESSION_KEY_PAYPALV2_EXPRESS_USED );
		if ( $workorderid && $expressUsed ) {
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

		#$this->set('add_paydata[redirection_mode', 'DIRECT_TO_MERCHANT');

		$this->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'success', 'oid' => $order->get_id() ] ) );
		$this->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'error', 'oid' => $order->get_id() ] ) );
		$this->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'back', 'oid' => $order->get_id() ] ) );

		return $this->submit();
	}
}
