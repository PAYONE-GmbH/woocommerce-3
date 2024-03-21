<?php

namespace Payone\Transaction;

use Payone\Gateway\AmazonPayBase;
use Payone\Plugin;

class AmazonPayExpressGetCheckoutSession extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'genericpayment' );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'AMP' );
		$this->set( 'add_paydata[action]', 'getCheckoutSession' );

		$workorderid = Plugin::get_session_value( AmazonPayBase::SESSION_KEY_WORKORDERID );
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

		return $this->submit();
	}
}
