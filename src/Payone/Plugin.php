<?php

namespace Payone;

use Payone\Database\Migration;
use Payone\Gateway\GatewayBase;
use Payone\Gateway\SepaDirectDebit;
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

		add_action( 'woocommerce_after_checkout_form', [$this, 'add_javascript']);
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_callback_url( $type = 'transaction' ) {
		$url = get_site_url( null, self::CALLBACK_SLUG . '/' );
		if ($type !== 'transaction') {
			$url .= '?type=' . $type;
		}

		return esc_url( $url );
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

			if ( $this->is_callback_after_redirect() ) {
				return $this->process_callback_after_redirect();
			} elseif ( $this->is_manage_mandate_callback() ) {
				return $this->process_manage_mandate_callback();
			}

			$response = 'ERROR';
			if ( $this->is_valid_transaction_callback() ) {
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
		$transaction_status = TransactionStatus::construct_from_post_parameters();

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
			$transaction_status
				->get_gateway()
				->process_transaction_status( $transaction_status );
		}
	}

	public function order_status_changed( $id, $from_status, $to_status ) {
		$order   = new \WC_Order( $id );
		$gateway = $this->get_gateway_for_order( $order );

		if ( method_exists( $gateway, 'order_status_changed' ) ) {
			$gateway->order_status_changed( $order, $from_status, $to_status );
		}
	}

	/**
	 * @todo IP-Range von PAYONE testen
	 * 
	 * @return bool
	 */
	private function is_valid_transaction_callback() {
		$options   = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
		$post_vars = self::get_post_vars();

		return isset( $post_vars['key'] ) && $post_vars['key'] === hash( 'md5', $options['key'] );
	}

	/**
	 * @return bool
	 */
	private function is_callback_after_redirect() {
		$allowed_redirect_types = [ 'success', 'error', 'return' ];
		if ( isset( $_GET['type'] ) && in_array( $_GET['type'], $allowed_redirect_types, true)
		     && isset( $_GET['oid'] ) && (int)$_GET['oid']
		) {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function process_callback_after_redirect() {
		$order_id = (int)$_GET['oid'];

		$order = new \WC_Order( $order_id );
		$gateway = self::get_gateway_for_order( $order );

		return $gateway->process_payment( $order_id );
	}

	/**
	 * @return bool
	 */
	private function is_manage_mandate_callback() {
		if ( isset( $_GET['type'] ) && $_GET['type'] === 'ajax-manage-mandate') {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function process_manage_mandate_callback() {
		$gateway = self::find_gateway( SepaDirectDebit::GATEWAY_ID );

		return $gateway->process_manage_mandate( $_POST );
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
	public static function get_gateway_for_order( \WC_Order $order ) {
		// @todo Was tun, wenn es das Gateway nicht gibt?
		return self::find_gateway( $order->get_payment_method() );
	}

	public function add_javascript() {
		include PAYONE_VIEW_PATH . '/gateway/common/checkout.js.php';
	}

	/**
	 * @param string $gateway_id
	 *
	 * @return null|GatewayBase
	 */
	private static function find_gateway( $gateway_id ) {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		foreach ( $payment_gateways as $payment_gateway_id => $payment_gateway ) {
			if ( $gateway_id === $payment_gateway_id ) {
				return $payment_gateway;
			}
		}

		return null;
	}
}