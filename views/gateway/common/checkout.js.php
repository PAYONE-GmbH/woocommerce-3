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
    function payone_klarna_gateway_id_to_category( gateway_id ) {
        switch (gateway_id) {
            case '<?php echo \Payone\Gateway\KlarnaInvoice::GATEWAY_ID; ?>':
                return 'pay_later';
            case '<?php echo \Payone\Gateway\KlarnaInstallments::GATEWAY_ID; ?>':
                return 'pay_over_time';
            case '<?php echo \Payone\Gateway\KlarnaSofort::GATEWAY_ID; ?>':
                return 'direct_debit';
        }

        return '';
    }

    jQuery(document).ready(function() {
        var payone_payment_methods_initialized = false;

        jQuery('body').on('payment_method_selected', function (event) {
            var current_gateway = jQuery('input[name=payment_method]:checked').val();

            if (payone_payment_methods_initialized === false) {
                payone_payment_methods_initialized = true;
            } else {
                if (current_gateway === '<?php echo \Payone\Gateway\KlarnaInvoice::GATEWAY_ID; ?>'
                    || current_gateway === '<?php echo \Payone\Gateway\KlarnaInstallments::GATEWAY_ID; ?>'
                    || current_gateway === '<?php echo \Payone\Gateway\KlarnaSofort::GATEWAY_ID; ?>'
                ) {
                    payone_klarna_actively_chosen = true;
                }
            }

            if (payone_klarna_actively_chosen) {
                var current_category = payone_klarna_gateway_id_to_category(current_gateway);
                switch (current_category) {
                    case 'pay_later':
                        klarna_vars.pay_later.widget_shown = false;
                        jQuery('#klarna_pay_over_time_container').empty();
                        jQuery('#klarna_pay_over_time_error').empty();
                        jQuery('#klarna_direct_debit_container').empty();
                        jQuery('#klarna_direct_debit_error').empty();
                        break;
                    case 'pay_over_time':
                        klarna_vars.pay_over_time.widget_shown = false;
                        jQuery('#klarna_pay_later_container').empty();
                        jQuery('#klarna_pay_later_error').empty();
                        jQuery('#klarna_direct_debit_container').empty();
                        jQuery('#klarna_direct_debit_error').empty();
                        break;
                    case 'direct_debit':
                        klarna_vars.direct_debit.widget_shown = false;
                        jQuery('#klarna_pay_later_container').empty();
                        jQuery('#klarna_pay_later_error').empty();
                        jQuery('#klarna_pay_over_time_container').empty();
                        jQuery('#klarna_pay_over_time_error').empty();
                        break;
                }
                payone_checkout_clicked_klarna_generic(current_category);
            }
        });

        jQuery('form.woocommerce-checkout').on('checkout_place_order', function (event) {
            if ( jQuery('input#terms').is(':checked') ) {
                var current_gateway = jQuery('input[name=payment_method]:checked').val();

                var result = true;
                switch (current_gateway) {
                    case '<?php echo \Payone\Gateway\SepaDirectDebit::GATEWAY_ID; ?>':
                        payone_block();
                        result = payone_checkout_clicked_<?php echo \Payone\Gateway\SepaDirectDebit::GATEWAY_ID; ?>();
                        break;
                    case '<?php echo \Payone\Gateway\KlarnaInvoice::GATEWAY_ID; ?>':
                    case '<?php echo \Payone\Gateway\KlarnaInstallments::GATEWAY_ID; ?>':
                    case '<?php echo \Payone\Gateway\KlarnaSofort::GATEWAY_ID; ?>':
                        payone_block();
                        result = payone_checkout_clicked_klarna_generic(payone_klarna_gateway_id_to_category(current_gateway));
                        break;
                    default:
                        payone_unblock();
                }
            }

            return result;
        });
    });
</script>
