<?php

namespace Payone\Admin;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class TransactionLog implements ListTableInterface  {
	public function displayList() {
		$list_table = new TransactionLogListTable( $this );
		$list_table->prepare_items();

		include PAYONE_VIEW_PATH . '/admin/transaction-log-list.php';
	}

	public function displaySingle( $id ) {
		$entry = $this->getEntry( $id );
		include PAYONE_VIEW_PATH . '/admin/transaction-log-single.php';
	}

	/**
	 * @param int $page
	 * @param int $per_page
	 *
	 * @return array
	 */
	public function get_entries( $page = 1, $per_page = 10 ) {
		global $wpdb;

		$query = 'SELECT id, transaction_id, data, created_at
                  FROM ' . $wpdb->prefix . \Payone\Transaction\Log::TABLE_NAME . ' 
                  ORDER BY created_at DESC
                  LIMIT ' . ( $page -1 ) * $per_page . ', ' . $per_page;
		$rows  = $wpdb->get_results( $query, ARRAY_A );

		$entries = [];
		foreach ( $rows as $row ) {
			$entries[] = \Payone\Transaction\Log::constructFromDatabase( $row );
		}

		return $entries;
	}

	/**
	 * @return int
	 */
	public function get_num_total_entries() {
		global $wpdb;

		$query = 'SELECT COUNT(id)
                  FROM ' . $wpdb->prefix . \Payone\Transaction\Log::TABLE_NAME;

		return (int)$wpdb->get_var( $query );
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