<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class SepaDirectDebit extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_sepa';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'Payone Lastschrift (SEPA)';
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'SEPA Direct Debit', 'payone-woocommerce-3' ) );
		$this->form_fields['sepa_check_bank_data'] = [
			'title'   => __( 'Check bank data', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => [
				'basic'    => __( 'Basic', 'payone-woocommerce-3' ),
				'blacklist' => __( 'Check POS black list', 'payone-woocommerce-3' ),
				'none' => __( 'None (only possible if PAYONE Mandate Management is inactive)', 'payone-woocommerce-3' ),
			],
			'default' => 'basic',
		];
		$this->form_fields['sepa_use_mandate_management'] = [
			'title'   => __( 'Use PAYONE Mandate Management', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => [
				'0' => __( 'No', 'payone-woocommerce-3' ),
				'1' => __( 'Yes', 'payone-woocommerce-3' ),
			],
			'default' => '1',
		];
		$this->form_fields['sepa_pdf_download_mandate'] = [
			'title'   => __( 'Download mandate as PDF', 'payone-woocommerce-3' ),
			'type'    => 'select',
			'options' => [
				'0' => __( 'No', 'payone-woocommerce-3' ),
				'1' => __( 'Yes', 'payone-woocommerce-3' ),
			],
			'default' => '1',
		];
		$this->form_fields['sepa_countries'] = [
			'title'   => __( 'List of supported bank countries', 'payone-woocommerce-3' ),
			'type'    => 'multiselect',
			'options' => [
				'AT' => __( 'Austria', 'payone-woocommerce-3' ),
				'BE' => __( 'Belgium', 'payone-woocommerce-3' ),
				'BG' => __( 'Bulgaria', 'payone-woocommerce-3' ),
				'HR' => __( 'Croatia', 'payone-woocommerce-3' ),
				'CY' => __( 'Cyprus', 'payone-woocommerce-3' ),
				'CZ' => __( 'Czech Republic', 'payone-woocommerce-3' ),
				'DK' => __( 'Denmark', 'payone-woocommerce-3' ),
				'EE' => __( 'Estonia', 'payone-woocommerce-3' ),
				'FI' => __( 'Finland', 'payone-woocommerce-3' ),
				'FR' => __( 'France', 'payone-woocommerce-3' ),
				'GF' => __( 'French Guiana', 'payone-woocommerce-3' ),
				'DE' => __( 'Germany', 'payone-woocommerce-3' ),
				'GI' => __( 'Gibraltar', 'payone-woocommerce-3' ),
				'GR' => __( 'Greece', 'payone-woocommerce-3' ),
				'GP' => __( 'Guadeloupe', 'payone-woocommerce-3' ),
				'HU' => __( 'Hungary', 'payone-woocommerce-3' ),
				'IS' => __( 'Iceland', 'payone-woocommerce-3' ),
				'IE' => __( 'Ireland', 'payone-woocommerce-3' ),
				'IT' => __( 'Italy', 'payone-woocommerce-3' ),
				'LV' => __( 'Latvia', 'payone-woocommerce-3' ),
				'LI' => __( 'Liechtenstein', 'payone-woocommerce-3' ),
				'LT' => __( 'Lithuania', 'payone-woocommerce-3' ),
				'LU' => __( 'Luxembourg', 'payone-woocommerce-3' ),
				'MT' => __( 'Malta', 'payone-woocommerce-3' ),
				'MQ' => __( 'Martinique', 'payone-woocommerce-3' ),
				'YT' => __( 'Mayotte', 'payone-woocommerce-3' ),
				'MC' => __( 'Monaco', 'payone-woocommerce-3' ),
				'NL' => __( 'Netherlands', 'payone-woocommerce-3' ),
				'NO' => __( 'Norway', 'payone-woocommerce-3' ),
				'PL' => __( 'Poland', 'payone-woocommerce-3' ),
				'PT' => __( 'Portugal', 'payone-woocommerce-3' ),
				'RE' => __( 'Réunion', 'payone-woocommerce-3' ),
				'RO' => __( 'Romania', 'payone-woocommerce-3' ),
				'BL' => __( 'Saint Barthélemy', 'payone-woocommerce-3' ),
				'MF' => __( 'Saint Martin (French part)', 'payone-woocommerce-3' ),
				'PM' => __( 'Saint Pierre and Miquelon', 'payone-woocommerce-3' ),
				'SK' => __( 'Slovakia', 'payone-woocommerce-3' ),
				'SI' => __( 'Slovenia', 'payone-woocommerce-3' ),
				'ES' => __( 'Spain', 'payone-woocommerce-3' ),
				'SE' => __( 'Sweden', 'payone-woocommerce-3' ),
				'CH' => __( 'Switzerland', 'payone-woocommerce-3' ),
				'GB' => __( 'United Kingdom', 'payone-woocommerce-3' ),
			],
			'default' => [ 'DE', 'AT', 'CH' ],
		];
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/sepa-direct-debit/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\SepaDirectDebit( $this );
		$response    = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(), 'error' );
			return;
		}

		$order->set_transaction_id( $response->get( 'txid' ) );

		$authorization_method = $transaction->get( 'request' );
		$order->update_meta_data( '_authorization_method', $authorization_method );
		$order->save_meta_data();
		$order->save();

		if ( $authorization_method === 'preauthorization' ) {
			$order->update_status( 'on-hold', __( 'Waiting for payment.', 'woocommerce' ) );
		} elseif ( $authorization_method === 'authorization' ) {
			$order->update_status( 'processing',
				__( 'Payment is authorized and captured.', 'woocommerce' ) );
		}

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

	public function process_manage_mandate( $data ) {
		$transaction = new \Payone\Transaction\ManageMandate( $this, $data );
		$response = $transaction->execute();

		$result = [];
		if ( $response->has_error() ) {
			// @todo Die Fehlermeldung ist irreführend, wenn keine IBAN angegeben wurde. Hier muss von uns schon geprüft werden
			$result = [
				'status' => 'error',
				'message' => $response->get( 'customermessage' ),
			];
		} elseif ( $response->is_approved() && $response->get( 'mandate_status' ) === 'active' ) {
			$result = [
				'status' => 'active',
				'reference' => $response->get( 'mandate_identification' ),
			];
		} elseif ( $response->is_approved() && $response->get( 'mandate_status' ) === 'pending' ) {
			$result = [
				'status' => 'pending',
				'reference' => $response->get( 'mandate_identification' ),
				'text' => urldecode( $response->get( 'mandate_text' ) ),
			];
		}

		$result['call'] = 'process_manage_mandate';
		$result['date'] = $data;

		echo json_encode($result);
		exit;
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		parent::process_transaction_status( $transaction_status );

		$order = $transaction_status->get_order();
		if ( $transaction_status->is_paid() || $transaction_status->is_capture() ) {
			if ( $order->get_status() === 'processing' ) {
				$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			} else {
				$order->update_status( 'wc-processing', __( 'Payment received.', 'payone-woocommerce-3' ) );
			}
		} elseif ( $transaction_status->is_cancelation() ) {
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone-woocommerce-3' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'preauthorization'
		     && $from_status === 'on-hold' && $to_status === 'processing'
		) {
			// @todo Reagieren, wenn Capture fehlschlägt?
			$this->capture( $order );
		}
	}
}
