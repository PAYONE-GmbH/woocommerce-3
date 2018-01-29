<?php

namespace Payone\Admin;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class TransactionLog {
	public function displayList() {
		$entries = $this->getEntries();
		include PAYONE_VIEW_PATH . '/admin/transaction_log_list.php';
	}

	public function displaySingle( $id ) {
		$entry = $this->getEntry( $id );
		include PAYONE_VIEW_PATH . '/admin/transaction_log_single.php';
	}

	/**
	 * @param int $page
	 *
	 * @return array
	 */
	private function getEntries( $page = 0 ) {
		global $wpdb;

		$query = 'SELECT id, transaction_id, data, created_at
                  FROM ' . $wpdb->prefix . \Payone\Transaction\Log::TABLE_NAME . ' 
                  ORDER BY created_at DESC';
		$rows  = $wpdb->get_results( $query, ARRAY_A );

		$entries = [];
		foreach ( $rows as $row ) {
			$entries[] = \Payone\Transaction\Log::constructFromDatabase( $row );
		}

		return $entries;
	}

	/**
	 * @param int $id
	 *
	 * @return \Payone\Transaction\Log
	 */
	private function getEntry( $id ) {
		global $wpdb;

		$query = 'SELECT id, transaction_id, data, created_at
                  FROM ' . $wpdb->prefix . \Payone\Transaction\Log::TABLE_NAME . ' 
                  WHERE id=' . (int) $id;
		$row   = $wpdb->get_row( $query, ARRAY_A );

		return \Payone\Transaction\Log::constructFromDatabase( $row );
	}
}