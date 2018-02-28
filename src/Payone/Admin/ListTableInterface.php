<?php

namespace Payone\Admin;

interface ListTableInterface {
	/**
	 * @param int $page
	 * @param int $per_page
	 *
	 * @return array
	 */
	public function get_entries( $page, $per_page );

	/**
	 * @return int
	 */
	public function get_num_total_entries();
}