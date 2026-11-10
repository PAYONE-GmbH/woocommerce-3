<?php

namespace Payone\Transaction;

/**
 * Credit card payment with a token from the PAYONE Hosted Tokenization SDK.
 *
 * Depending on how the customer entered the card the token is used differently:
 * - manual:              classic credit card payment, token is a pseudo card PAN (clearingtype=cc)
 * - clickToPay/register: Click to Pay wallet payment (clearingtype=wlt, wallettype=CTP)
 */
class CreditCardHtp extends CreditCard {
	const INPUT_MODE_MANUAL = 'manual';

	/**
	 * Card schemes as returned by the SDK mapped to PAYONE card types.
	 */
	const CARD_TYPES = [
		'VISA'       => 'V',
		'MASTERCARD' => 'M',
		'AMEX'       => 'A',
		'DINERS'     => 'D',
		'DISCOVER'   => 'C',
		'JCB'        => 'J',
		'MAESTRO'    => 'O',
		'UNIONPAY'   => 'P',
	];

	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
	 */
	public function __construct( $gateway ) {
		parent::__construct( $gateway );

		$input_mode = isset( $_POST['card_input_mode'] ) ? (string) $_POST['card_input_mode'] : self::INPUT_MODE_MANUAL;

		if ( self::is_click_to_pay( $input_mode ) ) {
			$token = $this->get( 'pseudocardpan' );
			$this->remove( 'pseudocardpan' );
			$this->remove( 'cardholder' );
			$this->set( 'clearingtype', 'wlt' );
			$this->set( 'wallettype', 'CTP' );
			$this->set( 'add_paydata[paymentcheckout_data]', $token );

			$card_type = self::to_payone_card_type( isset( $_POST['card_type'] ) ? (string) $_POST['card_type'] : '' );
			if ( $card_type ) {
				$this->set( 'cardtype', $card_type );
			}
		}
	}

	/**
	 * @param string $input_mode
	 *
	 * @return bool
	 */
	public static function is_click_to_pay( $input_mode ) {
		return in_array( strtolower( $input_mode ), [ 'clicktopay', 'register' ], true );
	}

	/**
	 * @param string $scheme e.g. "VISA" or "MASTERCARD"
	 *
	 * @return string PAYONE card type or empty string
	 */
	public static function to_payone_card_type( $scheme ) {
		$card_types = self::CARD_TYPES;
		$scheme     = strtoupper( trim( $scheme ) );

		return isset( $card_types[ $scheme ] ) ? $card_types[ $scheme ] : '';
	}
}
