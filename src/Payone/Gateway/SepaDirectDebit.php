<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

class SepaDirectDebit extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_sepa';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'Payone ' . __( 'SEPA Direct Debit', 'payone-woocommerce-3' );
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
		$order->update_meta_data( '_mandate_identification', $transaction->get( 'mandate_identification' ) );
		$order->update_meta_data( '_mandate_identification_hash', uniqid( $order_id, true ) );
		$order->save_meta_data();
		$order->save();

		if ( $authorization_method === 'preauthorization' ) {
			$order->update_status( 'on-hold', __( 'Waiting for payment.', 'payone-woocommerce-3' ) );
		} elseif ( $authorization_method === 'authorization' ) {
			$order->update_status( 'processing',
				__( 'Payment is authorized and captured.', 'payone-woocommerce-3' ) );
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
		$result = [];

		if ( $data['confirmation_check'] === '0' ) {
			$result = [
				'status' => 'error',
				'message' => __( 'Please check this option', 'payone-woocommerce-3' ),
			];
		} elseif ( $data['confirmation_check'] === '1' ) {
			$result = [
				'status'    => 'active',
				'reference' => $data[ 'mandate_identification' ],
			];
		}

		if ( !$result ) {
			$transaction = new \Payone\Transaction\ManageMandate( $this, $data );
			$response    = $transaction->execute();

			if ( $data['confirmation_check'] === '0' ) {

			} elseif ( $data['confirmation_check'] === '1' ) {
				$result = [
					'status'  => 'error',
					'message' => __( 'Please check this option', 'payone-woocommerce-3' ),
				];
			} elseif ( $response->has_error() ) {
				// @todo Die Fehlermeldung ist irreführend, wenn keine IBAN angegeben wurde. Hier muss von uns schon geprüft werden
				$result = [
					'status'  => 'error',
					'message' => $response->get( 'customermessage' ),
				];
			} elseif ( $response->is_approved() && $response->get( 'mandate_status' ) === 'active' ) {
				$result = [
					'status'    => 'active',
					'reference' => $response->get( 'mandate_identification' ),
				];
			} elseif ( $response->is_approved() && $response->get( 'mandate_status' ) === 'pending' ) {
				$result = [
					'status'        => 'pending',
					'reference'     => $response->get( 'mandate_identification' ),
					'text'          => urldecode( $response->get( 'mandate_text' ) ),
					'error_message' => '',
				];
			}
		}

		$result['call'] = 'process_manage_mandate';
		$result['date'] = $data;

		echo json_encode($result);
		exit;
	}

	public function process_manage_mandate_getfile( $data ) {
		$mandate_identification_hash = isset ($data[ 'hash' ] ) ? $data[ 'hash' ] : '';
		$transaction = new \Payone\Transaction\GetFile( $this );
		$response = $transaction->execute( $mandate_identification_hash );

		header("Content-type:application/pdf");
		header("Content-Disposition:attachment;filename=SEPA-Lastschriftmandat.pdf");
		echo utf8_decode( $response->get( '_DATA' ) );
		exit;
	}

	/**
	 * @param \WC_Order $order
	 */
	public function add_content_to_thankyou_page( \WC_Order $order ) {
		$hash = $order->get_meta( '_mandate_identification_hash' );
		if ( $hash ) {
			$url = Plugin::get_callback_url( 'manage-mandate-getfile' );
			$url .= '&hash=' . $hash;

			echo '<p><a href="' . $url . '">Ihr SEPA-Mandat als PDF herunterladen</a>';
		}
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
		if ( $transaction_status->is_paid() || $transaction_status->is_capture() ) {
			if ( $order->get_status() === 'processing' ) {
				$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			} else {
				$order->update_status( 'wc-processing', __( 'Payment received.', 'payone-woocommerce-3' ) );
			}
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'preauthorization'
		     && $from_status === 'on-hold' && $to_status === 'processing'
		) {
			$this->capture( $order );
		}
	}
}
