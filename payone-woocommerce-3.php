<?php

/**
 * Plugin PAYONE Payment for WooCommerce
 * Version: 1.6.1
 * Plugin URI: https://www.payone.com/
 * Description: Integration of PAYONE payment into your WooCommerce store.
 * Author: PAYONE GmbH
 * Author URI: https://www.payone.com/
 * Requires at least: 5.0
 * Tested up to: 5.3
 */

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

define( 'PAYONE_PLUGIN_VERSION', '1.6.1' );
define( 'PAYONE_PLUGIN_PATH', __DIR__ );
define( 'PAYONE_VIEW_PATH', PAYONE_PLUGIN_PATH . '/views' );

require_once 'src/autoload.php';

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$payonePlugin = new \Payone\Plugin();
	add_action( 'init', [ $payonePlugin, 'add_callback_url' ] );
	add_action( 'plugins_loaded', [ $payonePlugin, 'init' ] );
}
