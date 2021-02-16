<?php

namespace Payone\Gateway;

use Payone\Payone\Api\Request;
use Payone\Payone\Api\TransactionStatus;
use Payone\Subscription\SubscriptionHandler;

class Invoice extends GatewayBase implements SubscriptionAwareInterface {

	use SubscriptionAwareTrait;

	const GATEWAY_ID = 'bs_payone_invoice';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'Payone ' . __( 'Invoice', 'payone-woocommerce-3' );
		$this->method_description = '';

		if ( SubscriptionHandler::is_wcs_active() ) {
			$this->add_subscription_support();
			$this->add_subscription_actions();
		}
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Invoice', 'payone-woocommerce-3' ) );
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/invoice/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\Invoice( $this );

		// According to https://docs.woocommerce.com/document/subscriptions/develop/payment-gateway-integration/
		// If order passed to process_redirect() is actual subscription itself, that means that this is a payment
        // method change request.
		// We need to set some specific parameters for PayOne request that are described in
        // https://docs.payone.com/display/public/PLATFORM/Special+remarks+-+PayPal
		if ( $this->order_is_subscription( $order ) ) {
			$transaction->set( 'amount', 0 );
			// PMCR stands for (P)ayment (M)ethod (C)hange (R)equest
			// There is a possibility that this particular order ID was already used and failed. If that is
			// the case, PayOne would always return that "Reference ID already exists" error.
			$transaction->set( 'reference', sprintf( '%d-PMCR', (int) $order->get_id() ) );
		}

		if ( $this->order_contains_subscription( $order ) ) {
			// This is initial payment for (possibly multiple) subscription.
			$transaction->set( 'recurrence', 'recurring' );
			$transaction->set( 'customer_is_present', 'yes' );
		}

		$response = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(),
				'error' );

			return;
		}
		// @todo Bei Kauf auf Rechnung anderer Status und Order abschließen?

		if ( $this->order_is_subscription( $order ) ) {
			// We need to get and save some data from PayOne response if this is payment method change request.
			// See comment above, on the first "if ( $this->order_is_subscription( $order ) )" clause.
			$order->update_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
			$order->save_meta_data();
		}

		if ( $this->order_contains_subscription( $order ) ) {
			// Set necessary data for (possible multiple) subscription, so we can charge recurring payments.
			foreach ( wcs_get_subscriptions_for_order( $order ) as $subscription ) {
				/** @var \WC_Subscription $subscription */
				$subscription->update_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
				$subscription->save_meta_data();
			}
		}

		$order->set_transaction_id( $response->get( 'txid' ) );
		$response->store_clearing_info( $order );
		$this->add_email_meta_hook( [ $this, 'email_meta_action' ] );
		$order->update_meta_data( '_authorization_method', $transaction->get( 'request' ) );
		$order->update_status( 'on-hold', __( 'Invoice has been sent', 'payone-woocommerce-3' ) );

		// Reduce stock levels
		wc_reduce_stock_levels( $order_id );

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
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

		if ( $transaction_status->is_overpaid() ) {
			$order->add_order_note( __( 'Payment received. Customer overpaid!', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $transaction_status->is_underpaid() ) {
			$order->add_order_note( __( 'Payment received. Customer underpaid!', 'payone-woocommerce-3' ) );
		} elseif ( $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );

		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}

	public function process_woocommerce_scheduled_subscription_payment( $renewal_total, $renewal_order ) {
		$subscription = $this->get_subscriptions_for_renewal_order( $renewal_order );

		if ( ! $subscription instanceof \WC_Subscription ) {
			return;
		}

		$transaction = new \Payone\Transaction\Invoice( new \Payone\Gateway\Invoice() );

		$transaction->set( 'amount', (int) ( round( $subscription->get_total(), 2 ) * 100 ) );
		$transaction->set( 'recurrence', 'recurring' );
		$transaction->set( 'customer_is_present', 'no' );
		$transaction->set( 'userid', $subscription->get_meta( '_payone_userid' ) );

		$response = $transaction->execute( $renewal_order );

		if ( $response->is_approved() ) {
			$subscription->payment_complete( (string) $response->get( 'txid' ) );
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
