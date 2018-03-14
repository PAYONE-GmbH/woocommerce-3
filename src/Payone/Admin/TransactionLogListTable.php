<?php

namespace Payone\Admin;

class TransactionLogListTable extends AbstractListTable {
	public function get_columns() {
		$columns = array(
			'id'             => 'ID',
			'transaction_id' => __( 'Transaction ID', 'payone-woocommerce-3' ),
			'order_id'       => __( 'Order ID', 'payone-woocommerce-3' ),
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
				return '<a href="?page=payone-transaction-log&id=' . $item->getId() . '">' . $item->getId() . '</a>';
			case 'transaction_id':
				return $item->getTransactionId();
			case 'order_id':
				return $item->getData()->get( 'reference' );
			case 'txaction':
				return $item->getData()->get( 'txaction' );
			case 'sequencenumber':
				return $item->getData()->get( 'sequencenumber' );
			case 'mode':
				return $item->getData()->get( 'mode' );
			case 'portal_id':
				return $item->getData()->get( 'portalid' );
			case 'created_at':
				return $item->getCreatedAt()->format( 'd.m.Y H:i' );
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
		}
	}
}