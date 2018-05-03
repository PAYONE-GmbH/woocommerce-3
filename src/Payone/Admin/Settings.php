<?php

namespace Payone\Admin;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

class Settings {
	/**
	 * @var \Payone\Admin\Option\Account
	 */
	private $account;

	/**
	 * @var \Payone\Admin\Option\AddressChecks
	 */
	private $address_checks;

	/**
	 * @var \Payone\Admin\Option\CreditCheck
	 */
	private $credit_check;

	public function init() {
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
		add_action( 'admin_init', [ $this, 'register_options' ] );
	}

	public function plugin_menu() {
		add_menu_page( __( 'Payone Settings', 'payone' ),
			__( 'BS PAYONE', 'payone' ),
			'manage_options',
			'payone-settings-account',
			[ $this, 'render_account_options' ] );
		/*
		add_submenu_page( 'payone-settings-account',
			__( 'Address Checks', 'payone' ),
			__( 'Address Checks', 'payone' ),
			'manage_options',
			'payone-address-checks',
			[ $this, 'address_checks' ] );
		add_submenu_page( 'payone-settings-account',
			__( 'Credit Check', 'payone' ),
			__( 'Credit Check', 'payone' ),
			'manage_options',
			'payone-credit-check',
			[ $this, 'credit_check' ] );
		*/
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
		$this->account = new \Payone\Admin\Option\Account();
		$this->account->register();

		$this->address_checks = new \Payone\Admin\Option\AddressChecks();
		$this->address_checks->register();

		$this->credit_check = new \Payone\Admin\Option\CreditCheck();
		$this->credit_check->register();
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

	public function address_checks() {
		$this->address_checks->render();
	}

	public function credit_check() {
		$this->credit_check->render();
	}
}