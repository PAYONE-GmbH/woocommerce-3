<?php

namespace Payone\WooCommerceSubscription;

trait WCSHandler {
    public static function is_wcs_active() {
        return (bool) in_array(
            'woocommerce-subscriptions/woocommerce-subscriptions.php',
            apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
            true
        );
    }

    public static function is_subscription( \WC_Order $order ) {
        return self::is_wcs_active() && wcs_order_contains_subscription( $order );
    }
}
