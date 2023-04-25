<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

class KlarnaInstallments extends KlarnaBase {

	const GATEWAY_ID = 'payone_klarna_installments';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->method_title       = 'PAYONE ' . __( 'Klarna Ratenkauf', 'payone-woocommerce-3' );
		$this->method_description = '';
		$this->hide_when_b2b      = true;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Klarna Ratenkauf', 'payone-woocommerce-3' ) );
		$this->form_fields['countries']['default'] = [ 'DE' ];
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';
		include_once PAYONE_VIEW_PATH . '/gateway/klarna/common.php';
		include PAYONE_VIEW_PATH . '/gateway/klarna/installments-payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\KlarnaAuthorizeInstallments::class );
	}

	public function get_financingtype() {
		return 'KIS';
	}
}
