<?php

namespace Payone\Gateway;

class Invoice extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_invoice';

	public function __construct() {
		$this->id                 = self::GATEWAY_ID;
		$this->icon               = '';
		$this->has_fields         = true;
		$this->method_title       = 'BS PAYONE Rechnung';
		$this->method_description = 'method_description';
		$this->supports           = [ 'products', 'refunds' ];

		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->get_option( 'title' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public function init_form_fields() {
		$this->form_fields = [
			'enabled'     => [
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Kauf auf Rechnung ermöglichen', 'woocommerce' ),
				'default' => 'yes',
			],
			'title'       => [
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Rechnung', 'woocommerce' ),
				'desc_tip'    => true,
			],
			'description' => [
				'title'   => __( 'Customer Message', 'woocommerce' ),
				'type'    => 'textarea',
				'default' => '',
			],
		];
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/invoice/payment-form.php';
	}

	public function process_refund( $order_id, $amount = null, $reason = '' ) {

		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\Refund();
		$transaction->set('amount', -$amount * 100);
		$response    = $transaction->execute( $order );

		// @todo wirklich testen, ob der refund funktioniert hat
		return true;
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\Invoice();
		$response    = $transaction->execute( $order );

		// @todo Fehler abfangen und transaktions-ID in Order ablegen.
		// @todo Bei Kauf auf Rechnung anderer Status und Order abschließen?

		$order->set_transaction_id( $response->get( 'txid' ) );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status( 'on-hold', __( 'Rechnung wurde geschickt', 'woocommerce' ) );

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
}