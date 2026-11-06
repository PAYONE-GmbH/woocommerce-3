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

	/**
	 * @return int
	 */
	public function get_total_items() {
		return (int) $this->get_pagination_arg( 'total_items' );
	}

	/**
	 * Badge for a PAYONE status (APPROVED, REDIRECT, ERROR, ...).
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public static function status_badge( $status ) {
		$status = (string) $status;
		if ( $status === '' ) {
			return '<span class="payone-muted">&ndash;</span>';
		}
		$types = [
			'APPROVED' => 'success',
			'OK'       => 'success',
			'REDIRECT' => 'live',
			'PENDING'  => 'test',
			'ERROR'    => 'error',
			'INVALID'  => 'error',
		];
		$type  = isset( $types[ strtoupper( $status ) ] ) ? $types[ strtoupper( $status ) ] : 'neutral';

		return '<span class="payone-badge payone-badge--' . $type . '">' . esc_html( $status ) . '</span>';
	}

	/**
	 * @param string $mode "test" or "live"
	 *
	 * @return string
	 */
	public static function mode_badge( $mode ) {
		$mode = (string) $mode;
		if ( $mode === '' ) {
			return '<span class="payone-muted">&ndash;</span>';
		}

		return '<span class="payone-badge payone-badge--' . ( $mode === 'live' ? 'live' : 'test' ) . '">' . esc_html( ucfirst( $mode ) ) . '</span>';
	}

	public function no_items() {
		esc_html_e( 'No log entries yet.', 'payone-woocommerce-3' );
	}
}
