<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;

class Eps extends RedirectGatewayBase {
    const GATEWAY_ID = 'bs_payone_eps';

    public function __construct() {
        parent::__construct(self::GATEWAY_ID);

        $this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-eps.png';
        $this->method_title       = 'PAYONE ' . __( 'eps', 'payone-woocommerce-3' );
        $this->method_description = '';
    }

    public function init_form_fields() {
        $this->init_common_form_fields( 'PAYONE ' . __( 'eps', 'payone-woocommerce-3' ) );
        $this->form_fields[ 'countries' ][ 'default' ] = [ 'AT' ];
    }

    public function payment_fields() {
        $bankgroups = [
            'ARZ_OAB' => 'Apothekerbank',
            'ARZ_BAF' => 'Ärztebank',
            'BA_AUS' => 'Bank Austria',
            'ARZ_BCS' => 'Bankhaus Carl Spängler & Co.AG',
            'EPS_SCHEL' => 'Bankhaus Schelhammer & Schattera AG',
            'BAWAG_PSK' => 'BAWAG P.S.K. AG',
            'BAWAG_ESY' => 'Easybank AG',
            'SPARDAT_EBS' => 'Erste Bank und Sparkassen',
            'ARZ_HAA' => 'Hypo Alpe-Adria-Bank International AG',
            'ARZ_VLH' => 'Hypo Landesbank Vorarlberg',
            'HRAC_OOS' => 'HYPO Oberösterreich,Salzburg,Steiermark',
            'ARZ_HTB' => 'Hypo Tirol Bank AG',
            'EPS_OBAG' => 'Oberbank AG',
            'RAC_RAC' => 'Raiffeisen Bankengruppe Österreich',
            'EPS_SCHOELLER' => 'Schoellerbank AG',
            'ARZ_OVB' => 'Volksbank Gruppe',
            'EPS_VRBB' => 'VR-Bank Braunau',
            'EPS_AAB' => 'Austrian Anadi Bank AG',
            'EPS_BKS' => 'BKS Bank AG',
            'EPS_BKB' => 'Brüll Kallmus Bank AG',
            'EPS_VLB' => 'BTV VIER LÄNDER BANK',
            'EPS_CBGG' => 'Capital Bank Grawe Gruppe AG',
            'EPS_DB' => 'Dolomitenbank',
            'EPS_NOELB' => 'HYPO NOE Landesbank AG',
            'EPS_HBL' => 'HYPO-BANK BURGENLAND Aktiengesellschaft',
            'EPS_MFB' => 'Marchfelder Bank',
            'EPS_SPDBW' => 'Sparda Bank Wien',
            'EPS_SPDBA' => 'SPARDA-BANK AUSTRIA',
            'EPS_VKB' => 'Volkskreditbank AG',
        ];

        include PAYONE_VIEW_PATH . '/gateway/eps/payment-form.php';
    }

    /**
     * @param int $order_id
     *
     * @return array
     * @throws \WC_Data_Exception
     */
    public function process_payment( $order_id ) {
        return $this->process_redirect( $order_id, \Payone\Transaction\Eps::class );
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
        if ( $from_status === 'on-hold' && $to_status === 'processing' ) {
            $this->capture( $order );
        }
    }
}
