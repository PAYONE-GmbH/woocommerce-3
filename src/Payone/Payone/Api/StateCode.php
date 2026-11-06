<?php

namespace Payone\Payone\Api;

/**
 * Converts WooCommerce state codes into the ISO 3166-2 subdivision codes expected by the
 * PAYONE API for "state" and "shipping_state".
 *
 * WooCommerce does not use ISO 3166-2 for every country (see woocommerce/i18n/states.php):
 * Japan and Thailand carry a country prefix, China is numbered consecutively and Mexico uses
 * two letter codes. Every mapping below has been verified against the PAYONE API.
 *
 * @see https://docs.payone.com/information/platform-codes
 */
class StateCode {
	/**
	 * WooCommerce CN1..CN32 => ISO 3166-2:CN
	 */
	const CN = [
		'CN1'  => 'YN', // Yunnan
		'CN2'  => 'BJ', // Beijing
		'CN3'  => 'TJ', // Tianjin
		'CN4'  => 'HE', // Hebei
		'CN5'  => 'SX', // Shanxi
		'CN6'  => 'NM', // Inner Mongolia
		'CN7'  => 'LN', // Liaoning
		'CN8'  => 'JL', // Jilin
		'CN9'  => 'HL', // Heilongjiang
		'CN10' => 'SH', // Shanghai
		'CN11' => 'JS', // Jiangsu
		'CN12' => 'ZJ', // Zhejiang
		'CN13' => 'AH', // Anhui
		'CN14' => 'FJ', // Fujian
		'CN15' => 'JX', // Jiangxi
		'CN16' => 'SD', // Shandong
		'CN17' => 'HA', // Henan
		'CN18' => 'HB', // Hubei
		'CN19' => 'HN', // Hunan
		'CN20' => 'GD', // Guangdong
		'CN21' => 'GX', // Guangxi Zhuang
		'CN22' => 'HI', // Hainan
		'CN23' => 'CQ', // Chongqing
		'CN24' => 'SC', // Sichuan
		'CN25' => 'GZ', // Guizhou
		'CN26' => 'SN', // Shaanxi
		'CN27' => 'GS', // Gansu
		'CN28' => 'QH', // Qinghai
		'CN29' => 'NX', // Ningxia Hui
		'CN30' => 'MO', // Macao
		'CN31' => 'XZ', // Tibet
		'CN32' => 'XJ', // Xinjiang
	];

	/**
	 * WooCommerce two letter codes => ISO 3166-2:MX
	 */
	const MX = [
		'AG' => 'AGU', // Aguascalientes
		'BC' => 'BCN', // Baja California
		'BS' => 'BCS', // Baja California Sur
		'CM' => 'CAM', // Campeche
		'CS' => 'CHP', // Chiapas
		'CH' => 'CHH', // Chihuahua
		'DF' => 'CMX', // Ciudad de México
		'CO' => 'COA', // Coahuila
		'CL' => 'COL', // Colima
		'DG' => 'DUR', // Durango
		'MX' => 'MEX', // Estado de México
		'GT' => 'GUA', // Guanajuato
		'GR' => 'GRO', // Guerrero
		'HG' => 'HID', // Hidalgo
		'JA' => 'JAL', // Jalisco
		'MI' => 'MIC', // Michoacán
		'MO' => 'MOR', // Morelos
		'NA' => 'NAY', // Nayarit
		'NL' => 'NLE', // Nuevo León
		'OA' => 'OAX', // Oaxaca
		'PU' => 'PUE', // Puebla
		'QT' => 'QUE', // Querétaro
		'QR' => 'ROO', // Quintana Roo
		'SL' => 'SLP', // San Luis Potosí
		'SI' => 'SIN', // Sinaloa
		'SO' => 'SON', // Sonora
		'TB' => 'TAB', // Tabasco
		'TM' => 'TAM', // Tamaulipas
		'TL' => 'TLA', // Tlaxcala
		'VE' => 'VER', // Veracruz
		'YU' => 'YUC', // Yucatán
		'ZA' => 'ZAC', // Zacatecas
	];

	/**
	 * Current ISO codes that the PAYONE API still validates against their previous value.
	 */
	const PREVIOUS_ISO_CODE = [
		'IN' => [
			'TS' => 'TG', // Telangana
			'UK' => 'UT', // Uttarakhand
			'OD' => 'OR', // Odisha
		],
	];

	/**
	 * Valid ISO codes the PAYONE API does not know yet. These are not transmitted at all,
	 * the API accepts requests without state.
	 */
	const UNKNOWN_TO_API = [
		'IN' => [ 'DH', 'LA' ], // Dadra and Nagar Haveli and Daman and Diu, Ladakh
		'ID' => [ 'KU' ],       // Kalimantan Utara
		'TH' => [ '38' ],       // Bueng Kan
	];

	/**
	 * @param string $country ISO 3166 country code
	 * @param string $state   State code as stored by WooCommerce
	 *
	 * @return string ISO 3166-2 subdivision code without country prefix, '' if none should be sent
	 */
	public static function to_iso( $country, $state ) {
		$country = strtoupper( trim( (string) $country ) );
		$state   = strtoupper( trim( (string) $state ) );

		if ( $state === '' ) {
			return '';
		}

		// Local copies, because dereferencing class constants inside isset() is not supported by PHP 5.6
		$cn       = self::CN;
		$mx       = self::MX;
		$previous = self::PREVIOUS_ISO_CODE;
		$unknown  = self::UNKNOWN_TO_API;

		switch ( $country ) {
			case 'JP': // JP01..JP47
			case 'TH': // TH-10 etc.
				if ( preg_match( '/^' . $country . '-?(\d{2})$/', $state, $matches ) ) {
					$state = $matches[1];
				}
				break;
			case 'CN':
				if ( isset( $cn[ $state ] ) ) {
					$state = $cn[ $state ];
				}
				break;
			case 'MX':
				if ( isset( $mx[ $state ] ) ) {
					$state = $mx[ $state ];
				}
				break;
		}

		if ( isset( $previous[ $country ][ $state ] ) ) {
			$state = $previous[ $country ][ $state ];
		}

		if ( isset( $unknown[ $country ] ) && in_array( $state, $unknown[ $country ], true ) ) {
			return '';
		}

		return $state;
	}
}
