<?php

use PHPUnit\Framework\TestCase;


class WC_Tax
{
    public static function get_tax_classes() {
        return [
            0 => 'Ermäßigter Steuersatz',
            1 => 'Steuerfrei',
        ];
    }

    public static function get_rates_for_tax_class( $tax_class ) {
        switch ( $tax_class ) {
            case 'Ermäßigter Steuersatz':
                return (object) [
                    1 => (object) [
                        'tax_rate_id' => 2,
                        'tax_rate_country' => 'DE',
                        'tax_rate_state' => '',
                        'tax_rate' => 7.0000,
                        'tax_rate_name' => 'Mehrwertsteuer',
                        'tax_rate_priority' => 1,
                        'tax_rate_compound' => 0,
                        'tax_rate_shipping' => 1,
                        'tax_rate_order' => 0,
                        'tax_rate_class' => 'ermaessigter-steuersatz',
                        'postcode_count' => 0,
                        'city_count' => 0,
                    ]
                ];
            case 'Steuerfrei':
                return (object) [
                    3 => (object) [
                        'tax_rate_id' => 3,
                        'tax_rate_country' => 'DE',
                        'tax_rate_state' => '',
                        'tax_rate' => 0.0000,
                        'tax_rate_name' => 'Mehrwertsteuer',
                        'tax_rate_priority' => 1,
                        'tax_rate_compound' => 0,
                        'tax_rate_shipping' => 1,
                        'tax_rate_order' => 0,
                        'tax_rate_class' => 'steuerfrei',
                        'postcode_count' => 0,
                        'city_count' => 0,
                    ]
                ];
        }

        return (object) [
            1 => (object) [
                'tax_rate_id' => 1,
                'tax_rate_country' => 'DE',
                'tax_rate_state' => '',
                'tax_rate' => 19.0000,
                'tax_rate_name' => 'Mehrwertsteuer',
                'tax_rate_priority' => 1,
                'tax_rate_compound' => 0,
                'tax_rate_shipping' => 1,
                'tax_rate_order' => 0,
                'tax_rate_class' => '',
                'postcode_count' => 0,
                'city_count' => 0,
            ],
            4 => (object) [
                'tax_rate_id' => 4,
                'tax_rate_country' => 'SE',
                'tax_rate_state' => '',
                'tax_rate' => 19.0000,
                'tax_rate_name' => 'Mehrwertsteuer',
                'tax_rate_priority' => 1,
                'tax_rate_compound' => 0,
                'tax_rate_shipping' => 1,
                'tax_rate_order' => 0,
                'tax_rate_class' => '',
                'postcode_count' => 0,
                'city_count' => 0,
            ]
        ];
    }
}

