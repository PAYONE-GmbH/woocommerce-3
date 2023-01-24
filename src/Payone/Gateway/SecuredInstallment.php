<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class SecuredInstallment extends GatewayBase {

	const GATEWAY_ID = 'payone_secured_installment';
	const PAYLA_PARTNER_ID = 'e7yeryF2of8X';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-rechnungskauf.png'; // @todo
		$this->method_title       = 'PAYONE ' . __( 'Secured Installment', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Secured Installment', 'payone-woocommerce-3' ) );
	}

	public function payment_fields() {
		$environment = $this->get_mode() === 'live' ? 'p' : 't';
		$snippet_token = self::PAYLA_PARTNER_ID . $this->get_merchant_id() . md5(uniqid('payone_secured_installment', true));

		include PAYONE_VIEW_PATH . '/gateway/payla/secured-installment-payment-form.php';
	}

	public function process_secured_installment_options() {
		$transaction = new \Payone\Transaction\SecuredInstallmentOptions( $this );
		$response = $transaction->execute( WC()->cart );
		if ( $response->get( 'status' ) === 'OK' ) {
			$result = [];
			$i = 0;
			while ( $response->get('add_paydata[installment_option_id_'. $i . ']' ) ) {
				$monthly_amount = round( $response->get('add_paydata[monthly_amount_value_'. $i . ']' ) / 100, 2 );
				$result[] = [
					'workorderid' => $response->get('workorderid'),
					'option_id' => $response->get('add_paydata[installment_option_id_'. $i . ']' ),
					'number_of_payments' => $response->get('add_paydata[number_of_payments_'. $i . ']' ),
					'monthly_amount' => number_format_i18n( $monthly_amount, 2 ) . ' €',
					'nominal_interest_rate' => number_format_i18n( $response->get('add_paydata[nominal_interest_rate_'. $i . ']' ) / 100, 2 ) . '&nbsp;%',
					'effective_interest_rate' => number_format_i18n( $response->get('add_paydata[effective_interest_rate_'. $i . ']' ) / 100, 2 ) . '&nbsp;%',
					'total_amount_value' => number_format_i18n( $response->get('add_paydata[total_amount_value_'. $i . ']' ) / 100, 2 ) . '&nbsp;€',
					'info_url' => $response->get('add_paydata[link_credit_information_href_'. $i . ']' ),
				];
				$i++;
			}
			/*
			$result = [
				'annual_percentage_rate' => number_format_i18n( $response->get( 'add_paydata[annual-percentage-rate]' ), 2 ),
				'interest_amount'        => number_format_i18n( $response->get( 'add_paydata[interest-amount]' ), 2 ),
				'amount'                 => number_format_i18n( $response->get( 'add_paydata[amount]' ), 2 ),
				'number_of_rates'        => $response->get( 'add_paydata[number-of-rates]' ),
				'rate'                   => number_format_i18n( $response->get( 'add_paydata[rate]' ), 2 ),
				'payment_firstday'       => $response->get( 'add_paydata[payment-firstday]' ),
				'interest_rate'          => number_format_i18n( $response->get( 'add_paydata[interest-rate]' ), 2 ),
				'monthly_debit_interest' => number_format_i18n( $response->get( 'add_paydata[monthly-debit-interest]' ), 2 ),
				'last_rate'              => number_format_i18n( $response->get( 'add_paydata[last-rate]' ), 2 ),
				'service_charge'         => number_format_i18n( $response->get( 'add_paydata[service-charge]' ), 2 ),
				'total_amount'           => number_format_i18n( $response->get( 'add_paydata[total-amount]' ), 2 ),
				'form'                   => [
					'installment_amount'      => $response->get( 'add_paydata[rate]' ),
					'installment_number'      => $response->get( 'add_paydata[number-of-rates]' ),
					'last_installment_amount' => $response->get( 'add_paydata[last-rate]' ),
					'interest_rate'           => 100 * $response->get( 'add_paydata[interest-rate]' ),
					'amount'                  => $response->get( 'add_paydata[total-amount]' ),
				],
			];
			*/

			echo json_encode( $result );
			exit;
		}

		return null;
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\SecuredInstallment( $this );

		$response = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(),
				'error' );

			return;
		}
		// @todo Bei Kauf auf Rechnung anderer Status und Order abschließen?

		$order->set_transaction_id( $response->get( 'txid' ) );
		$order->add_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
		$response->store_clearing_info( $order );
		$this->add_email_meta_hook( [ $this, 'email_meta_action' ] );
		$order->update_meta_data( '_authorization_method', $transaction->get( 'request' ) );
		$order->update_status( 'on-hold', __( 'Invoice has been sent', 'payone-woocommerce-3' ) );

		// Reduce stock levels
		wc_reduce_stock_levels( $order_id );

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		if ( $transaction_status->no_further_action_necessary() ) {
			return;
		}

		$order = $transaction_status->get_order();

		if ( $transaction_status->is_overpaid() ) {
			$order->add_order_note( __( 'Payment received. Customer overpaid!', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $transaction_status->is_underpaid() ) {
			$order->add_order_note( __( 'Payment received. Customer underpaid!', 'payone-woocommerce-3' ) );
		} elseif ( $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );

		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}
}