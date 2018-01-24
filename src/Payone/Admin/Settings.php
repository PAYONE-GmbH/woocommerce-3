<?php

namespace Payone\Admin;

use Payone\Admin\Option\Account;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Settings {
	private $account;

	public function init() {
		add_action( 'admin_menu', [ $this, 'pluginMenu' ] );
		add_action( 'admin_init', [ $this, 'registerOptions' ] );
	}

	public function pluginMenu() {
		add_menu_page( 'Payone Einstellungen',
			'BS PAYONE',
			'manage_options',
			'payone-settings-account',
			[ $this, 'renderAccountOptions' ] );
		add_submenu_page( 'payone-settings-account',
			'API-Log',
			'API-Log',
			'manage_options',
			'payone-api-log',
			[ $this, 'apiLog' ] );
	}

	public function registerOptions() {
		$this->account = new Account();
		$this->account->register();
	}

	public function renderAccountOptions() {
		$this->account->render();
	}

	public function apiLog() {
		$apiLog = new ApiLog();

		if ( isset( $_GET['id'] ) && (int) $_GET['id'] ) {
			$apiLog->displaySingle( (int) $_GET['id'] );
		} else {
			$apiLog->displayList();
		}
	}
}