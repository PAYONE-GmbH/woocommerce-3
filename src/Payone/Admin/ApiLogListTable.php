<?php

namespace Payone\Admin;

class ApiLogListTable extends AbstractListTable {
	public function get_columns() {
		$columns = array(
			'id'          => 'ID',
			'request'     => __( 'Request', 'payone-woocommerce-3' ),
			'response'    => __( 'Response', 'payone-woocommerce-3' ),
			'mode'        => __( 'Modus', 'payone-woocommerce-3' ),
			'merchant_id' => __( 'Merchant-ID', 'payone-woocommerce-3' ),
			'portal_id'   => __( 'Portal ID', 'payone-woocommerce-3' ),
			'created_at'  => __( 'Created at', 'payone-woocommerce-3' ),
		);

		return $columns;
	}

	public function column_default( $item, $column_name ) {
		switch($column_name){
			case 'id':
				return '<a href="?page=payone-api-log&id=' . $item->getId() . '">' . $item->getId() . '</a>';
			case 'request':
				$result = $item->get_request()->get( 'request' );
				$clearing_type = $item->get_request()->get('clearingtype');
				if ($clearing_type) {
					$result .= ' (' . $clearing_type . ')';
				}

				return $result;
			case 'response':
				return $item->getResponse()->get('status');
			case 'mode':
				return $item->get_request()->get('mode');
			case 'merchant_id':
				return $item->get_request()->get('mid');
			case 'portal_id':
				return $item->get_request()->get('portalid');
			case 'created_at':
				return $item->getCreatedAt()->format('d.m.Y H:i');
			default:
				return print_r($item,true); // Show the whole array for troubleshooting purposes
		}
	}
}