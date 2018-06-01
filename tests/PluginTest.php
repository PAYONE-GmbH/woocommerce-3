<?php

use PHPUnit\Framework\TestCase;

// '213.178.72.196', '213.178.72.197', '217.70.200.0/24', '185.60.20.0/24'

final class PluginTest extends TestCase {
	public function testPayoneIpRanges() {

		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '213.178.72.196', '213.178.72.196' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '213.178.72.197', '213.178.72.197' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '217.70.200.3', '217.70.200.0/24' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '185.60.20.128', '185.60.20.0/24' ) );
		$this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '192.168.0.1', '213.178.72.196' ) );
		$this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '192.168.0.1', '185.60.20.0/24' ) );
		$this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '185.60.21.1', '185.60.20.0/24' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '192.168.65.178', '192.168.65.178' ) );
	}
}
