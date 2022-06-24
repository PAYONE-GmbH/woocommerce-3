<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

class RatepayInstallments extends RatepayBase {

    const GATEWAY_ID = 'payone_ratepay_installments';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->method_title       = 'PAYONE ' . __( 'Ratepay Installments', 'payone-woocommerce-3' );;
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Ratepay Installments', 'payone-woocommerce-3' ) );
        $this->add_shop_ids_field();
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

        $shop_ids_data = (array) $this->get_option( 'shop_ids_data', [] );
        $shop_id = $this->determine_shop_id( WC()->cart );
        $shop_id_data = isset( $shop_ids_data[$shop_id] ) ? $shop_ids_data[$shop_id] : [];
        $installment_months = isset( $shop_id_data['month_allowed'] ) ? explode( ',', $shop_id_data['month_allowed'] ) : [];

        $currency = $shop_id_data['currency'];
        if ( $currency === 'EUR' ) {
            $currency = '€';
        }

        include PAYONE_VIEW_PATH . '/gateway/ratepay/installments-payment-form.php';
	}

    /**
     * @param int $order_id
     *
     * @return array
     * @throws \WC_Data_Exception
     */
    public function process_payment( $order_id ) {
        return $this->process_redirect( $order_id, \Payone\Transaction\RatepayInstallments::class );
    }

    public function process_calculate( $data )
    {
        $transaction = new \Payone\Transaction\RatepayCalculate( $this );

        $calculation_type = isset( $data['calculation-type'] ) ? $data['calculation-type'] : '';
        $transaction->set( 'add_paydata[calculation_type]', $calculation_type );
        if ( $calculation_type === 'calculation-by-time') {
            $month = isset( $data['month'] ) ? $data['month'] : '';
            $transaction->set( 'add_paydata[month]', $month );
        } else {
            $rate = isset( $data['rate'] ) ? $data['rate'] : '';
            $transaction->set( 'add_paydata[rate]', $rate );
        }

        $response = $transaction->execute( $this->determine_shop_id( WC()->cart ) );
        if ( $response->get('status') === 'OK' ) {
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
                'form' => [
                    'installment_amount'      => $response->get( 'add_paydata[rate]' ),
                    'installment_number'      => $response->get( 'add_paydata[number-of-rates]' ),
                    'last_installment_amount' => $response->get( 'add_paydata[last-rate]' ),
                    'interest_rate'           => 100 * $response->get( 'add_paydata[interest-rate]' ),
                    'amount'                  => $response->get( 'add_paydata[total-amount]' ),
                ],
            ];

            echo json_encode( $result );
            exit;
        }

        return null;
    }

    public function get_financingtype() {
        return 'RPS';
    }
}
