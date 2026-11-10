<?php

namespace Payone\Admin;

class TransactionLogListTable extends AbstractListTable {
	public function get_columns() {
		$columns = array(
			'id'             => 'ID',
			'created_at'     => __( 'Created at', 'payone-woocommerce-3' ),
			'txaction'       => __( 'Transaction action', 'payone-woocommerce-3' ),
			'transaction_id' => __( 'Transaction ID', 'payone-woocommerce-3' ),
			'reference'      => __( 'Reference', 'payone-woocommerce-3' ),
			'sequencenumber' => __( 'Sequencenumber', 'payone-woocommerce-3' ),
			'mode'           => __( 'Modus', 'payone-woocommerce-3' ),
			'portal_id'      => __( 'Portal ID', 'payone-woocommerce-3' ),
		);

		return $columns;
	}

	/**
	 * @param \Payone\Transaction\Log $item
	 *
	 * @return string
	 */
	public static function get_detail_url( $item ) {
		return admin_url( 'admin.php?page=payone-transaction-log&id=' . (int) $item->get_id() );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return '<a class="payone-log__id" href="' . esc_url( self::get_detail_url( $item ) ) . '">#' . esc_html( $item->get_id() ) . '</a>';
			case 'transaction_id':
				return esc_html( $item->get_transaction_id() );
			case 'reference':
				return esc_html( $item->get_data()->get( 'reference' ) );
			case 'txaction':
				return '<a href="' . esc_url( self::get_detail_url( $item ) ) . '"><strong>' . esc_html( $item->get_data()->get( 'txaction' ) ) . '</strong></a>';
			case 'sequencenumber':
				return esc_html( $item->get_data()->get( 'sequencenumber' ) );
			case 'mode':
				return self::mode_badge( $item->get_data()->get( 'mode' ) );
			case 'portal_id':
				return esc_html( $item->get_data()->get( 'portalid' ) );
			case 'created_at':
				return esc_html( $item->get_created_at()->format( 'd.m.Y H:i' ) );
			default:
				return '';
		}
	}
}
