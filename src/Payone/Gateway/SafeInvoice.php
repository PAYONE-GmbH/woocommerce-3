<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Capture;

class SafeInvoice extends GatewayBase {
	const GATEWAY_ID = 'bs_payone_safeinvoice';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = '';
		$this->method_title       = 'Payone ' . __( 'Safe Invoice', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Safe Invoice', 'payone-woocommerce-3' ) );
        $this->form_fields[ 'countries' ][ 'default' ] = [ 'DE' ];
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/safe-invoice/payment-form.php';
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\SafeInvoice( $this );
		$response    = $transaction->execute( $order );

		if ( $response->has_error() ) {
			wc_add_notice( __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message(),
				'error' );

			return;
		}
		// @todo Bei Kauf auf Rechnung anderer Status und Order abschließen?

		$order->set_transaction_id( $response->get( 'txid' ) );
		if ( $transaction->get( 'request' ) === 'authorization' ) {
			$response->store_clearing_info( $order );
		}
		$this->add_email_meta_hook( [ $this, 'email_meta_action' ] );
		$order->update_meta_data( '_authorization_method', $transaction->get( 'request' ) );
		$order->update_status( 'on-hold', __( 'Invoice has been sent', 'payone-woocommerce-3' ) );

		wc_reduce_stock_levels( $order_id );
		$woocommerce->cart->empty_cart();

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
			$order->add_order_note(__( 'Payment received. Customer underpaid!', 'payone-woocommerce-3' ));
		} elseif ( $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment received.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );

		if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$capture = new Capture( $this );
			$response = $capture->execute( $order );
			if ( $response && $response->is_approved() ) {
				$response->store_clearing_info( $order );
				$order->save_meta_data();
			}

			return $response;
		}
	}

	protected function add_data_to_capture( Capture $capture, \WC_Order $order ) {
		\Payone\Transaction\SafeInvoice::add_article_list_to_transaction( $capture, $order );
	}
}