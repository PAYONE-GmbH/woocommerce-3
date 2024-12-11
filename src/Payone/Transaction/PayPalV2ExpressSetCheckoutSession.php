<?php

namespace Payone\Transaction;

class PayPalV2ExpressSetCheckoutSession extends Base {
	/**
	 * @param \Payone\Gateway\PayPalV2Base $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'genericpayment' );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PAL' );
		$this->set( 'add_paydata[action]', 'setexpresscheckout' );
		$this->set( 'add_paydata[payment_action]', 'CAPTURE' );
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
