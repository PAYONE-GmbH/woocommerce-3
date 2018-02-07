<?php

namespace Payone\Admin;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class AddressChecks {
	public function display() {
		include PAYONE_VIEW_PATH . '/admin/address-checks.php';
	}
}