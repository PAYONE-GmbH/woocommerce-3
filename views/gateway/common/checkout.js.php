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

            payone_block();

            /**
             * @todo Ich habe es nicht geschafft, hier eine abstrakte Lösung zu finden. Ein Versuch mit
             * window['checkout_clicked_'+current_gateway]() ruft zwar die Methode auf, der Rückgabewert
             * wird aber ignoriert.
             */
            var result = true;
            switch (current_gateway) {
                case '<?php echo \Payone\Gateway\SepaDirectDebit::GATEWAY_ID; ?>':
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\SepaDirectDebit::GATEWAY_ID; ?>();
                    break;
                case '<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>':
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>();
                    break;
                default:
                    payone_unblock();
            }

            return result;
        });
    });
</script>