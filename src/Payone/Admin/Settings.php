<?php

namespace Payone\Admin;

use Payone\Admin\Option\Account;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Settings {
	private $account;

	public function init() {
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
		add_action( 'admin_init', [ $this, 'register_options' ] );
	}

	public function plugin_menu() {
		add_menu_page( __( 'BS PAYONE Settings', 'payone' ),
			'BS PAYONE',
			'manage_options',
			'payone-settings-account',
			[ $this, 'render_account_options' ] );
		add_submenu_page( 'payone-settings-account',
			__( 'Transaction Status Log', 'payone' ),
			__( 'Transaction Status Log', 'payone' ),
			'manage_options',
			'payone-transaction-log',
			[ $this, 'transaction_log' ] );
		add_submenu_page( 'payone-settings-account',
			__( 'API-Log', 'payone' ),
			__( 'API-Log', 'payone' ),
			'manage_options',
			'payone-api-log',
			[ $this, 'api_log' ] );
	}

	public function register_options() {
		$this->account = new Account();
		$this->account->register();
	}

	public function render_account_options() {
		$this->account->render();
	}

	public function transaction_log() {
		$apiLog = new TransactionLog();

		if ( isset( $_GET['id'] ) && (int) $_GET['id'] ) {
			$apiLog->displaySingle( (int) $_GET['id'] );
		} else {
			$apiLog->displayList();
		}
	}

	public function api_log() {
		$apiLog = new ApiLog();

		if ( isset( $_GET['id'] ) && (int) $_GET['id'] ) {
			$apiLog->displaySingle( (int) $_GET['id'] );
		} else {
			$apiLog->displayList();
		}
	}
}