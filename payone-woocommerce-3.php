<?php

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Blocks\Package;
use Payone\Gateway\PayoneBlocksSupport;

/**
 * Plugin Name: PAYONE Payment for WooCommerce
 * Version: 2.7.0
 * Plugin URI: https://www.payone.com/
 * Description: Integration of PAYONE payment into your WooCommerce store.
 * Author: PAYONE GmbH
 * Author URI: https://www.payone.com/
 * Requires at least: 5.0
 * Tested up to: 9.1.2
 */

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

define( 'PAYONE_PLUGIN_VERSION', '2.6.0' );
define( 'PAYONE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PAYONE_PLUGIN_PATH', __DIR__ );
define( 'PAYONE_VIEW_PATH', PAYONE_PLUGIN_PATH . '/views' );

require_once 'src/autoload.php';

$payone_plugin = null;
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
} );

add_action( 'woocommerce_loaded', function () {
	global $payone_plugin;

	$payone_plugin = new \Payone\Plugin();
	$payone_plugin->init();
} );

add_action( 'woocommerce_blocks_loaded', function () {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( PaymentMethodRegistry $payment_method_registry ) {
				$container = Package::container();
				// registers as shared instance.
				$container->register(
					PayoneBlocksSupport::class,
					function () {
						return new PayoneBlocksSupport();
					}
				);
				$payment_method_registry->register(
					$container->get( PayoneBlocksSupport::class )
				);
			},
			5
		);
	}
} );
