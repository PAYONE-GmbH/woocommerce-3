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
    function payone_klarna_category_was_chosen() {
        var current_gateway = jQuery('input[name=payment_method]:checked').val();

        return payone_klarna_gateway_id_to_category(current_gateway) !== '';
    }
    function reload_current_klarna_widget() {
        var current_gateway = jQuery('input[name=payment_method]:checked').val();
        var current_category = payone_klarna_gateway_id_to_category(current_gateway);
        if (current_category) {
            klarna_vars.pay_later.widget_shown = false;
            klarna_vars.pay_over_time.widget_shown = false;
            klarna_vars.direct_debit.widget_shown = false;
            jQuery('#klarna_pay_later_container').empty();
            jQuery('#klarna_pay_later_error').empty();
            jQuery('#klarna_pay_over_time_container').empty();
            jQuery('#klarna_pay_over_time_error').empty();
            jQuery('#klarna_direct_debit_container').empty();
            jQuery('#klarna_direct_debit_error').empty();
            payone_checkout_clicked_klarna_generic(current_category);
        }
    }

    jQuery('form.woocommerce-checkout').change(function (event) {
        if (payone_klarna_actively_chosen && payone_klarna_category_was_chosen() && klarna_data != {}) {
            var values_changed = false;
            if (klarna_data.firstname != jQuery('#billing_first_name').val()
                || klarna_data.lastname != jQuery('#billing_last_name').val()
                || klarna_data.company != jQuery('#billing_company').val()
                || klarna_data.telephonenumber != jQuery('#billing_phone').val()
                || klarna_data.email != jQuery('#billing_email').val()
                || klarna_data.shipping_telephonenumber != jQuery('#billing_phone').val()
                || klarna_data.shipping_email != jQuery('#billing_email').val()
            ) {
                values_changed = true;
            }
            if (!values_changed && jQuery('#ship-to-different-address-checkbox').prop('checked') === true) {
                if (klarna_data.shipping_firstname != jQuery('#shipping_first_name').val()
                    || klarna_data.shipping_lastname != jQuery('#shipping_last_name').val()
                    || klarna_data.shipping_company != jQuery('#shipping_company').val()
                ) {
                    values_changed = true;
                }
            }

            if (values_changed) {
                reload_current_klarna_widget();
            }
        }
    });

    var payone_klarna_actively_chosen = false;

    jQuery(document).ready(function() {
        var payone_payment_methods_initialized = false;

        jQuery('body').on('updated_checkout', function (event) {
            if (payone_payment_methods_initialized && payone_klarna_actively_chosen) {
                reload_current_klarna_widget();
            }
        });

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
                klarna_vars.pay_later.widget_shown = false;
                klarna_vars.pay_over_time.widget_shown = false;
                klarna_vars.direct_debit.widget_shown = false;
                jQuery('#klarna_pay_later_container').empty();
                jQuery('#klarna_pay_later_error').empty();
                jQuery('#klarna_pay_over_time_container').empty();
                jQuery('#klarna_pay_over_time_error').empty();
                jQuery('#klarna_direct_debit_container').empty();
                jQuery('#klarna_direct_debit_error').empty();
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
