<?php

namespace Payone;

use Payone\Database\Migration;
use Payone\Gateway\GatewayBase;
use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Log;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Plugin {
	const CALLBACK_SLUG = 'payone-callback';

	/**
	 * @var GatewayBase[]
	 */
	private $gateways;

	/**
	 * @todo Evtl. Zugriff über file_get_contents('php://input') realisieren, wenn der Server file_get_contents zulässt
	 *
	 * @return array
	 */
	public static function get_post_vars() {
		return $_POST;
	}

	public function init() {
		$migration = new Migration();
		$migration->run();

		if ( is_admin() ) {
			$settings = new \Payone\Admin\Settings();
			$settings->init();
		}

		$this->gateways = [
			\Payone\Gateway\CreditCard::GATEWAY_ID      => new \Payone\Gateway\CreditCard(),
			\Payone\Gateway\SepaDirectDebit::GATEWAY_ID => new \Payone\Gateway\SepaDirectDebit(),
			\Payone\Gateway\PrePayment::GATEWAY_ID      => new \Payone\Gateway\PrePayment(),
			\Payone\Gateway\Invoice::GATEWAY_ID         => new \Payone\Gateway\Invoice(),
		];

		foreach ( $this->gateways as $gateway ) {
			add_filter( 'woocommerce_payment_gateways', [ $gateway, 'add' ] );
		}

		add_action( 'woocommerce_order_status_changed', [ $this, 'order_status_changed' ], 10, 3 );
	}

	public function add_callback_url() {
		add_rewrite_rule( '^' . self::CALLBACK_SLUG . '/?$', 'index.php?' . self::CALLBACK_SLUG . '=true', 'top' );
		add_filter( 'query_vars', [ $this, 'add_rewrite_var' ] );
		add_action( 'template_redirect', [ $this, 'catch_payone_callback' ] );
	}

	public function add_rewrite_var( $vars ) {
		$vars[] = self::CALLBACK_SLUG;

		return $vars;
	}

	public function catch_payone_callback() {
		if ( get_query_var( self::CALLBACK_SLUG ) ) {

			$response = 'ERROR';
			if ( $this->is_valid_callback() ) {
				$this->debug_payone_callback();
				Log::constructFromPostVars();

				if ( defined( 'PAYONE_PROCESS_CALLBACKS' ) && PAYONE_PROCESS_CALLBACKS ) {
					// @todo remove
					$this->process_callback();
				}
				$response = 'TSOK';
			}

			echo $response;
			exit();
		}
	}

	public function process_callback() {
		$transaction_status = TransactionStatus::constructFromPostParameters();
		$order = new \WC_Order( $transaction_status->get_order_id() );

		$gateway = $this->getGatewayForOrder( $order );
		$gateway->process_transaction_status( $transaction_status, $order );
	}

	public function order_status_changed( $id, $from_status, $to_status ) {
		$order = new \WC_Order( $id );
		$gateway = $this->getGatewayForOrder($order);

		if ( method_exists( $gateway, 'order_status_changed' ) ) {
			$gateway->order_status_changed( $order, $from_status, $to_status );
		}
	}

	private function is_valid_callback() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
		$post_vars = self::get_post_vars();

		return isset( $post_vars['key'] ) && $post_vars['key'] === hash( 'md5', $options['key'] );
	}

	private function debug_payone_callback() {
		if ( ! defined( 'PAYONE_LOCALDEV' ) || ! PAYONE_LOCALDEV ) {
			$message = json_encode( $_SERVER ) . "\n\n" . json_encode( $_POST ) . "\n\n";
			mail( 'dirk@pooliestudios.com', '[PAYONE CALLBACK]', $message );
		}
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return null|GatewayBase
	 */
	private function getGatewayForOrder( $order ) {
		$gateway_id = $order->get_payment_method();

		// @todo Was tun, wenn es das Gateway nicht gibt?
		return isset( $this->gateways[$gateway_id] ) ? $this->gateways[$gateway_id] : null;
	}
}