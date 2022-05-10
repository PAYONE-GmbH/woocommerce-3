<?php

namespace Payone\Transaction;

use Payone\Gateway\PayPalBase;
use Payone\Plugin;

class PayPal extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
     * @param string $authorization_method
	 */
    public function __construct( $gateway, $authorization_method = null ) {
        // We want to be able to overide the default setting for subscription handling
        if ( $authorization_method === null ) {
            $authorization_method = $gateway->get_authorization_method();
        }
        parent::__construct( $authorization_method );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'wlt' );
		$this->set( 'wallettype', 'PPE' );

        $workorderid = get_transient( PayPalBase::TRANSIENT_KEY_WORKORDERID );
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
		$this->set_once( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );
        $this->set_shipping_data_from_order( $order );
        $this->set_customer_ip_from_order( $order );

		$this->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'success', 'oid' => $order->get_id() ] ) );
		$this->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'error', 'oid' => $order->get_id() ] ) );
		$this->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back', 'oid' => $order->get_id() ] ) );

		return $this->submit();
	}
}
