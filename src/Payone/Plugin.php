<?php

namespace Payone;

use Payone\Database\Migration;
use Payone\Transaction\Log;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Plugin {
	const CALLBACK_SLUG = 'payone-callback';

	public function init() {
		$migration = new Migration();
		$migration->run();

		if ( is_admin() ) {
			$settings = new \Payone\Admin\Settings();
			$settings->init();
		}

		$gateways = [
			\Payone\Gateway\CreditCard::GATEWAY_ID      => new \Payone\Gateway\CreditCard(),
			\Payone\Gateway\SepaDirectDebit::GATEWAY_ID => new \Payone\Gateway\SepaDirectDebit(),
			\Payone\Gateway\PrePayment::GATEWAY_ID      => new \Payone\Gateway\PrePayment(),
			\Payone\Gateway\Invoice::GATEWAY_ID         => new \Payone\Gateway\Invoice(),
		];

		foreach ( $gateways as $gateway ) {
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

			$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
			if ( isset( $_POST['key'] ) && $_POST['key'] === hash( 'md5', $options['key'] ) ) {
				$message = print_r( $_SERVER, 1 ) . "\n\n" . print_r( $_POST, 1 ) . "\n\n";
				mail( 'dirk@pooliestudios.com', '[PAYONE CALLBACK]', $message );

				$transaction_id = isset($_POST['txid']) ? $_POST['txid'] : '';
				$transaction_log_entry = new Log();
				$transaction_log_entry->setData(\Payone\Payone\Api\DataTransfer::constructFromArray( $_POST ));
				$transaction_log_entry->setTransactionId($transaction_id);
				$transaction_log_entry->save();

				echo 'TSOK';
			} else {
				echo 'ERROR';
			}
			exit();
		}
	}

	public function order_status_changed($id, $from_status, $to_status) {
		// @todo Muss PAYONE kontaktiert werden?
	}
}