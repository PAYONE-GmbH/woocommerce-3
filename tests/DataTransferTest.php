<?php

use PHPUnit\Framework\TestCase;

final class DataTransferTest extends TestCase {
	public function testGetAndSet() {
		$dataTransfer = new \Payone\Payone\Api\DataTransfer();

		$dataTransfer->set( 'test', '1234' );
		$dataTransfer->set( 'cardpan', '4111111111111111' );
		$dataTransfer->set( 'iban', 'DE85123456782599100003' );
		$dataTransfer->set( 'street', 'Hauptstrasse 1' );

		$this->assertEquals( '1234', $dataTransfer->get( 'test' ) );
		$this->assertEquals( '4111111111111111', $dataTransfer->get( 'cardpan' ) );
		$this->assertEquals( 'DE85123456782599100003', $dataTransfer->get( 'iban' ) );
		$this->assertEquals( 'Hauptstrasse 1', $dataTransfer->get( 'street' ) );
	}

	public function testAnonymization() {
		$dataTransfer = new \Payone\Payone\Api\DataTransfer();

		$dataTransfer->set( 'test', '1234' );
		$dataTransfer->set( 'cardpan', '4111111111111111' );
		$dataTransfer->set( 'iban', 'DE85123456782599100003' );
		$dataTransfer->set( 'street', 'Hauptstrasse 1' );

		$dataTransfer->anonymize_parameters();

		$this->assertEquals( '1234', $dataTransfer->get( 'test' ) );
		$this->assertEquals( '4111xxxxxxxx1111', $dataTransfer->get( 'cardpan' ) );
		$this->assertEquals( 'DE85xxxxxxxxxxxxxxx003', $dataTransfer->get( 'iban' ) );
		$this->assertEquals( 'Hxxxxxxxxxxxx1', $dataTransfer->get( 'street' ) );
	}

	public function testRemove() {
		$dataTransfer = new \Payone\Payone\Api\DataTransfer();

		$dataTransfer->set( 'test', '1234' );
		$this->assertEquals( '1234', $dataTransfer->get( 'test' ) );
		$this->assertEquals( [ 'test' => '1234' ], $dataTransfer->get_all() );

		$dataTransfer->remove( 'test' );
		$this->assertEquals( [], $dataTransfer->get_all() );
	}

	public function test_shortened_data_in_get_serialized_parameters() {
		$dataTransfer = new \Payone\Payone\Api\DataTransfer();

		$long_data = '';
		for ( $i = 0; $i < 20; $i ++ ) {
			$long_data .= md5( time() );
		}
		$dataTransfer->set( '_DATA', $long_data );
		$this->assertEquals( 20 * 32, strlen( $dataTransfer->get( '_DATA' ) ) );
		$this->assertEquals( 212, strlen( $dataTransfer->get_serialized_parameters() ) );
	}

	public function test_removed_empty_data_in_get_postfields_from_parameters() {
		$dataTransfer = new \Payone\Payone\Api\DataTransfer();
		$dataTransfer->set( 'filled', 'filled-data' );
		$dataTransfer->set( 'not-filled', '' );

		$this->assertFalse( strpos( $dataTransfer->get_postfields_from_parameters(), 'not-filled' ) );
	}
}
