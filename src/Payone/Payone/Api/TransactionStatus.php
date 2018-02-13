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
	public function get_order_id() {
		return $this->get('reference');
	}

	/**
	 * @return float
	 */
	public function get_balance() {
		return (float)$this->get('balance', 0.0);
	}
}