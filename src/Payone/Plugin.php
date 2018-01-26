<?php

namespace Payone;

use Payone\Database\Migration;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Plugin {
	public function init() {
		$migration = new Migration();
		$migration->run();

		if ( is_admin() ) {
			$settings = new \Payone\Admin\Settings();
			$settings->init();
		}

		$gateways = [
			\Payone\Gateway\CreditCard::GATEWAY_ID => new \Payone\Gateway\CreditCard(),
			\Payone\Gateway\SepaDirectDebit::GATEWAY_ID => new \Payone\Gateway\SepaDirectDebit(),
			\Payone\Gateway\PrePayment::GATEWAY_ID => new \Payone\Gateway\PrePayment(),
			\Payone\Gateway\Invoice::GATEWAY_ID => new \Payone\Gateway\Invoice(),
		];

		foreach ( $gateways as $gateway ) {
			add_filter( 'woocommerce_payment_gateways', [ $gateway, 'add' ] );
		}
	}
}