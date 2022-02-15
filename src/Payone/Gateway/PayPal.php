<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\WooCommerceSubscription\WCSAwareGatewayTrait;
use Payone\WooCommerceSubscription\WCSHandler;

class PayPal extends RedirectGatewayBase {

    use WCSAwareGatewayTrait;

	const GATEWAY_ID = 'bs_payone_paypal';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

        if ( WCSHandler::is_wcs_active() && $this->are_paypal_billing_agreements_enabled() ) {
            $this->add_wcs_support();
        }

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-paypal.png';;
		$this->method_title       = 'PAYONE ' . __( 'PayPal', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
	    $this->init_common_form_fields( __( 'PayPal', 'payone-woocommerce-3' ) );
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/paypal/payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\PayPal::class );
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

        $transaction->set( 'reference', $order->get_id() );
        $transaction->set( 'recurrence', 'recurring' );
        $transaction->set( 'customer_is_present', 'yes' );

        return $transaction;
    }

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->no_further_action_necessary() ) {
			return;
		}

		$order = $transaction_status->get_order();
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'authorization' && $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment is authorized by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_capture() ) {
			$order->add_order_note( __( 'Payment is captured by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_paid() ) {
			// Do nothing. Everything already happened.
		} else {
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone-woocommerce-3' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
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
