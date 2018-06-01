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
		$this->set( 'businessrelation', 'b2b' ); // @todo
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Order $order ) {
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

			$this->set( 'id[' . $n . ']', $item_id );
			$this->set( 'pr[' . $n . ']', round( 100 * wc_get_price_including_tax( $product ) ) );
			$this->set( 'no[' . $n . ']', $item_data->get_quantity() );
			$this->set( 'de[' . $n . ']', $product->get_name() );
			$this->set( 'va[' . $n . ']', 100 * $va );
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

			$this->set( 'id[' . $n . ']', $item_id );
			$this->set( 'pr[' . $n . ']', round( 100 * ($item_data->get_total_tax() + $item_data->get_total() ) ) );
			$this->set( 'no[' . $n . ']', $item_data->get_quantity() );
			$this->set( 'de[' . $n . ']', $item_data->get_name() );
			$this->set( 'va[' . $n . ']', 100 * $va );
			$n++;
		}

		$this->set( 'reference', $order->get_id() );
		$this->set( 'amount', $order->get_total() * 100 );
		$this->set( 'currency', strtoupper( $order->get_currency() ) );
		$this->set_personal_data_from_order( $order );

		return $this->submit();
	}
}