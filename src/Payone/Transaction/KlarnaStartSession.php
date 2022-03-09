<?php

namespace Payone\Transaction;

use Payone\Plugin;

class KlarnaStartSession extends Base {
	/**
	 * @param \Payone\Gateway\GatewayBase $gateway
     * @param string $authorization_method
	 */
	public function __construct( $gateway, $authorization_method = null ) {
		parent::__construct( 'genericpayment' );
		$this->set_data_from_gateway( $gateway );

        $this->set( 'clearingtype', 'fnc' );
        $this->set( 'financingtype', $gateway->get_financingtype() );
        $this->set( 'add_paydata[action]', 'start_session' );
	}

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return \Payone\Payone\Api\Response
	 */
	public function execute( \WC_Cart $cart ) {
		$this->add_article_list_to_transaction( $cart );

		$this->set_once( 'amount', $cart->get_total( 'non-view' ) * 100 );

		return $this->submit();
	}

    public function get_data_for_authorization( \WC_Cart $cart ) {
        $tax_totals = $cart->get_tax_totals();
        $tax_item = array_shift( $tax_totals ); // @todo
        $tax_amount = $tax_item->amount;

        $data = [
            'purchase_country' => $this->get( 'country' ),
            'purchase_currency' => $this->get( 'currency' ),
            'billing_address' => [
                'given_name' => $this->get( 'firstname' ),
                'family_name' => $this->get( 'lastname' ),
                'email' => $this->get( 'email' ),
                'street_address' => $this->get( 'street' ),
                'street_address2' => $this->get( 'addressaddition' ),
                'postal_code' => $this->get( 'zip' ),
                'city' => $this->get( 'city' ),
                'phone' => $this->get( 'telephonenumber' ),
                'country' => $this->get( 'country' ),
            ],
            'shipping_address' => [
                'given_name' => $this->get( 'shipping_firstname' ),
                'family_name' => $this->get( 'shipping_lastname' ),
                'email' => $this->get( 'add_paydata[shipping_email]' ),
                'street_address' => $this->get( 'shipping_street' ),
                'street_address2' => $this->get( 'shipping_addressaddition' ),
                'postal_code' => $this->get( 'shipping_zip' ),
                'city' => $this->get( 'shipping_city' ),
                'phone' => $this->get( 'add_paydata[shipping_telephonenumber]' ),
                'country' => $this->get( 'shipping_country' ),
            ],
            'order_amount' => $this->get( 'amount' ),
            'order_tax_amount' =>  $tax_amount * 100,
            'order_lines' => [],
        ];

        $i = 1;
        while ( $this->get( "id[{$i}]" ) ) {
            $data['order_lines'][] = [
                'reference' => $this->get( "id[{$i}]" ),
                'name' => $this->get( "de[{$i}]" ),
                'quantity' => $this->get( "no[{$i}]" ),
                'unit_price' => round( $this->get( "pr[{$i}]" ) / $this->get( "no[{$i}]" ) ),
                'total_amount' => $this->get( "pr[{$i}]" ),
            ];
            $i++;
        }

        return $data;
    }
}
