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

		$gateways = [
			\Payone\Gateway\CreditCard::GATEWAY_ID      => \Payone\Gateway\CreditCard::class,
			\Payone\Gateway\SepaDirectDebit::GATEWAY_ID => \Payone\Gateway\SepaDirectDebit::class,
			\Payone\Gateway\PrePayment::GATEWAY_ID      => \Payone\Gateway\PrePayment::class,
			\Payone\Gateway\Invoice::GATEWAY_ID         => \Payone\Gateway\Invoice::class,
		];

		foreach ( $gateways as $gateway ) {
			add_filter( 'woocommerce_payment_gateways', [ $gateway, 'add' ] );
		}

		add_action( 'woocommerce_order_status_changed', [ $this, 'order_status_changed' ], 10, 3 );

		$plugin_rel_path = dirname( plugin_basename(__FILE__) ) . '/../../lang/';
		load_plugin_textdomain( 'payone-woocommerce-3', false, $plugin_rel_path);
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

				try {
					$this->process_callback();
					$response = 'TSOK';
				} catch (\Exception $e) {
					$response .= ' (' . $e->getMessage() . ')';
				}
			}

			echo $response;
			exit();
		}
	}

	public function process_callback() {
		$transaction_status = TransactionStatus::constructFromPostParameters();

		// @todo DEV-Modus entfernen. Wird genutzt um auf dem DEV-Server die Transaktionen nur zu loggen,
		// @todo statt sie zu bearbeiten.
		$do_process_callback = false;
		if ( $transaction_status->get_order_id() < 1000000 ) {
			$do_process_callback = true;
		} elseif ( defined( 'PAYONE_LOCALDEV' ) && PAYONE_LOCALDEV ) {
			$do_process_callback = true;
		} elseif ( ! defined( 'PAYONE_DEV_MODE' ) || ! PAYONE_DEV_MODE ) {
			$do_process_callback = true;
		}
		if ( $do_process_callback ) {
			$order = new \WC_Order( $transaction_status->get_order_id() );

			$gateway = $this->get_gateway_for_order( $order );
			$gateway->process_transaction_status( $transaction_status, $order );
		}
	}

	public function order_status_changed( $id, $from_status, $to_status ) {
		$order   = new \WC_Order( $id );
		$gateway = $this->get_gateway_for_order( $order );

		if ( method_exists( $gateway, 'order_status_changed' ) ) {
			$gateway->order_status_changed( $order, $from_status, $to_status );
		}
	}

	private function is_valid_callback() {
		$options   = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
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
	private function get_gateway_for_order( $order ) {
		// @todo Was tun, wenn es das Gateway nicht gibt?
		return $this->get_gateway( $order->get_payment_method() );
	}

	/**
	 * @param string $gateway_id
	 *
	 * @return null|GatewayBase
	 */
	private function get_gateway( $gateway_id ) {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		foreach ( $payment_gateways as $payment_gateway_id => $payment_gateway ) {
			if ( $gateway_id === $payment_gateway_id ) {
				return $payment_gateway;
			}
		}

		return null;
	}
}