<?php

use Payone\Transaction\CreditCardHtp;
use PHPUnit\Framework\TestCase;

final class TransactionCreditCardHtpTest extends TestCase {
	/**
	 * @dataProvider provideInputModes
	 */
	public function testIsClickToPay( $input_mode, $expected ) {
		$this->assertSame( $expected, CreditCardHtp::is_click_to_pay( $input_mode ) );
	}

	public function provideInputModes() {
		return [
			'manual'         => [ 'manual', false ],
			'empty'          => [ '', false ],
			'click to pay'   => [ 'clickToPay', true ],
			'lowercase'      => [ 'clicktopay', true ],
			'register'       => [ 'register', true ],
			'unknown'        => [ 'something', false ],
		];
	}

	/**
	 * @dataProvider provideCardSchemes
	 */
	public function testToPayoneCardType( $scheme, $expected ) {
		$this->assertSame( $expected, CreditCardHtp::to_payone_card_type( $scheme ) );
	}

	public function provideCardSchemes() {
		return [
			[ 'VISA', 'V' ],
			[ 'MASTERCARD', 'M' ],
			[ 'mastercard', 'M' ],
			[ ' Amex ', 'A' ],
			[ 'DINERS', 'D' ],
			[ 'DISCOVER', 'C' ],
			[ 'JCB', 'J' ],
			[ 'MAESTRO', 'O' ],
			[ 'UNIONPAY', 'P' ],
			[ '', '' ],
			[ 'UNKNOWN', '' ],
		];
	}
}
