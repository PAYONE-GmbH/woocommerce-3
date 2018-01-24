<?php

namespace Payone\Admin;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class ApiLog {
	public function displayList() {
		$entries = $this->getEntries();
		include PAYONE_VIEW_PATH . '/admin/api_log_list.php';
	}

	public function displaySingle( $id ) {
		$entry = $this->getEntry( $id );
		include PAYONE_VIEW_PATH . '/admin/api_log_single.php';
	}

	/**
	 * @param int $page
	 *
	 * @return array
	 */
	private function getEntries( $page = 0 ) {
		global $wpdb;

		$query = 'SELECT id, request, response, created_at
                  FROM ' . $wpdb->prefix . \Payone\Payone\Api\Log::TABLE_NAME . ' 
                  ORDER BY created_at DESC';
		$rows  = $wpdb->get_results( $query, ARRAY_A );

		$entries = [];
		foreach ( $rows as $row ) {
			$entries[] = \Payone\Payone\Api\Log::constructFromDatabase( $row );
		}

		return $entries;
	}

	/**
	 * @param int $id
	 *
	 * @return \Payone\Payone\Api\Log
	 */
	private function getEntry( $id ) {
		global $wpdb;

		$query = 'SELECT id, request, response, created_at
                  FROM ' . $wpdb->prefix . \Payone\Payone\Api\Log::TABLE_NAME . ' 
                  WHERE id=' . (int) $id;
		$row   = $wpdb->get_row( $query, ARRAY_A );

		return \Payone\Payone\Api\Log::constructFromDatabase( $row );
	}
}