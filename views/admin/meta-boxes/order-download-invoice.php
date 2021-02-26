<?php

/**
 * @var \Payone\Plugin $this
 * @var \WC_Order $order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="form-field form-field-wide wc-payone-invoice-download">
    <label for="payone_invoice_download"><?php _e( 'Invoice', 'payone-woocommerce-3' ); ?>:</label>
    <a href="/payone-callback?type=download-invoice&oid=<?php echo $order->get_id(); ?>">
		<?php _e( 'Download Invoice', 'payone-woocommerce-3' ); ?>
    </a>
</p>
