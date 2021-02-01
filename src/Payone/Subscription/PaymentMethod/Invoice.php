<?php

namespace Payone\Subscription\PaymentMethod;

class Invoice implements SubscriptionInterface {
	public function process_scheduled_subscription_payment( $subscription, $order ) {
		$transaction = new \Payone\Transaction\Invoice( new \Payone\Gateway\Invoice() );

		$transaction->set( 'amount', (int) ( round( $subscription->get_total(), 2 ) * 100 ) );
		$transaction->set( 'recurrence', 'recurring' );
		$transaction->set( 'customer_is_present', 'no' );
		$transaction->set( 'reference', sprintf( '%d_%d', (int) $subscription->get_id(), (int) $order->get_id() ) );

		$response = $transaction->execute( $order );

		if ( $response->is_approved() ) {
			$subscription->payment_complete( (string) $response->get( 'txid' ) );
			$subscription->add_order_note( sprintf(
				'PayOne: %s (PayOne Reference: %s)',
				__( 'Scheduled subscription payment successful.', 'payone-woocommerce-3' ),
				$transaction->get( 'reference', 'N/A' )
			) );

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
