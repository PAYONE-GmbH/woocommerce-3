<?php

namespace Payone\Transaction;

use Payone\Gateway\PayPalV2Base;
use Payone\Plugin;

class PayPalV2ExpressGetCheckoutDetails extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'genericpayment' );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PAL' );
		$this->set( 'add_paydata[action]', 'getexpresscheckoutdetails' );

		$workorderid = Plugin::get_session_value( PayPalV2Base::SESSION_KEY_WORKORDERID );
		if ( $workorderid ) {
			$this->set( 'workorderid', $workorderid );
		}
	}

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Cart $cart ) {
		$this->set_once( 'amount', $cart->get_total( 'non-view' ) * 100 );
		$this->set( 'currency', strtoupper( get_woocommerce_currency() ) );
		$this->add_article_list_to_transaction( $cart );

		return $this->submit();
	}
}
