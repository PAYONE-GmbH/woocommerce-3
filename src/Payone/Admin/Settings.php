<?php

namespace Payone\Admin;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Settings
{
    public function init()
    {
        add_action( 'admin_menu', [$this, 'pluginMenu'] );
    }

    /** Step 1. */
    public function pluginMenu()
    {
        add_menu_page('Payone Einstellungen', 'BS PAYONE', 'manage_options', 'payone-settings-start', [$this, 'pluginOptions']);
        add_submenu_page( 'payone-settings-start', 'API-Log', 'API-Log', 'manage_options', 'payone-api-log', [$this, 'apiLog']);
    }

    /** Step 3. */
    public function pluginOptions()
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        include PAYONE_VIEW_PATH.'/admin/options.php';
    }

    public function apiLog()
    {
        $apiLog = new ApiLog();

        if (isset($_GET['id']) && (int)$_GET['id']) {
            $apiLog->displaySingle((int)$_GET['id']);
        } else {
            $apiLog->displayList();
        }
    }
}