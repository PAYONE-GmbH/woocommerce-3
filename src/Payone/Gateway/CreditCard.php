<?php

namespace Payone\Gateway;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class CreditCard extends \WC_Payment_Gateway implements GatewayInterface {
	const GATEWAY_ID = 'bs_payone_creditcard';

	public function __construct() {
		$this->id                 = self::GATEWAY_ID;
		$this->icon               = '';
		$this->has_fields         = true;
		$this->method_title       = 'BS PAYONE Kreditkarte';
		$this->method_description = 'method_description';

		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->get_option( 'title' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public function add( $methods ) {
		$methods[] = '\\Payone\\Gateway\\CreditCard';

		return $methods;
	}

	public function init_form_fields() {
		$this->form_fields = [
			'enabled'     => [
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Kreditkartenzahlung ermöglichen', 'woocommerce' ),
				'default' => 'yes',
			],
			'title'       => [
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Kreditkarte', 'woocommerce' ),
				'desc_tip'    => true,
			],
			'description' => [
				'title'   => __( 'Customer Message', 'woocommerce' ),
				'type'    => 'textarea',
				'default' => '',
			],
		];
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status( 'on-hold', __( 'Awaiting cheque payment', 'woocommerce' ) );

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	public function payment_fields() {
		return '<h1>test</h1>';
	}
}