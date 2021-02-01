<?php

namespace Payone\Subscription;

use Payone\Gateway\CreditCard;
use Payone\Gateway\Invoice;
use Payone\Gateway\PayPal;
use Payone\Gateway\SepaDirectDebit;
use Payone\Subscription\PaymentMethod\SubscriptionInterface;

/**
 * @see https://docs.woocommerce.com/document/subscriptions/develop/action-reference/
 */
class SubscriptionDispatcher {
	/**
	 * Is Woocommerce Subscription Plugin installed and active?
	 * @return bool
	 */
	public static function is_wcs_active() {
		return (bool) in_array(
			'woocommerce-subscriptions/woocommerce-subscriptions.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		);
	}

	/**
	 * Add all actions regarding Woocommerce Subscription Plugin.
	 * @return void
	 */
	public function add_actions() {
		add_action( 'woocommerce_scheduled_subscription_payment', [ $this, 'process_scheduled_subscription_payment' ] );
		add_action( 'woocommerce_subscription_payment_failed', [ $this, 'process_subscription_payment_failed' ] );
	}

	/**
	 * @param int $subscription_id
	 */
	public function process_scheduled_subscription_payment( $subscription_id ) {
		$subscription = new \WC_Subscription( (int) $subscription_id );

		//Get order that is meant to represent this transaction.
		/** @var \WC_Order[] $orders */
		$orders = $subscription->get_related_orders( 'all', 'renewal' );
		$order  = reset( $orders );

		if ( ! $order instanceof \WC_Order ) {
			$subscription->add_order_note( sprintf( 'PayOne: %s', __( 'Could not get order created for renewal.', 'payone-woocommerce-3' ) ) );
			$subscription->payment_failed();

			return;
		}

		$orderStatus = $order->get_status();

		if ( $orderStatus !== 'pending' && $order->needs_payment() ) {
			//Process only pending orders.
			return;
		}

		switch ( $order->get_payment_method() ) {
			case CreditCard::GATEWAY_ID:
				$subscriptionClass = new PaymentMethod\CreditCard();
				break;
			case PayPal::GATEWAY_ID:
				$subscriptionClass = new PaymentMethod\PayPal();
				break;
			case SepaDirectDebit::GATEWAY_ID:
				$subscriptionClass = new PaymentMethod\SepaDirectDebit();
				break;
			case Invoice::GATEWAY_ID:
				$subscriptionClass = new PaymentMethod\Invoice();
				break;
			default:
				$subscriptionClass = null;
				break;
		}

		if ( ! $subscriptionClass instanceof SubscriptionInterface ) {
			//Do nothing.
			return;
		}

		$subscriptionClass->process_scheduled_subscription_payment( $subscription, $order );
	}

	/**
	 * @param \WC_Subscription $subscription
	 * @param string $new_status
	 */
	public function process_subscription_payment_failed( $subscription, $new_status ) {
		//At the moment, we handle all within $this->process_scheduled_subscription_payment().
	}
}
