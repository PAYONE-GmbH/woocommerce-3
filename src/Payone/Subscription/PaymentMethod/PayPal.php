<?php

namespace Payone\Subscription\PaymentMethod;

use Payone\Gateway\GatewayBase;

class PayPal implements SubscriptionInterface {
	public function process_scheduled_subscription_payment( $subscription, $order ) {
		/** @var GatewayBase $this */
		$transaction = new \Payone\Transaction\PayPal( new \Payone\Gateway\PayPal() );

		$transaction->set( 'amount', (int) ( round( $subscription->get_total(), 2 ) * 100 ) );
		$transaction->set( 'recurrence', 'recurring' );
		$transaction->set( 'customer_is_present', 'no' );
		$transaction->set( 'userid', $subscription->get_meta( '_payone_userid' ) );
		$transaction->set( 'reference', sprintf( '%d_%d', (int) $subscription->get_id(), (int) $order->get_id() ) );

		$response = $transaction->execute( $order );

		if ( $response->is_approved() ) {
			$subscription->add_order_note( sprintf(
				'PayOne: %s (PayOne Reference: %s)',
				__( 'Scheduled subscription payment successful.', 'payone-woocommerce-3' ),
				$transaction->get( 'reference', 'N/A' )
			) );
			$subscription->payment_complete( $response->get( 'txid' ) );

			return;
		}

		$subscription->add_order_note( sprintf(
			'PayOne: %s (Error: %s)',
			__( 'Scheduled subscription payment failed.', 'payone-woocommerce-3' ),
			$response->get_error_message()
		) );
		$subscription->payment_failed();
	}
}
