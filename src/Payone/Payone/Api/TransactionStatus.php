<?php

namespace Payone\Payone\Api;

use Payone\Gateway\GatewayBase;
use Payone\Plugin;

class TransactionStatus extends DataTransfer {
	/**
	 * @var \WC_Order $order
	 */
	private $order;

	/**
	 * @var GatewayBase
	 */
	private $gateway;

	/**
	 * @return TransactionStatus
	 */
	public static function construct_from_post_parameters() {
		$transaction_status = new TransactionStatus( $_POST );
		$transaction_status->set_order( $transaction_status->get('reference') );

		return $transaction_status;
	}

	/**
	 * @todo Der try-catch-Block wurde hinzugefügt, weil der DEV-Server die Order bearbeiten muss, auch wenn er die ID
	 * @todo nicht kennt. Das kann/soll/muss evtl. beim Livegang wieder entfernt werden.
	 * 
	 * @param int $order_id
	 *
	 * @return TransactionStatus
	 */
	public function set_order( $order_id ) {
		if ( $order_id ) {
			try {
				$this->order   = new \WC_Order( $order_id );
				$this->gateway = Plugin::get_gateway_for_order( $this->order );

				$this->order->update_meta_data( '_' . $this->get_action(), time() );
				$this->order->save_meta_data();
			} catch ( \Exception $e) {
				$this->order = null;
				$this->gateway = null;
			}
		} else {
			$this->order = null;
			$this->gateway = null;
		}

		return $this;
	}

	/**
	 * @return \WC_Order
	 */
	public function get_order() {
		return $this->order;
	}

	/**
	 * @return GatewayBase
	 */
	public function get_gateway() {
		return $this->gateway;
	}

	/**
	 * @return string
	 */
	public function get_action() {
		return $this->get( 'txaction' );
	}

	/**
	 * @return string
	 */
	public function get_order_id() {
		return $this->get( 'reference' );
	}

	/**
	 * @return float
	 */
	public function get_balance() {
		return $this->get_float( 'balance', 0.0 );
	}

	/**
	 * Returns the amount that is still missing. When the amount is negative, it means that the customer overpaid.
	 *
	 * @return float
	 */
	public function get_sum_missing() {
		return $this->get_price() + $this->get_balance(); // balance is always negative
	}

	/**
	 * @return float
	 */
	public function get_price() {
		return $this->get_float( 'price', 0.0 );
	}

	/**
	 * @return int
	 */
	public function get_sequencenumber() {
		return $this->get_int( 'sequencenumber' );
	}

	/**
	 * @return bool
	 */
	public function is_appointed() {
		return $this->get_action() === 'appointed';
	}

	/**
	 * @return bool
	 */
	public function is_capture() {
		return $this->get_action() === 'capture';
	}

	/**
	 * @return bool
	 */
	public function is_paid() {
		return $this->get_action() === 'paid';
	}

	/**
	 * @return bool
	 */
	public function is_underpaid() {
		return $this->get_action() === 'underpaid';
	}

	/**
	 * @return bool
	 */
	public function is_cancelation() {
		return $this->get_action() === 'cancelation';
	}

	/**
	 * @return bool
	 */
	public function is_overpaid() {
		return $this->is_paid() && $this->get_sum_missing() < 0.0;
	}
}