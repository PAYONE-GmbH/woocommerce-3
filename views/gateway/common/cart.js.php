<?php
    $paypal_express_button_image = PAYONE_PLUGIN_URL . 'assets/' . __( 'checkout-paypal-en.png', 'payone-woocommerce-3' );
    $paypal_express_button_image = apply_filters( 'payone_paypal_express_button_image_url', $paypal_express_button_image );
?>
<script type="text/javascript">
jQuery('#payone-paypal-express-button').html('<button style="text-align:center;width:100%" title="PayPal Express"><img style="margin:auto; text-align:left;" src="<?php echo $paypal_express_button_image; ?>" alt="PayPal Express"></button>');
jQuery('#payone-paypal-express-button').on('click', function(event) {
    jQuery( '.cart_totals' ).block({
        message: null,
        overlayCSS: {
            background: '#fff',
            opacity: 0.6
        }
    });

    event.preventDefault();

    jQuery.post('<?php echo \Payone\Plugin::get_callback_url(['type' => 'ajax-paypal-express-set-checkout']); ?>', function (result) {
        var json = jQuery.parseJSON(result);
        if (typeof json.status !== 'undefined' && json.status === 'ok') {
            window.location.href = json.url;
        }
    });

    return false;
});
</script>
