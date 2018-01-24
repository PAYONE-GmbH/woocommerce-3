<?php

namespace Payone\Database;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Migration {
	const PAYONE_DB_VERSION = '1.0';
	const PAYONE_DB_VERSION_OPTION = 'payone_db_version';

	public function run() {
		global $wpdb;
		$installed_ver = get_option( self::PAYONE_DB_VERSION_OPTION );

		if ( $installed_ver !== self::PAYONE_DB_VERSION ) {

			$table_name      = $wpdb->prefix . \Payone\Payone\Api\Log::TABLE_NAME;
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE {$table_name} (
						id int(11) unsigned NOT NULL AUTO_INCREMENT,
  						request text,
  						response text,
  						created_at datetime NOT NULL,
  						PRIMARY KEY (id)
					) {$charset_collate};";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( self::PAYONE_DB_VERSION_OPTION, self::PAYONE_DB_VERSION );
		}
	}
}
