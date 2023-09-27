<script type = "text/javascript" >
    function payone_block() {
        jQuery('.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }
function payone_unblock() {
    jQuery('.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table').unblock();
}
function payone_klarna_gateway_id_to_category(gateway_id) {
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
function payone_listen_to_company_change() {
    jQuery('#billing_company,#shipping_company').change(function () {
        jQuery('form.checkout').trigger('update_checkout');
    });
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
function payone_secured_installment_was_chosen() {
    return jQuery('input[name=payment_method]:checked').val() == '<?php echo \Payone\Gateway\SecuredInstallment::GATEWAY_ID; ?>';
}
jQuery('form.woocommerce-checkout').change(function (event) {
    jQuery('input[name="payone_ship_to_different_address_checkbox"]').val(jQuery('#ship-to-different-address-checkbox').prop('checked') ? 1 : 0);
    if (payone_secured_installment_was_chosen() && typeof payone_secured_installment_options_setup === 'function') {
        payone_secured_installment_options_setup();
    }
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
<?php
$gateway_to_select = \Payone\Plugin::get_session_value( \Payone\Gateway\GatewayBase::SESSION_KEY_SELECT_GATEWAY );
if ($gateway_to_select) {
    \Payone\Plugin::delete_session_value( \Payone\Gateway\GatewayBase::SESSION_KEY_SELECT_GATEWAY );
} else {
    $gateway_to_select = '';
}
?>
var select_gateway_after_redirect = '<?php echo $gateway_to_select; ?>';
var payone_klarna_actively_chosen = <?php echo \Payone\Plugin::get_session_value( \Payone\Gateway\KlarnaBase::SESSION_KEY_SESSION_STARTED ) ? 'true' : 'false'; ?>;
    jQuery(document).ready(function () {
    var payone_payment_methods_initialized = false;
    payone_listen_to_company_change();
    if (select_gateway_after_redirect) {
        var current_gateway = jQuery('input[name=payment_method]:checked').val();
        if (current_gateway !== select_gateway_after_redirect) {
            jQuery('#payment_method_' + select_gateway_after_redirect).click();
        }
    }
    jQuery('body').on('updated_checkout', function (event) {
        if (payone_payment_methods_initialized && payone_klarna_actively_chosen) {
            reload_current_klarna_widget();
        }
    });
    jQuery('body').on('payment_method_selected', function (event) {
        var current_gateway = jQuery('input[name=payment_method]:checked').val();
        var is_klarna_gateway = current_gateway === '<?php echo \Payone\Gateway\KlarnaInvoice::GATEWAY_ID; ?>'
            || current_gateway === '<?php echo \Payone\Gateway\KlarnaInstallments::GATEWAY_ID; ?>'
            || current_gateway === '<?php echo \Payone\Gateway\KlarnaSofort::GATEWAY_ID; ?>';
        if (payone_payment_methods_initialized === false) {
            payone_payment_methods_initialized = true;
        } else {
            if (is_klarna_gateway) {
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
        if (jQuery('input#terms').length === 0 || jQuery('input#terms').is(':checked')) {
            var current_gateway = jQuery('input[name=payment_method]:checked').val();
            var result = true;
            switch ( current_gateway ) {
                case '<?php echo \Payone\Gateway\Giropay::GATEWAY_ID; ?>':
                    payone_block();
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\Giropay::GATEWAY_ID; ?>();
                    break;
                case '<?php echo \Payone\Gateway\RatepayDirectDebit::GATEWAY_ID; ?>':
                    payone_block();
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\RatepayDirectDebit::GATEWAY_ID; ?>();
                    break;
                case '<?php echo \Payone\Gateway\RatepayInstallments::GATEWAY_ID; ?>':
                    payone_block();
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\RatepayInstallments::GATEWAY_ID; ?>();
                    break;
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
                case '<?php echo \Payone\Gateway\SecuredInstallment::GATEWAY_ID; ?>':
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\SecuredInstallment::GATEWAY_ID; ?>();
                    break;
                case '<?php echo \Payone\Gateway\SecuredInvoice::GATEWAY_ID; ?>':
                    result = payone_checkout_clicked_<?php echo \Payone\Gateway\SecuredInvoice::GATEWAY_ID; ?>();
                    break;
                default:
                    payone_unblock();
            }
        }
        return result;
    });
});
function payone_valid_iban(value) {
    value = value.replace(/\s+/g, '').toUpperCase();
    const codeLengths = {
        AD: 24, AE: 23, AL: 28, AT: 20, AZ: 28, BA: 20, BE: 16, BG: 22, BH: 22, BR: 29, CH: 21, CR: 21, CY: 28, CZ: 24,
        DE: 22, DK: 18, DO: 28, EE: 20, ES: 24, LC: 30, FI: 18, FO: 18, FR: 27, GB: 22, GI: 23, GL: 18, GR: 27, GT: 28,
        HR: 21, HU: 28, IE: 22, IL: 23, IS: 26, IT: 27, JO: 30, KW: 30, KZ: 20, LB: 28, LI: 21, LT: 20, LU: 20, LV: 21,
        MC: 27, MD: 24, ME: 22, MK: 19, MR: 27, MT: 31, MU: 30, NL: 18, NO: 15, PK: 24, PL: 28, PS: 29, PT: 25, QA: 29,
        RO: 24, RS: 22, SA: 24, SE: 24, SI: 19, SK: 24, SM: 27, TN: 24, TR: 26
    };
    const code = value.match(/^([A-Z]{2})(\d{2})([A-Z\d]+)$/);
    if (!code || value.length !== codeLengths[code[1]]) {
        return false;
    }
    let rearrange =
        value.substring(4, value.length)
        + value.substring(0, 4);
    let alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split('');
    let alphaMap = {};
    let number = [];
    alphabet.forEach((value, index) => {
        alphaMap[value] = index + 10;
    });
    rearrange.split('').forEach((value, index) => {
        number[index] = alphaMap[value] || value;
    });
    return payone_modulo(number.join('').toString(), 97) === 1;
}
function payone_modulo(aNumStr, aDiv) {
    var tmp = "";
    var i, r;
    for (i = 0; i < aNumStr.length; i++) {
        tmp += aNumStr.charAt(i);
        r = tmp % aDiv;
        tmp = r.toString();
    }
    return tmp / 1;
}
</script>
