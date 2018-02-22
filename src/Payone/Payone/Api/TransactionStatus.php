<?php

namespace Payone\Payone\Api;

class TransactionStatus extends DataTransfer  {
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
		return $this->get('reference');
	}

	/**
	 * @return float
	 */
	public function get_balance() {
		return (float)$this->get('balance', 0.0);
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
		return $this->get_action() === 'overpaid';
	}
}