<?php use Payone\Plugin;

include 'common.php'; ?>
<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<div id="amazonpay_express_error"></div>
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<script>
    function payone_amazonpay_express_get_address(which) {
        if (which === 'shipping' && ! jQuery('#ship-to-different-address-checkbox').prop('checked')) {
            which = 'billing';
        }
        return jQuery('#' + which + '_first_name').val() + jQuery('#' + which + '_last_name').val()
            + jQuery('#' + which + '_company').val() + jQuery('#' + which + '_address_1').val()
            + jQuery('#' + which + '_postcode').val() + jQuery('#' + which + '_city').val()
    }
    if (jQuery('input[name=payment_method]:checked').val() === '<?php echo \Payone\Gateway\AmazonPayExpress::GATEWAY_ID; ?>') {
        if (!jQuery('#ship-to-different-address-checkbox').prop('checked')) {
            jQuery('#ship-to-different-address-checkbox').trigger('click');
        }
        var payone_amazonpay_express_shipping_address_now = payone_amazonpay_express_get_address('shipping');

        jQuery('form.checkout.woocommerce-checkout')
            .on('change', function () {
                if (jQuery('input[name=payment_method]:checked').val() === '<?php echo \Payone\Gateway\AmazonPayExpress::GATEWAY_ID; ?>') {
                    var shipping_now = payone_amazonpay_express_get_address('shipping');
                    if (shipping_now !== payone_amazonpay_express_shipping_address_now) {
                        amazon.Pay.changeShippingAddress({amazonCheckoutSessionId: '<?php echo Plugin::get_session_value( \Payone\Gateway\AmazonPayExpress::SESSION_KEY_AMAZONPAY_SESSION_ID ); ?>'});
                    }

                    return true;
                }
            });
    }
</script>