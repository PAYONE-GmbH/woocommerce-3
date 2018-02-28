<?php

namespace Payone\Admin;

abstract class AbstractListTable extends \WP_List_Table {
	/**
	 * @var ListTableInterface
	 */
	private $list_table_interface;

	private $per_page = 20;

	public function __construct( ListTableInterface $list_table_interface ) {
		parent::__construct( [] );

		$this->list_table_interface = $list_table_interface;
	}

	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$current_page = $this->get_pagenum();

		$total_items = $this->list_table_interface->get_num_total_entries();

		$this->items = $this->list_table_interface->get_entries( $current_page, $this->per_page );

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $this->per_page,
			'total_pages' => ceil( $total_items / $this->per_page ),
		] );
	}
}