<?php

use PHPUnit\Framework\TestCase;

// Some preparations for mocking the wordpress environment
if (!defined('PAYONE_PLUGIN_VERSION')) define('PAYONE_PLUGIN_VERSION', 'test');

function get_option( $key) {
	return [
		'account_id'      => '',
		'merchant_id'     => '',
		'portal_id'       => '',
		'key'             => '',
		'mode'            => 'test',
		'api_log'         => 0,
		'transaction_log' => 0,
	];
}

function get_plugins( $plugin_folder = '' ) {
	return [];
}

final class RequestTest extends TestCase {
	public function testCreateResponse() {
		$request = new \Payone\Payone\Api\Request();

		$response = $request->create_response("status=REDIRECT\nredirecturl=https://secure.pay1.de/3ds/redirect.php?md=20954722&txid=262491170\ntxid=262491170");

		$this->assertEquals( 'REDIRECT', $response->get( 'status' ) );
		$this->assertEquals( 'https://secure.pay1.de/3ds/redirect.php?md=20954722&txid=262491170', $response->get( 'redirecturl' ) );
		$this->assertEquals( '262491170', $response->get( 'txid' ) );
	}

	public function testCreateGetfileResponse() {
		$request = new \Payone\Payone\Api\Request();

		$pdfData = "%PDF-1.5\n.SOME-DATA..\n%%EOF";
		$response = $request->create_response($pdfData);

		$this->assertEquals( 'OK', $response->get( 'status' ) );
		$this->assertEquals( $pdfData, $response->get( '_DATA' ) );
	}
}
