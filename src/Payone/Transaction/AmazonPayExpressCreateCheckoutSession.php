<?php

namespace Payone\Transaction;

class AmazonPayExpressCreateCheckoutSession extends Base {
	/**
	 * @param \Payone\Gateway\AmazonPayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'genericpayment' );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'AMP' );
		$this->set( 'add_paydata[action]', 'createCheckoutSessionPayload' );

		$restrictions = $gateway->get_restrictions();
		if ( $restrictions ) {
			$this->set( 'add_paydata[specialRestrictions]', $restrictions );
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
