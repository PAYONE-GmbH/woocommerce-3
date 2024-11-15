<?php

namespace Payone\Transaction;

use Automattic\WooCommerce\Admin\Overrides\OrderRefund;
use Payone\Gateway\GatewayBase;
use Payone\Payone\Api\Request;
use Payone\Plugin;

class Base extends Request {
	const FIELD_LENGTH_ID_N = 32;
	const FIELD_LENGTH_NO_N = 6;
	const FIELD_LENGTH_DE_N = 255;
	const FIELD_LENGTH_VA_N = 4;

	/**
	 * @var GatewayBase
	 */
	private $gateway;

	public function __construct( $type ) {
		parent::__construct();

		$this->gateway = null;
		$this->set( 'request', $type );
	}

	/**
	 * @return bool
	 */
	public function should_submit_cart() {
		if ( ! $this->gateway ) {
			return false;
		}

		if ( $this->gateway->should_submit_cart() ) {
			return true;
		}
		
		// Für diese Gateways bei Debit immer die detaillierte Artikelliste mitsenden
		if ( $this->get( 'request' ) === 'debit'
		     && in_array( $this->gateway->id, [
				\Payone\Gateway\SafeInvoice::GATEWAY_ID,
				\Payone\Gateway\KlarnaInstallments::GATEWAY_ID,
				\Payone\Gateway\KlarnaInvoice::GATEWAY_ID,
				\Payone\Gateway\KlarnaSofort::GATEWAY_ID,
				\Payone\Gateway\RatepayDirectDebit::GATEWAY_ID,
				\Payone\Gateway\RatepayInstallments::GATEWAY_ID,
				\Payone\Gateway\RatepayOpenInvoice::GATEWAY_ID,
				\Payone\Gateway\SecuredInvoice::GATEWAY_ID,
				\Payone\Gateway\SecuredInstallment::GATEWAY_ID,
			], true )
		) {
			return true;
		}

		// Für diese Gateways bei Capture immer die detaillierte Artikelliste mitsenden
		if ( $this->get( 'request' ) === 'capture'
		     && in_array( $this->gateway->id, [
				\Payone\Gateway\SecuredInvoice::GATEWAY_ID,
				\Payone\Gateway\SecuredInstallment::GATEWAY_ID,
			], true )
		) {
			return true;
		}

		// Bei Sicherer Rechnung immer den Warenkorb mitsenden
		return $this->gateway->id === \Payone\Gateway\SafeInvoice::GATEWAY_ID;
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
		$this->gateway = $gateway;
		$this
			->set_account_id( $this->gateway->get_account_id() )
			->set_merchant_id( $this->gateway->get_merchant_id() )
			->set_portal_id( $this->gateway->get_portal_id() )
			->set_key( $this->gateway->get_key() );
	}

	public function set_personal_data_from_order( \WC_Order $order ) {
		$country = $order->get_billing_country();
		$company = $order->get_billing_company();

		$this->set( 'lastname', $order->get_billing_last_name() );
		$this->set( 'firstname', $order->get_billing_first_name() );
		$this->set( 'street', $order->get_billing_address_1() );
		$this->set( 'adressaddition', $order->get_billing_address_2() );
		$this->set( 'zip', $order->get_billing_postcode() );
		$this->set( 'city', $order->get_billing_city() );

		if ( ! empty( $company ) ) {
			$this->set( 'company', $company );
		}

		$this->set( 'country', $country );
		$this->set( 'email', $order->get_billing_email() );
		$this->set( 'telephonenumber', $order->get_billing_phone() );
	}

	/**
	 * Sets the proper business relation of the customer according
	 * to the provided billing address data.
	 *
	 * @param \WC_Order $order
	 */
	protected function set_business_relation( \WC_Order $order ) {
		$company = $order->get_billing_company();

		// Set b2b if billing company is present, set b2c otherwise
		$this->set(
			'businessrelation',
			is_string( $company ) && ! empty( $company )
				? 'b2b'
				: 'b2c'
		);
	}

