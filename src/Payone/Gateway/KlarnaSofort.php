<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Plugin;

class KlarnaSofort extends KlarnaBase {

	const GATEWAY_ID = 'payone_klarna_sofort';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->method_title = 'PAYONE ' . __( 'Klarna Sofort bezahlen', 'payone-woocommerce-3' );
		$this->method_description = '';
	}

	public function init_form_fields() {
		$this->init_common_form_fields( __( 'Klarna Sofort bezahlen', 'payone-woocommerce-3' ) );
		$this->form_fields['countries']['default'] = [ 'DE' ];
	}

	public function payment_fields() {
		$options = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		include_once PAYONE_VIEW_PATH . '/gateway/klarna/common.php';
		include PAYONE_VIEW_PATH . '/gateway/klarna/sofort-payment-form.php';
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		return $this->process_redirect( $order_id, \Payone\Transaction\KlarnaAuthorizeSofort::class );
	}

	public function get_financingtype() {
		return 'KDD';
	}
}
