<?php

namespace Payone\Gateway;

abstract class GatewayBase extends \WC_Payment_Gateway {
	/**
	 * @var string
	 */
	protected $requestType;

	public function add( $methods ) {
		$methods[] = get_class( $this );

		return $methods;
	}

	public function init_common_form_fields( $label ) {
		$this->form_fields = [
			'enabled'      => [
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable payment method', 'payone' ),
				'default' => 'yes',
			],
			'request_type' => [
				'title'   => __( 'Request type', 'payone' ),
				'type'    => 'select',
				'options' => [
					'authorization'    => 'Authorization',
					'preauthorization' => 'Preauthorization',
				],
				'default' => 'authorization',
			],
			'title'        => [
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => $label,
				'desc_tip'    => true,
			],
			'description'  => [
				'title'   => __( 'Customer Message', 'woocommerce' ),
				'type'    => 'textarea',
				'default' => '',
			],
		];
	}
}