	/**
	 * "YYYY-MM-DD" -> "YYYYMMDD"
	 *
	 * @param string $birthday
	 *
	 * @return string
	 */
	public static function convert_birthday( $birthday ) {
		return implode( '', explode( '-', $birthday ) );
	}

	/**
	 * Sets PAYONE API shipping data from the provided WooCommerce order object.
	 *
	 * @param \WC_Order $order The WooCommerce order object.
	 *
	 * @return void
	 * @author Fabian Böttcher <fabian.boettcher@payone.de>
	 */
	protected function set_shipping_data_from_order( \WC_Order $order ) {
		// Collect order shipping information.
		$data = [
			'shipping_firstname'       => $order->get_shipping_first_name(),
			'shipping_lastname'        => $order->get_shipping_last_name(),
			'shipping_company'         => $order->get_shipping_company(),
			'shipping_street'          => $order->get_shipping_address_1(),
			'shipping_zip'             => $order->get_shipping_postcode(),
			'shipping_addressaddition' => $order->get_shipping_address_2(),
			'shipping_city'            => $order->get_shipping_city(),
			'shipping_state'           => $order->get_shipping_state(),
			'shipping_country'         => $order->get_shipping_country(),
		];

		// Trim parameter values and set parameters null for empty strings.
		$data = array_map( function ( $value ) {
			return empty( $value = trim( $value ) ) ? null : $value;
		}, $data );

		// Set valid PAYONE API shipping data.
		foreach ( $data as $name => $value ) {
			if ( $value !== null ) {
				$this->set( $name, $value );
			}
		}
	}

	protected function has_no_shipping_data()
	{
		return ! $this->get( 'shipping_lastname' )
		       && ! $this->get( 'shipping_street' )
		       && ! $this->get( 'shipping_zip' );
	}

	protected function copy_shipping_data_from_personal_data() {
		// Collect order shipping information.
		$data = [
			'shipping_firstname'       => $this->get( 'firstname' ),
			'shipping_lastname'        => $this->get( 'lastname' ),
			'shipping_company'         => $this->get( 'company' ),
			'shipping_street'          => $this->get( 'street' ),
			'shipping_zip'             => $this->get( 'zip' ),
			'shipping_addressaddition' => $this->get( 'addressaddition' ),
			'shipping_city'            => $this->get( 'city' ),
			'shipping_country'         => $this->get( 'country' ),
		];

		// Trim parameter values and set parameters null for empty strings.
		$data = array_map( function ( $value ) {
			return empty( $value = trim( $value ) ) ? null : $value;
		}, $data );

		// Set valid PAYONE API shipping data.
		foreach ( $data as $name => $value ) {
			if ( $value !== null ) {
				$this->set( $name, $value );
			}
		}
	}

	/**
	 * Sets PAYONE API customer IP from the provided WooCommerce order.
	 *
	 * @param \WC_Order $order The WooCommerce order object.
	 *
	 * @return void
	 * @author Fabian Böttcher <fabian.boettcher@payone.de>
	 */
	protected function set_customer_ip_from_order( \WC_Order $order ) {
		// Get IP from order object.
		$ip = get_post_meta( $order->get_id(), '_customer_ip_address', true );

		// Validate the customer IP.
		$ip = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );

