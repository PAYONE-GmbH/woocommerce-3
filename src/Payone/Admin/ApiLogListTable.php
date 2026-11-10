<?php

namespace Payone\Admin;

class ApiLogListTable extends AbstractListTable {
	public function get_columns() {
		$columns = array(
			'id'          => 'ID',
			'created_at'  => __( 'Created at', 'payone-woocommerce-3' ),
			'request'     => __( 'Request', 'payone-woocommerce-3' ),
			'reference'   => __( 'Reference', 'payone-woocommerce-3' ),
			'response'    => __( 'Response', 'payone-woocommerce-3' ),
			'mode'        => __( 'Modus', 'payone-woocommerce-3' ),
			'merchant_id' => __( 'Merchant-ID', 'payone-woocommerce-3' ),
			'portal_id'   => __( 'Portal ID', 'payone-woocommerce-3' ),
		);

		return $columns;
	}

	/**
	 * @param \Payone\Payone\Api\Log $item
	 *
	 * @return string
	 */
	public static function get_detail_url( $item ) {
		return admin_url( 'admin.php?page=payone-api-log&id=' . (int) $item->get_id() );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return '<a class="payone-log__id" href="' . esc_url( self::get_detail_url( $item ) ) . '">#' . esc_html( $item->get_id() ) . '</a>';
			case 'request':
				$clearing_type = $item->get_request()->get( 'clearingtype' );
				$result        = '<a href="' . esc_url( self::get_detail_url( $item ) ) . '"><strong>' . esc_html( $item->get_request()->get( 'request' ) ) . '</strong></a>';
				if ( $clearing_type ) {
					$result .= ' <code>' . esc_html( $clearing_type ) . '</code>';
				}

				return $result;
			case 'reference':
				return esc_html( $item->get_request()->get( 'reference' ) );
			case 'response':
				$result = self::status_badge( $item->get_response()->get( 'status' ) );
				if ( $item->get_request()->get( 'request' ) === 'managemandate' ) {
					$mandate_status = $item->get_response()->get( 'mandate_status' );
					if ( $mandate_status ) {
						$result .= ' <code>' . esc_html( $mandate_status ) . '</code>';
					}
				}
				$error = $item->get_response()->get( 'errormessage' );
				if ( $error ) {
					$result .= '<div class="payone-log__error">' . esc_html( $error ) . '</div>';
				}

				return $result;
			case 'mode':
				return self::mode_badge( $item->get_request()->get( 'mode' ) );
			case 'merchant_id':
				return esc_html( $item->get_request()->get( 'mid' ) );
			case 'portal_id':
				return esc_html( $item->get_request()->get( 'portalid' ) );
			case 'created_at':
				return esc_html( $item->get_created_at()->format( 'd.m.Y H:i' ) );
			default:
				return '';
		}
	}
}
