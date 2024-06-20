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
	 * Enqueue styles.
	 */
	public function admin_styles() {
		// Register admin styles.
		wp_register_style( 'payone_admin_styles', PAYONE_PLUGIN_URL . '/assets/css/admin.css', [], PAYONE_PLUGIN_VERSION );

		// Admin styles for WC pages only.
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->id, wc_get_screen_ids(), true ) ) {
			wp_enqueue_style( 'payone_admin_styles' );
		}
	}
}
