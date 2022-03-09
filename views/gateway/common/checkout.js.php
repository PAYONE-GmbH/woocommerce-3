<script type="text/javascript">
    function payone_block() {
        jQuery( '.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }
    function payone_unblock() {
        jQuery( '.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table' ).unblock();
    }
    jQuery(document).ready(function() {
        jQuery('form.woocommerce-checkout').on('checkout_place_order', function (event) {
            var current_gateway = jQuery('input[name=payment_method]:checked').val();

            var result = true;
            switch (current_gateway) {
                case '<?php echo \Payone\Gateway\SepaDirectDebit::GATEWAY_ID; ?>':
                    payone_block();
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\SepaDirectDebit::GATEWAY_ID; ?>();
                    break;
                case '<?php echo \Payone\Gateway\KlarnaInvoice::GATEWAY_ID; ?>':
                    payone_block();
                    result = payone_checkout_clicked_klarna_generic( 'pay_later' );
                    break;
                case '<?php echo \Payone\Gateway\KlarnaInstallments::GATEWAY_ID; ?>':
                    payone_block();
                    result = payone_checkout_clicked_klarna_generic( 'pay_over_time' );
                    break;
                case '<?php echo \Payone\Gateway\KlarnaSofort::GATEWAY_ID; ?>':
                    payone_block();
                    result = payone_checkout_clicked_klarna_generic( 'direct_debit' );
                    break;
                default:
                    payone_unblock();
            }

            return result;
        });
    });
</script>
