<?php

use Payone\Payone\Api\StateCode;
use PHPUnit\Framework\TestCase;

final class StateCodeTest extends TestCase {
	/**
	 * @dataProvider provideStates
	 */
	public function testToIso( $country, $state, $expected ) {
		$this->assertSame( $expected, StateCode::to_iso( $country, $state ) );
	}

	public function provideStates() {
		return [
			'JP removes prefix'               => [ 'JP', 'JP14', '14' ],
			'JP keeps leading zero'           => [ 'JP', 'JP01', '01' ],
			'TH removes prefix'               => [ 'TH', 'TH-10', '10' ],
			'TH Bueng Kan unknown to API'     => [ 'TH', 'TH-38', '' ],
			'CN Beijing'                      => [ 'CN', 'CN2', 'BJ' ],
			'CN Jiangsu is not Beijing'       => [ 'CN', 'CN11', 'JS' ],
			'CN Xinjiang'                     => [ 'CN', 'CN32', 'XJ' ],
			'MX Jalisco'                      => [ 'MX', 'JA', 'JAL' ],
			'MX Ciudad de Mexico'             => [ 'MX', 'DF', 'CMX' ],
			'MX Queretaro'                    => [ 'MX', 'QT', 'QUE' ],
			'IN Telangana previous ISO code'  => [ 'IN', 'TS', 'TG' ],
			'IN Uttarakhand previous ISO'     => [ 'IN', 'UK', 'UT' ],
			'IN Odisha previous ISO code'     => [ 'IN', 'OD', 'OR' ],
			'IN Ladakh unknown to API'        => [ 'IN', 'LA', '' ],
			'IN Maharashtra unchanged'        => [ 'IN', 'MH', 'MH' ],
			'ID Kalimantan Utara unknown'     => [ 'ID', 'KU', '' ],
			'US unchanged'                    => [ 'US', 'CA', 'CA' ],
			'CA unchanged'                    => [ 'CA', 'ON', 'ON' ],
			'BR unchanged'                    => [ 'BR', 'SP', 'SP' ],
			'lowercase input'                 => [ 'jp', 'jp14', '14' ],
			'empty state'                     => [ 'JP', '', '' ],
			'unknown code passed through'     => [ 'CN', 'BJ', 'BJ' ],
		];
	}

	public function testMappingsCoverAllWooCommerceCodes() {
		$this->assertCount( 32, StateCode::CN );
		$this->assertCount( 32, StateCode::MX );
		$this->assertCount( 32, array_unique( StateCode::CN ) );
		$this->assertCount( 32, array_unique( StateCode::MX ) );
	}
}
