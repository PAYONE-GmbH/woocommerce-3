<?php

namespace Payone\Gateway;

use Payone\WooCommerceSubscription\WCSAwareGatewayTrait;
use Payone\WooCommerceSubscription\WCSHandler;

class PayPal extends PayPalBase {

    use WCSAwareGatewayTrait;

	const GATEWAY_ID = 'bs_payone_paypal';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

        if ( WCSHandler::is_wcs_active() && $this->are_paypal_billing_agreements_enabled() ) {
            $this->add_wcs_support();
        }

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-paypal.png';
		$this->method_title       = 'PAYONE ' . __( 'PayPal', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

    public function init_form_fields() {
        $this->init_common_form_fields( 'PAYONE ' . __( 'PayPal', 'payone-woocommerce-3' ) );
    }

    /**
     * @param \WC_Order $order
     * @return \Payone\Transaction\PayPal
     */
    public function wcs_get_transaction_for_subscription_signup( \WC_Order $order ) {
        if ( (int)$order->get_total() === 0 ) {
            $transaction = new \Payone\Transaction\PayPal( $this, 'preauthorization' );
            // The user does not need to pay anything right now, but we need to set the amount to 1 cent.
            // This is not going to be captured. We just need the preauthorization;
            $transaction->set('amount', 1);
        } else {
            $transaction = new \Payone\Transaction\PayPal( $this );
        }

        $transaction->set_reference( $order );
        $transaction->set( 'recurrence', 'recurring' );
        $transaction->set( 'customer_is_present', 'yes' );

        return $transaction;
    }

    /**
     * @return bool
     */
    protected function are_paypal_billing_agreements_enabled() {
        if ( isset( $this->global_settings['paypal_billing_agreements_enabled'] ) ) {
            return (bool) $this->global_settings['paypal_billing_agreements_enabled'];
        }

        return false;
    }
}
