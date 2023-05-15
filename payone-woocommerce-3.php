<?php

/**
 * Plugin Name: PAYONE Payment for WooCommerce
 * Version: 2.5.2
 * Plugin URI: https://www.payone.com/
 * Description: Integration of PAYONE payment into your WooCommerce store.
 * Author: PAYONE GmbH
 * Author URI: https://www.payone.com/
 * Requires at least: 5.0
 * Tested up to: 6.2
 */

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

define( 'PAYONE_PLUGIN_VERSION', '2.5.2' );
define( 'PAYONE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PAYONE_PLUGIN_PATH', __DIR__ );
define( 'PAYONE_VIEW_PATH', PAYONE_PLUGIN_PATH . '/views' );

require_once 'src/autoload.php';

$payone_plugin = null;
add_action( 'woocommerce_loaded', function () {
	global $payone_plugin;

	$payone_plugin = new \Payone\Plugin();
	$payone_plugin->init();
} );
