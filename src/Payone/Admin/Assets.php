<?php

namespace Payone\Admin;

class Assets {

	/**
	 * Hook in tabs.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
	}

	/**
	 * Enqueue styles and scripts on WooCommerce and PAYONE admin pages.
	 */
	public function admin_styles() {
		wp_register_style( 'payone_admin_styles', PAYONE_PLUGIN_URL . 'assets/css/admin.css', [], PAYONE_PLUGIN_VERSION );
		wp_register_script( 'payone_admin_scripts', PAYONE_PLUGIN_URL . 'assets/js/admin.js', [], PAYONE_PLUGIN_VERSION, true );

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$is_wc_screen     = function_exists( 'wc_get_screen_ids' ) && in_array( $screen->id, wc_get_screen_ids(), true );
		$is_payone_screen = strpos( $screen->id, 'payone' ) !== false;
		if ( $is_wc_screen || $is_payone_screen ) {
			wp_enqueue_style( 'payone_admin_styles' );
			wp_enqueue_script( 'payone_admin_scripts' );
		}
	}
}
