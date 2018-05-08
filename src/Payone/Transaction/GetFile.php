<?php

namespace Payone\Transaction;

class GetFile extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 * @param array $data
	 */
	public function __construct( $gateway ) {
		parent::__construct( 'getfile' );
		$this->set_data_from_gateway( $gateway );
		$this->remove( 'aid' ); // Der Request darf diesen Parameter nicht enthalten
		$this->set( 'file_type', 'SEPA_MANDATE' );
		$this->set( 'file_format', 'PDF' );
	}

	public function execute( $mandate_identification_hash ) {
		$args = [
			'post_type' => 'shop_order',
			'post_status' => 'any',
			'meta_key' => '_mandate_identification_hash',
			'meta_value' => $mandate_identification_hash,
			'meta_compare' => '=',
		];

		$query = new \WP_Query( $args );
		$posts = $query->get_posts();
		if ( isset( $posts[ 0 ] ) ) {
			$post = $posts[ 0 ];
			$order = wc_get_order( $post->ID );
			$this->set( 'file_reference', $order->get_meta( '_mandate_identification' ) );

			return $this->submit();
		}

		return null;
	}
}