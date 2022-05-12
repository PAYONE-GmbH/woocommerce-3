<?php

/**
 * Plugin Name: PAYONE Payment for WooCommerce
 * Version: 2.1.0
 * Plugin URI: https://www.payone.com/
 * Description: Integration of PAYONE payment into your WooCommerce store.
 * Author: PAYONE GmbH
 * Author URI: https://www.payone.com/
 * Requires at least: 5.0
 * Tested up to: 5.9.3
 */

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

define( 'PAYONE_PLUGIN_VERSION', '2.1.0' );
define( 'PAYONE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PAYONE_PLUGIN_PATH', __DIR__ );
define( 'PAYONE_VIEW_PATH', PAYONE_PLUGIN_PATH . '/views' );

require_once 'src/autoload.php';

$payonePlugin = null;
add_action( 'woocommerce_loaded', function() {
    global $payonePlugin;

    $payonePlugin = new \Payone\Plugin();
    $payonePlugin->init();
} );
