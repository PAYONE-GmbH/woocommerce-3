<?php

/*
Plugin Name: BS PAYONE WooCommerce 3
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.3.0
Author: BS PAYONE
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

define( 'PAYONE_PLUGIN_VERSION', '1.3.0' );
define( 'PAYONE_PLUGIN_PATH', __DIR__ );
define( 'PAYONE_VIEW_PATH', PAYONE_PLUGIN_PATH . '/views' );

require_once 'src/autoload.php';

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$payonePlugin = new \Payone\Plugin();
	add_action( 'init', [ $payonePlugin, 'add_callback_url' ] );
	add_action( 'plugins_loaded', [ $payonePlugin, 'init' ] );
}