final class PluginTest extends TestCase {
	public function test_payone_ip_ranges() {
	    // IPv4
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '213.178.72.196', '213.178.72.196' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '213.178.72.197', '213.178.72.197' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '217.70.200.3', '217.70.200.0/24' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '185.60.20.128', '185.60.20.0/24' ) );
		$this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '192.168.0.1', '213.178.72.196' ) );
		$this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '192.168.0.1', '185.60.20.0/24' ) );
		$this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '185.60.21.1', '185.60.20.0/24' ) );
		$this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '192.168.65.178', '192.168.65.178' ) );

		// IPv6
        $this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '::ffff:213.178.72.196', '213.178.72.196' ) );
        $this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '::ffff:213.178.72.197', '213.178.72.197' ) );
        $this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '::ffff:217.70.200.3', '217.70.200.0/24' ) );
        $this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '::ffff:185.60.20.128', '185.60.20.0/24' ) );
        $this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '::ffff:192.168.0.1', '213.178.72.196' ) );
        $this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '::ffff:192.168.0.1', '185.60.20.0/24' ) );
        $this->assertFalse( \Payone\Plugin::ip_address_is_in_range( '::ffff:185.60.21.1', '185.60.20.0/24' ) );
        $this->assertTrue( \Payone\Plugin::ip_address_is_in_range( '::ffff:192.168.65.178', '192.168.65.178' ) );
    }

	public function test_get_tax_rate_for_item_19() {
	    $item = [
	        'id' => 778,
            'order_id' => 1000137,
            'name' => 'Trampolin',
            'product_id' => 8,
            'variation_id' => 0,
            'quantity' => 1,
            'tax_class' => '',
            'subtotal' => 49,
            'subtotal_tax' => 9.31,
            'total' => 49,
            'total_tax' => 9.31,
            'taxes' => [
                'total' => [
                    1 => 9.31,
                    2 => '',
                ],
                'subtotal' => [
                    1 => 9.31,
                    2 => '',
                ]
            ],
            'meta_data' => [],
        ];

	    $this->assertEquals( \Payone\Plugin::get_tax_rate_for_item_data( $item ), 19.00 );
    }

    public function test_get_tax_rate_for_item_7() {
        $item = [
            'id' => 781,
            'order_id' => 1000137,
            'name' => 'Trampolin',
            'product_id' => 1000120,
            'variation_id' => 0,
            'quantity' => 1,
            'tax_class' => 'ermaessigter-steuersatz',
            'subtotal' => 20,
            'subtotal_tax' => 1.4,
            'total' => 20,
            'total_tax' => 1.4,
            'taxes' => [
                'total' => [
                    1 => '',
                    2 => 1.4,
                ],
                'subtotal' => [
                    1 => '',
                    2 => 1.4,
                ]
            ],
            'meta_data' => [],
        ];

        $this->assertEquals( \Payone\Plugin::get_tax_rate_for_item_data( $item ), 7.00 );
    }

    public function test_get_tax_rate_for_item_7_multiple() {
        $item = [
            'id' => 783,
            'order_id' => 1000138,
            'name' => 'Trampolin',
            'product_id' => 1000120,
            'variation_id' => 0,
            'quantity' => 2,
            'tax_class' => 'ermaessigter-steuersatz',
            'subtotal' => 40,
            'subtotal_tax' => 2.8,
            'total' => 32,
            'total_tax' => 2.24,
            'taxes' => [
                'total' => [
                    1 => 2.24,
                    2 => '',
                ],
                'subtotal' => [
                    1 => 2.8,
                    2 => '',
                ]
            ],
            'meta_data' => [],
        ];

        $this->assertEquals( \Payone\Plugin::get_tax_rate_for_item_data( $item ), 7.00 );
    }

    public function test_get_tax_rate_for_item_19_rounding_error() {
        $item = [
            'id' => 778,
            'order_id' => 1000137,
            'name' => 'Trampolin',
            'product_id' => 8,
            'variation_id' => 0,
            'quantity' => 1,
            'tax_class' => '',
            'subtotal' => 49,
            'subtotal_tax' => 9.31,
            'total' => 49,
            'total_tax' => 9.32, // originally 9.31
            'taxes' => [
                'total' => [
                    1 => 9.31,
                    2 => '',
                ],
                'subtotal' => [
                    1 => 9.31,
                    2 => '',
                ]
            ],
            'meta_data' => [],
        ];

        $this->assertEquals( \Payone\Plugin::get_tax_rate_for_item_data( $item ), 19.00 );
    }

    public function test_sanitize_reference() {
	    $this->assertEquals( '12.34-567_abc-DE/8', \Payone\Plugin::sanitize_reference( '12.34-567_abc-DE/8' ) );
        $this->assertEquals( '1', \Payone\Plugin::sanitize_reference( '#1' ) );
        $this->assertEquals( '1', \Payone\Plugin::sanitize_reference( '$1' ) );
        $this->assertEquals( '8', \Payone\Plugin::sanitize_reference( '(8)' ) );
        $this->assertEquals( '1-234-5', \Payone\Plugin::sanitize_reference( '1-(234)-5' ) );
        $this->assertEquals( '1-234-5', \Payone\Plugin::sanitize_reference( '1(234)5' ) );
    }
}
