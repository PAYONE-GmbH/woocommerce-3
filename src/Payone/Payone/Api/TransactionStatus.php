<?php

namespace Payone\Payone\Api;

class TransactionStatus extends DataTransfer {
	/**
	 * @return TransactionStatus
	 */
	public static function constructFromPostParameters() {
		return new TransactionStatus( $_POST );
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
	 * @return bool
	 */
	public function isAppointed() {
		return $this->get_action() === 'appointed';
	}

	/**
	 * @return bool
	 */
	public function isPaid() {
		return $this->get_action() === 'paid';
	}

	/**
	 * @return bool
	 */
	public function isUnderpaid() {
		return $this->get_action() === 'underpaid';
	}

	/**
	 * @return bool
	 */
	public function isOverpaid() {
		return $this->isPaid() && $this->get_sum_missing() < 0.0;
	}
}