		// Append a valid IP address to PAYONE API request data.
		if ( $ip ) {
			$this->set( 'ip', $ip );
		}
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return int
	 */
	protected function get_next_sequencenumber( \WC_Order $order ) {
		$sequencenumber = 1 + (int) $order->get_meta( '_sequencenumber' );
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

	/**
	 * @param \WC_Order|\WC_Cart|OrderRefund $orderOrCartOrRefund
	 *
	 * @return array
	 */
	public function add_article_list_to_transaction( $orderOrCartOrRefund ) {
		if ( $orderOrCartOrRefund instanceof \WC_Order ) {
			$article_list = $this->get_article_list_for_transaction_from_order( $orderOrCartOrRefund );
		} elseif ( $orderOrCartOrRefund instanceof \WC_Cart ) {
			$article_list = $this->get_article_list_for_transaction_from_cart( $orderOrCartOrRefund );
		} else {
			$article_list = $this->get_article_list_for_transaction_from_refund( $orderOrCartOrRefund );
		}

		foreach ( $article_list as $n => $article ) {
			$this->set( 'id[' . $n . ']', substr( $article['id'], 0, self::FIELD_LENGTH_ID_N ) );
			$this->set( 'pr[' . $n . ']', $article['pr'] );
			$this->set( 'no[' . $n . ']', substr( $article['no'], 0, self::FIELD_LENGTH_NO_N ) );
			$this->set( 'de[' . $n . ']', substr( $article['de'], 0, self::FIELD_LENGTH_DE_N ) );
			$this->set( 'va[' . $n . ']', substr( $article['va'], 0, self::FIELD_LENGTH_VA_N ) );
			$this->set( 'it[' . $n . ']', $article['it'] );
		}
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	protected function get_article_list_for_transaction_from_order( \WC_Order $order ) {
		$articles  = [];
		$discounts = [];
		$n         = 1;
		foreach ( $order->get_items() as $item_id => $item_data ) {
			$product        = $item_data->get_product();
			$data           = $item_data->get_data();
			$va             = (int) round( 100 * Plugin::get_tax_rate_for_item_data( $data ) );
			$price_all      = $data['subtotal'] + $data['subtotal_tax'];
			$discount       = $price_all - ( $data['total'] + $data['total_tax'] );
			$discount       = (int) round( 100 * $discount );
			$price_one      = $price_all / $item_data->get_quantity();
			$price          = (int) round( 100 * $price_one );
			$sku            = $product->get_sku() ?: $product->get_id();
			$articles[ $n ] = [
				'id' => $sku,
				'pr' => $price,
				'no' => $item_data->get_quantity(),
				'de' => $product->get_name(),
				'va' => $va,
				'it' => 'goods',
			];
			$n ++;

			if ( ! isset( $discounts[ $va ] ) ) {
				$discounts[ $va ] = 0;
			}
			$discounts[ $va ] += $discount;
		}
		foreach ( $order->get_shipping_methods() as $item_id => $item_data ) {
			$data           = $item_data->get_data();
			$va             = Plugin::get_tax_rate_for_item_data( $data );
			$price          = (int) round( 100 * ( $data['total'] + $data['total_tax'] ) );
			$articles[ $n ] = [
				'id' => $item_data->get_instance_id(),
				'pr' => $price,
				'no' => 1,
				'de' => $data['name'],
				'va' => 100 * $va,
				'it' => 'shipment',
			];
			$n ++;
		}

		$discountIdx = 1;
		foreach ( $discounts as $discountVa => $discount ) {
			if ( $discount > 0 ) {
				$articles[ $n ] = [
					'id' => "{$order->get_id()}-{$discountIdx}",
					'pr' => - $discount,
					'no' => 1,
					'de' => __( 'Discount', 'payone-woocommerce-3' ),
					'va' => $discountVa,
					'it' => 'voucher',
				];
				$n ++;
				$discountIdx ++;
			}
		}

		return $articles;
	}

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return array
	 */
	protected function get_article_list_for_transaction_from_cart( \WC_Cart $cart ) {
		$articles  = [];
		$discounts = [];
		$n         = 1;

		foreach ( $cart->get_cart_contents() as $item_data ) {
			$product        = $item_data['data'];
			$data           = [
				'subtotal'     => $item_data['line_subtotal'],
				'subtotal_tax' => $item_data['line_subtotal_tax'],
				'total'        => $item_data['line_total'],
				'total_tax'    => $item_data['line_tax'],
			];
			$va             = (int) round( 100 * Plugin::get_tax_rate_for_item_data( $data ) );
			$price_all      = $data['subtotal'] + $data['subtotal_tax'];
			$discount       = $price_all - ( $data['total'] + $data['total_tax'] );
			$discount       = (int) round( 100 * $discount );
			$price_one      = $price_all / $item_data['quantity'];
			$price          = (int) round( 100 * $price_one );
			$sku            = $product->get_sku() ?: $product->get_id();
			$articles[ $n ] = [
				'id' => $sku,
				'pr' => $price,
				'no' => $item_data['quantity'],
				'de' => $product->get_name(),
				'va' => $va,
				'it' => 'goods',
			];
			$n ++;

			if ( ! isset( $discounts[ $va ] ) ) {
				$discounts[ $va ] = 0;
			}
			$discounts[ $va ] += $discount;
		}

		foreach ( $cart->calculate_shipping() as $item_data ) {
			/** @var \WC_Shipping_Rate $item_data */
			$taxes = $item_data->get_taxes();
			$tax   = 0.0;
			if ( $taxes ) {
				$tax = array_shift( $taxes );
			}

			$data           = [
				'subtotal'     => $item_data->get_cost(),
				'subtotal_tax' => $tax,
				'total'        => $item_data->get_cost(),
				'total_tax'    => $tax,
			];
			$va             = Plugin::get_tax_rate_for_item_data( $data );
			$price          = (int) round( 100 * ( $data['total'] + $data['total_tax'] ) );
			$articles[ $n ] = [
				'id' => $item_data->get_instance_id(),
				'pr' => $price,
				'no' => 1,
				'de' => $item_data->get_label(),
				'va' => 100 * $va,
				'it' => 'shipment',
			];
			$n ++;
		}

		// Just use the sum of all discounts as one position
		if ( $cart->has_discount() ) {
			$discount       = round( $cart->get_discount_total() + $cart->get_discount_tax(), 2 );
			$data           = [
				'subtotal'     => $cart->get_discount_total(),
				'subtotal_tax' => $cart->get_discount_tax(),
				'total'        => $cart->get_discount_total(),
				'total_tax'    => $cart->get_discount_tax(),
			];
			$va             = Plugin::get_tax_rate_for_item_data( $data );
			$articles[ $n ] = [
				'id' => 'd-' . $n,
				'pr' => - 100 * $discount,
				'no' => 1,
				'de' => __( 'Discount', 'payone-woocommerce-3' ),
				'va' => 100 * $va,
				'it' => 'voucher',
			];
		}
		$n ++;

		return $articles;
	}

	/**
	 * @param OrderRefund $refund
	 *
	 * @return array
	 */
	protected function get_article_list_for_transaction_from_refund( OrderRefund $refund = null ) {
		if ( ! $refund ) {
			return [];
		}

		$articles = [];
		$n        = 1;

		foreach ( $refund->get_items() as $item ) {
			$product        = $item->get_product();
			$data           = $item->get_data();
			$va             = (int) round( 100 * Plugin::get_tax_rate_for_item_data( $data ) );
			$price_all      = $data['subtotal'] + $data['subtotal_tax'];
			$price_one      = $price_all / $item->get_quantity();
			$price          = (int) round( 100 * $price_one );
			$sku            = $product->get_sku() ?: $product->get_id();
			$articles[ $n ] = [
				'id' => $sku,
				'pr' => - $price,
				'no' => - $item->get_quantity(),
				'de' => $product->get_name(),
				'va' => $va,
				'it' => 'goods',
			];
			$n ++;
		}
		$refund_data = $refund->get_data();
		if ( $refund_data['shipping_total'] != 0 ) {
			$va             = Plugin::get_tax_rate_for_total( $refund_data['shipping_total'], $refund_data['shipping_tax'] );
			$articles[ $n ] = [
				'id' => 'shipment-' . $n,
				'pr' => (int) round( 100 * ( $refund_data['shipping_total'] + $refund_data['shipping_tax'] ) ),
				'no' => 1,
				'de' => __( 'Shipping', 'payone-woocommerce-3' ),
				'va' => 100 * $va,
				'it' => 'shipment',
			];
		}

		return $articles;
	}
}
