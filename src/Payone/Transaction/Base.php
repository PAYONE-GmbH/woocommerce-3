<?php

namespace Payone\Transaction;

use Payone\Payone\Api\Request;

class Base extends Request {
	public function __construct( $type ) {
		parent::__construct();

		$this->set( 'request', $type );
	}

	/**
	 * Requests like "debit" don't have an authorization method. Therefore only "" is returned.
	 *
	 * @return string
	 */
	public function get_authorization_method() {
		$authorization_method = $this->get( 'request' );
		if ( ! in_array( $authorization_method, [ 'authorization', 'preauthorization' ], true ) ) {
			$authorization_method = '';
		}

		return $authorization_method;
	}

	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function set_data_from_gateway( $gateway ) {
		$this
			->set_account_id( $gateway->get_account_id() )
			->set_merchant_id( $gateway->get_merchant_id() )
			->set_portal_id( $gateway->get_portal_id() )
			->set_key( $gateway->get_key() );
	}

	public function set_personal_data_from_order( \WC_Order $order ) {
		$this->set( 'lastname', $order->get_billing_last_name() );
		$this->set( 'firstname', $order->get_billing_first_name() );
		$this->set( 'street', $order->get_billing_address_1() );
		$this->set( 'adressaddition', $order->get_billing_address_2() );
		$this->set( 'zip', $order->get_billing_postcode() );
		$this->set( 'city', $order->get_billing_city() );
		$this->set( 'country', $order->get_billing_country() );
		$this->set( 'email', $order->get_billing_email() );
		$this->set( 'telephonenumber', $order->get_billing_phone() );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return int
	 */
	protected function get_next_sequencenumber( \WC_Order $order ) {
		$sequencenumber = 1 + (int)$order->get_meta( '_sequencenumber');
		$order->update_meta_data( '_sequencenumber', $sequencenumber );
		$order->save_meta_data();

		return $sequencenumber;
	}

	/**
	 * @param \WC_Order $order
	 * @param int $sequencenumber
	 */
	protected function set_sequencenumber( \WC_Order $order, $sequencenumber ) {
		$order->update_meta_data( '_sequencenumber', $sequencenumber );
		$order->save_meta_data();
	}

	public function add_article_list_to_transaction( \WC_Order $order ) {
		$article_list = $this->get_article_list_for_transaction( $order );
		foreach ( $article_list as $n => $article) {
			$this->set( 'id[' . $n . ']', $article[ 'id' ] );
			$this->set( 'pr[' . $n . ']', $article[ 'pr' ] );
			$this->set( 'no[' . $n . ']', $article[ 'no' ] );
			$this->set( 'de[' . $n . ']', $article[ 'de' ] );
			$this->set( 'va[' . $n . ']', $article[ 'va' ] );
		}
	}

	protected function get_article_list_for_transaction( \WC_Order $order ) {
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

			$price = round( 100 * wc_get_price_including_tax( $product ) );
			$articles[ $n ] = [
				'id' => $item_id,
				'pr' => $price,
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

			$price = round( 100 * ($item_data->get_total_tax() + $item_data->get_total() ) );
			$articles[ $n ] = [
				'id' => $item_id,
				'pr' => $price,
				'no' => $item_data->get_quantity(),
				'de' => $item_data->get_name(),
				'va' => 100 * $va,
			];
			$n++;
		}

		$price = 100 * ( (float)$order->get_total_discount() + (float)$order->get_discount_tax() );
		if ($price > 0) {
			$articles[ $n ] = [
				'id' => -1,
				'pr' => - $price,
				'no' => 1,
				'de' => __( 'Discount', 'payone-woocommerce-3' ),
				'va' => 0,
			];
			$this->set( 'va[' . $n . ']', 0 );
		}

		$order->add_meta_data( '_article_list', $articles );
		$order->save_meta_data();

		return $articles;
	}
}
