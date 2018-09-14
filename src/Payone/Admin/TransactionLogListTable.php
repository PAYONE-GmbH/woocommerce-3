<?php

namespace Payone\Admin;

class TransactionLogListTable extends AbstractListTable {
	public function get_columns() {
		$columns = array(
			'id'             => 'ID',
			'transaction_id' => __( 'Transaction ID', 'payone-woocommerce-3' ),
			'reference'      => __( 'Reference', 'payone-woocommerce-3' ),
			'txaction'       => __( 'Transaction action', 'payone-woocommerce-3' ),
			'sequencenumber' => __( 'Sequencenumber', 'payone-woocommerce-3' ),
			'mode'           => __( 'Modus', 'payone-woocommerce-3' ),
			'portal_id'      => __( 'Portal ID', 'payone-woocommerce-3' ),
			'created_at'     => __( 'Created at', 'payone-woocommerce-3' ),
		);

		return $columns;
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return '<a href="?page=payone-transaction-log&id=' . $item->get_id() . '">' . $item->get_id() . '</a>';
			case 'transaction_id':
				return $item->get_transaction_id();
			case 'reference':
				return $item->get_data()->get( 'reference' );
			case 'txaction':
				return $item->get_data()->get( 'txaction' );
			case 'sequencenumber':
				return $item->get_data()->get( 'sequencenumber' );
			case 'mode':
				return $item->get_data()->get( 'mode' );
			case 'portal_id':
				return $item->get_data()->get( 'portalid' );
			case 'created_at':
				return $item->get_created_at()->format( 'd.m.Y H:i' );
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
		}
	}
}