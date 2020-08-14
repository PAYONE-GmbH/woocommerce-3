<?php

/*
Plugin Name: BS PAYONE WooCommerce
Plugin URI: https://www.bspayone.com/
Description: BS PAYONE Payment for WooCommerce 3
Version: 1.7.1
Author: pooliestudios
Author URI: https://pooliestudios.com
License: MIT
*/

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

define( 'PAYONE_PLUGIN_VERSION', '1.7.1' );
define( 'PAYONE_PLUGIN_PATH', __DIR__ );
define( 'PAYONE_VIEW_PATH', PAYONE_PLUGIN_PATH . '/views' );

require_once 'src/autoload.php';

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$payonePlugin = new \Payone\Plugin();
	add_action( 'init', [ $payonePlugin, 'add_callback_url' ] );
	add_action( 'plugins_loaded', [ $payonePlugin, 'init' ] );
}
