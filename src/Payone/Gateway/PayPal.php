<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class PayPal extends RedirectGatewayBase implements SubscriptionAwareInterface {

	use SubscriptionAwareTrait;

	const GATEWAY_ID = 'bs_payone_paypal';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'Payone ' . __( 'PayPal', 'payone-woocommerce-3' );
		$this->method_description = '';

		if ( self::is_wcs_active() && $this->is_paypal_billing_agreements_enabled() ) {
			$this->add_subscription_support();
			$this->add_subscription_actions();
		}
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

	public function process_scheduled_subscription_payment( $renewal_total, $renewal_order ) {
		$subscription = $this->get_subscriptions_for_renewal_order( $renewal_order );

		if ( ! $subscription instanceof \WC_Subscription ) {
			return;
		}

		/** @var GatewayBase $this */
		$transaction = new \Payone\Transaction\PayPal( new \Payone\Gateway\PayPal() );

		$transaction->set( 'amount', (int) ( round( $subscription->get_total(), 2 ) * 100 ) );
		$transaction->set( 'recurrence', 'recurring' );
		$transaction->set( 'customer_is_present', 'no' );
		$transaction->set( 'userid', $subscription->get_meta( '_payone_userid' ) );

		$response = $transaction->execute( $renewal_order );

		if ( $response->is_approved() ) {
			$subscription->payment_complete( $response->get( 'txid' ) );
			$renewal_order->add_order_note( sprintf(
				'PayOne: %s (PayOne Reference: %s)',
				__( 'Scheduled subscription payment successful.', 'payone-woocommerce-3' ),
				$transaction->get( 'reference', 'N/A' )
			) );

			return;
		}

		$renewal_order->add_order_note( sprintf(
			'PayOne: %s (Error: %s)',
			__( 'Scheduled subscription payment failed.', 'payone-woocommerce-3' ),
			$response->get_error_message()
		) );
		$subscription->payment_failed();
	}
}
