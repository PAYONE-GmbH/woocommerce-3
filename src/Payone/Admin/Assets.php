<?php

namespace Payone\Admin;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Assets {

    /**
     * Hook in tabs.
     */
    public function init() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
    }

    /**
     * Enqueue styles.
     */
    public function admin_styles()
    {
        global $wp_scripts;

        $version = PAYONE_PLUGIN_VERSION;
        $screen = get_current_screen();
        $screen_id = $screen ? $screen->id : '';

        // Register admin styles.
        wp_register_style('payone_admin_styles', PAYONE_PLUGIN_URL . '/assets/css/admin.css', [], $version);

        // Admin styles for WC pages only.
        if ( in_array( $screen_id, wc_get_screen_ids() ) ) {
            wp_enqueue_style('payone_admin_styles');
        }
    }
}
