<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

class RatepayOpenInvoice extends RatepayBase {

    const GATEWAY_ID = 'payone_ratepay_open_invoice';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->method_title       = 'PAYONE ' . __( 'Ratepay Open Invoice', 'payone-woocommerce-3' );;
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Ratepay Open Invoice', 'payone-woocommerce-3' ) );
        $this->add_shop_ids_field();
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

        include PAYONE_VIEW_PATH . '/gateway/ratepay/open-invoice-payment-form.php';
	}

    /**
     * @param int $order_id
     *
     * @return array
     * @throws \WC_Data_Exception
     */
    public function process_payment( $order_id ) {
        return $this->process_redirect( $order_id, \Payone\Transaction\RatepayOpenInvoice::class );
    }

    public function get_financingtype() {
        return 'RPV';
    }
}
