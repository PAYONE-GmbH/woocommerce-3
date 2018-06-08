<?php

namespace Payone\Transaction;

class SafeInvoice extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway->get_authorization_method() );
		$this->set_data_from_gateway( $gateway );

		$this->set( 'clearingtype', 'rec' );
		$this->set( 'clearingsubtype', 'POV' );
		$this->set( 'businessrelation', 'b2c' ); // @todo
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
		if ( $this->get( 'request' ) === 'authorization' ) {
			self::add_article_list_to_transaction( $this, $order );
		} else {
			$order->add_meta_data( '_article_list', self::get_article_list_for_transaction( $order ) );
			$order->save_meta_data();
		}

		$this->set( 'reference', $order->get_id() );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );

		return $this->submit();
	}

	public static function add_article_list_to_transaction( Base $transaction, \WC_Order $order ) {
		$article_list = self::get_article_list_for_transaction( $order );
		foreach ( $article_list as $n => $article) {
			$transaction->set( 'id[' . $n . ']', $article[ 'id' ] );
			$transaction->set( 'pr[' . $n . ']', $article[ 'pr' ] );
			$transaction->set( 'no[' . $n . ']', $article[ 'no' ] );
			$transaction->set( 'de[' . $n . ']', $article[ 'de' ] );
			$transaction->set( 'va[' . $n . ']', $article[ 'va' ] );
		}
	}

	public static function get_article_list_for_transaction( \WC_Order $order ) {
		$articles = $order->get_meta( '_article_list' );
		if ( is_array( $articles ) ) {
			return $articles;
		}

		$articles = [];

		$tax = new \WC_Tax();
		$n = 1;
		foreach ( $order->get_items() as $item_id => $item_data ) {
			$product = $item_data->get_product();
			$product_tax_class = $product->get_tax_class();
			$tax_rates =  $tax->get_rates( $product_tax_class );
			$va = 0;
			if ( $tax_rates ) {
				$tax_rate = array_pop( $tax_rates );
				if ( $tax_rate && isset( $tax_rate[ 'rate' ] ) ) {
					$va = round( $tax_rate[ 'rate' ] );
				}
			}

			$articles[ $n ] = [
				'id' => $item_id,
				'pr' => round( 100 * wc_get_price_including_tax( $product ) ),
				'no' => $item_data->get_quantity(),
				'de' => $product->get_name(),
				'va' => 100 * $va,
			];
			$n++;
		}
		foreach ( $order->get_shipping_methods() as $item_id => $item_data ) {
			$shipping_tax_class = $item_data->get_tax_class();
			$tax_rates =  $tax->get_rates( $shipping_tax_class );
			$va = 0;
			if ( $tax_rates ) {
				$tax_rate = array_pop( $tax_rates );
				if ( $tax_rate && isset( $tax_rate[ 'rate' ] ) ) {
					$va = round( $tax_rate[ 'rate' ] );
				}
			}

			$articles[ $n ] = [
				'id' => $item_id,
				'pr' => round( 100 * ($item_data->get_total_tax() + $item_data->get_total() ) ),
				'no' => $item_data->get_quantity(),
				'de' => $item_data->get_name(),
				'va' => 100 * $va,
			];
			$n++;
		}

		return $articles;
	}
}