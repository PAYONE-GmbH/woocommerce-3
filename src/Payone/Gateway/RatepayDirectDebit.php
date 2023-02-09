<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

class RatepayDirectDebit extends RatepayBase {

	const GATEWAY_ID = 'payone_ratepay_direct_debit';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->method_title       = 'PAYONE ' . __( 'Ratepay Direct Debit', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Ratepay Direct Debit', 'payone-woocommerce-3' ) );
		$this->add_allow_different_shipping_address_field();
		$this->add_device_fingerprint_field();
		$this->add_shop_ids_field();

		$this->form_fields['authorization_method']['default'] = 'preauthorization';
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include PAYONE_VIEW_PATH . '/gateway/ratepay/direct-debit-payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\RatepayDirectDebit::class );
	}

	public function get_financingtype() {
		return 'RPD';
	}
}
