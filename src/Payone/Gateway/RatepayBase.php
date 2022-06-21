<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;
use Payone\Transaction\Capture;
use Payone\Transaction\Debit;

abstract class RatepayBase extends RedirectGatewayBase {

    const TRANSIENT_KEY_SESSION_STARTED = 'payone_sssion_started';

    public function __construct( $id ) {
		parent::__construct( $id );

        $this->icon = PAYONE_PLUGIN_URL . 'assets/icon-ratepay.svg';
        $this->hide_when_no_shipping = true;
    }

    public function determine_shop_id( \WC_Order $order ) {
        return '88880103'; // @todo
    }

    protected function add_data_to_capture( Capture $capture, \WC_Order $order ) {
        $capture->set( 'add_paydata[shop_id]', $this->determine_shop_id( $order ) );
        $capture->add_article_list_to_transaction( $order );
        $capture->set( 'capturemode', 'completed' );
    }

    protected function add_data_to_debit( Debit $debit, \WC_Order $order ) {
        $debit->set( 'add_paydata[shop_id]', $this->determine_shop_id( $order ) );
    }

    public function process_start_session( $data ) {
        $transaction = new \Payone\Transaction\KlarnaStartSession( $this );

        $transaction
            ->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'success' ] ) )
            ->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'error' ] ) )
            ->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'back' ] ) );

        $transaction->set( 'add_paydata[shipping_telephonenumber]', $data['shipping_telephonenumber'] );
        unset( $data['shipping_telephonenumber'] );
        $transaction->set( 'add_paydata[shipping_email]', $data['shipping_email'] );
        unset( $data['shipping_email'] );

        foreach ( $data as $field => $value ) {
            $transaction->set( $field, $value );
        }

        $response = $transaction->execute( WC()->cart );

        if ($response->get('status') === 'OK') {
            $result = [
                'status' => 'ok',
                'client_token' => $response->get( 'add_paydata[client_token]' ),
                'workorderid' => $response->get( 'workorderid' ),
                'data_for_authorization' => $transaction->get_data_for_authorization( WC()->cart ),
            ];

        } else {
            $result = [
                'status' => 'error',
                'code' => $response->get( 'errorcode' ),
                'message' => $response->get( 'customermessage' ),
            ];
        }

        echo json_encode($result);
        exit;
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
		$authorization_method = $order->get_meta( '_authorization_method' );
		if ( $authorization_method === 'authorization' && $transaction_status->is_paid() ) {
			$order->add_order_note( __( 'Payment is authorized by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} elseif ( $authorization_method === 'preauthorization' && $transaction_status->is_capture() ) {
			$order->add_order_note( __( 'Payment is captured by PAYONE, payment is complete.', 'payone-woocommerce-3' ) );
			$order->payment_complete();
		} else {
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone-woocommerce-3' ) );
		}
	}

	public function order_status_changed( \WC_Order $order, $from_status, $to_status ) {
		$authorization_method = $order->get_meta( '_authorization_method' );

        if ( $authorization_method === 'preauthorization' && $to_status === 'processing' ) {
			$this->capture( $order );
		}
	}

    protected function add_shop_ids_field() {
        $this->form_fields[ 'shop_ids[]'] = [
            'title'   => __( 'Shop-IDs', 'payone-woocommerce-3' ),
            'type'    => 'shop_ids',
            'default' => '',
            'sanitize_callback' => [ $this, 'sanitize_shop_ids_field' ],
        ];
    }

    public function generate_shop_ids_html( $key, $data ) {
        $option = $this->get_option( $key );
        if ( $option ) {
            $shop_ids = explode( ',', $option );
        } else {
            $shop_ids = [];
        }

        $shop_ids_data = (array) $this->get_option( 'shop_ids_data', [] );

        $out = '<table class="table">';
        $out .= '<tr><th>' . __( 'Shop-ID', 'payone-woocommerce-3' )
            . '</th><th>' . __( 'Currency', 'payone-woocommerce-3' )
            . '</th><th>' . __( 'Invoice country', 'payone-woocommerce-3' )
            . '</th><th>' . __( 'Shipping country', 'payone-woocommerce-3' )
            . '</th><th>' . __( 'Min. basket', 'payone-woocommerce-3' )
            . '</th><th>' . __( 'Max. basket', 'payone-woocommerce-3' )
            . '</th></tr>';
        foreach ( $shop_ids as $shop_id ) {
            $shop_id_data = isset( $shop_ids_data[$shop_id] ) ? $shop_ids_data[$shop_id] : [];
            $shop_id_currency = isset( $shop_id_data['currency'] ) ? $shop_id_data['currency'] : '-';
            $shop_id_country_code_billing = isset( $shop_id_data['country_code_billing'] ) ? $shop_id_data['country_code_billing'] : '-';
            $shop_id_country_code_delivery = isset( $shop_id_data['country_code_delivery'] ) ? $shop_id_data['country_code_delivery'] : '-';
            $shop_id_invoice_sum_min = isset( $shop_id_data['invoice_sum_min'] ) ? $shop_id_data['invoice_sum_min'] : '-';
            $shop_id_invoice_sum_max = isset( $shop_id_data['invoice_sum_max'] ) ? $shop_id_data['invoice_sum_max'] : '-';
            $out .= '<tr><td><input type="text" name="' . $key . '" value="' . esc_attr( $shop_id ) . '"></td>';
            $out .= '<td>' . $shop_id_currency
                 . '</td><td>' . $shop_id_country_code_billing
                 . '</td><td>' . $shop_id_country_code_delivery
                 . '</td><td>' . number_format_i18n( $shop_id_invoice_sum_min )
                 . '</td><td>' . number_format_i18n( $shop_id_invoice_sum_max )
                 . '</td></tr>';
        }
        $out .= '<tr><td><input type="text" name="' . $key . '" value="" placeholder="' . __( 'Add Shop-ID', 'payone-woocommerce-3' ) .'"></td>';
        $out .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
        $out .= '</table>';

        return $out;
    }

    public function sanitize_shop_ids_field( $input ) {
        $values = isset( $_POST['shop_ids'] ) ? $_POST['shop_ids'] : [];

        $shop_ids = [];
        foreach ( $values as $value ) {
            $value = trim( $value );
            if ( $value ) {
                $shop_ids[] = $value;

                $transaction = new \Payone\Transaction\RatepayProfile( $this );
                $response = $transaction->execute( $value );

                if ( $response->get('status') === 'OK' ) {
                    $shop_id_data = [
                        'currency' => $response->get( 'add_paydata[currency]' ),
                        'country_code_billing' => $response->get( 'add_paydata[country-code-billing]' ),
                        'country_code_delivery' => $response->get( 'add_paydata[country-code-delivery]' ),
                        'invoice_sum_min' => $response->get( 'add_paydata[tx-limit-invoice-min]' ),
                        'invoice_sum_max' => $response->get( 'add_paydata[tx-limit-invoice-max]' ),
                    ];

                    $shop_ids_data = (array) $this->get_option( 'shop_ids_data', [] );
                    $shop_ids_data[$value] = $shop_id_data;
                    $this->update_option( 'shop_ids_data', $shop_ids_data );
                }
            }
        }

        return implode( ',', $shop_ids );
    }

    /**
     * "YYYY-MM-DD" -> "YYYYMMDD"
     *
     * @param string $birthday
     * @return string
     */
    public static function convert_birthday($birthday ) {
        return implode( '', explode( '-', $birthday ) );
    }
}
