<?php

namespace Payone\Gateway;

class PayDirekt extends RedirectGatewayBase {
	const GATEWAY_ID = 'bs_payone_paydirekt';

	protected function human_readable_name() {
		return __( 'PayDirekt', 'payone-woocommerce-3' );
	}

	public function init_form_fields() {
		parent::init_form_fields();

        $this->form_fields[ 'countries' ][ 'default' ] = [ 'DE' ];
	}
}