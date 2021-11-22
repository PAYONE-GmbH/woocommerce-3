<?php

/**
 * @var \Payone\Plugin $this
 * @var \WC_Order $order
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="woocommerce-order-payone-invoice">
    <h2 class="woocommerce-order-payone-invoice__title">
        <?php esc_html_e( 'Invoice', 'payone-woocommerce-3' ); ?>
    </h2>
    <table class="woocommerce-table woocommerce-table--order-payone-invoice">
        <tr class="woocommerce-table__row">
            <td class="woocommerce-table__cell">
                <a href="<?php echo $this->get_callback_url( ['type' => 'download-invoice', 'oid' => $order->get_id()] ); ?>">
                    <?php esc_html_e( 'Download Invoice', 'payone-woocommerce-3' ); ?>
                </a>
            </td>
        </tr>
    </table>
</section>
