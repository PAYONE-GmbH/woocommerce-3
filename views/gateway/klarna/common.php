<input type="hidden" id="klarna_authorization_token" name="klarna_authorization_token" value="">
<input type="hidden" id="klarna_workorderid" name="klarna_workorderid" value="">
<input type="hidden" id="klarna_shipping_email" name="klarna_shipping_email" value="">
<input type="hidden" id="klarna_shipping_telephonenumber" name="klarna_shipping_telephonenumber" value="">

<script src="https://x.klarnacdn.net/kp/lib/v1/api.js" async></script>
<script type="application/javascript">
    var klarna_vars = {
        pay_later: {
            widget_shown: false,
            result_start_session: null,
            finished: false,
        },
        pay_over_time: {
            widget_shown: false,
            result_start_session: null,
            finished: false,
        },
        direct_debit: {
            widget_shown: false,
            result_start_session: null,
            finished: false,
        }
    };
    var payone_klarna_actively_chosen = false;

    function payone_checkout_clicked_klarna_generic( payment_category ) {
        var data = {
            category: payment_category,
            currency: '<?php echo get_woocommerce_currency(); ?>',
            country: jQuery('#billing_country').val(),
            firstname: jQuery('#billing_first_name').val(),
            lastname: jQuery('#billing_last_name').val(),
            company: jQuery('#billing_company').val(),
            street: jQuery('#billing_address_1').val(),
            zip: jQuery('#billing_postcode').val(),
            city: jQuery('#billing_city').val(),
            addressaddition: jQuery('#billing_address_2').val(),
            email: jQuery('#billing_email').val(),
            telephonenumber: jQuery('#billing_phone').val(),
            'shipping_telephonenumber': jQuery('#billing_phone').val(),
            'shipping_email': jQuery('#billing_email').val(),
        }
        if (jQuery('#ship-to-different-address-checkbox').prop('checked') === true) {
            data.shipping_firstname = jQuery('#shipping_first_name').val();
            data.shipping_lastname = jQuery('#shipping_last_name').val();
            data.shipping_company = jQuery('#shipping_company').val();
            data.shipping_street = jQuery('#shipping_address_1').val();
            data.shipping_addressaddition = jQuery('#shipping_address_2').val();
            data.shipping_zip = jQuery('#shipping_postcode').val();
            data.shipping_city = jQuery('#shipping_city').val();
            data.shipping_country = jQuery('#shipping_country').val();
        } else {
            data.shipping_firstname = data.firstname;
            data.shipping_lastname = data.lastname;
            data.shipping_company = data.company;
            data.shipping_street = data.street;
            data.shipping_addressaddition = data.addressaddition;
            data.shipping_zip = data.zip;
            data.shipping_city = data.city;
            data.shipping_country = data.country;
        }

        if (klarna_vars[payment_category].widget_shown === false) {
            jQuery.post('<?php echo \Payone\Plugin::get_callback_url(['type' => 'ajax-klarna-start-session']); ?>', data, function (result) {
                klarna_vars[payment_category].result_start_session = jQuery.parseJSON(result);
                if (klarna_vars[payment_category].result_start_session.status === 'ok') {
                    document.getElementById("klarna_workorderid").value = klarna_vars[payment_category].result_start_session.workorderid;
                    Klarna.Payments.init({
                        client_token: klarna_vars[payment_category].result_start_session.client_token
                    });
                    Klarna.Payments.load({
                        container: '#klarna_' + payment_category + '_container',
                        payment_method_category: payment_category
                    }, function (klarnaResult) {
                        payone_unblock();
                        if (klarnaResult.show_form === false) {
                            jQuery('#klarna_' + payment_category + '_error').html('<strong style="color:red">PAYONE Klarna Rechnung kann nicht genutzt werden!</strong>');
                        } else {
                            klarna_vars[payment_category].widget_shown = true;
                        }
                    });
                } else {
                    jQuery('#klarna_'  + payment_category + '_error').html('<strong style="color:red">' + klarna_vars[payment_category].result_start_session.message + '</strong>');
                    payone_unblock();
                }
            });
        } else if (klarna_vars[payment_category].widget_shown === true && klarna_vars[payment_category].finished === false) {
            Klarna.Payments.authorize( { payment_method_category: payment_category }, klarna_vars[payment_category].result_start_session.data, function(klarnaResult) {
                payone_unblock();
                if (klarnaResult.approved === false && klarnaResult.show_form === false) {
                    jQuery('#klarna_' + payment_category + '_error').html('<strong style="color:red">PAYONE Klarna Rechnung kann nicht genutzt werden!</strong>');
                } else if (klarnaResult.approved === false) {
                    jQuery('#klarna_' + payment_category + '_error').html('<strong style="color:red">Der Vorgang wurde abgebrochen</strong>');
                } else if (klarnaResult.approved) {
                    document.getElementById("klarna_authorization_token").value = klarnaResult.authorization_token;
                    document.getElementById("klarna_shipping_email").value = jQuery('#billing_email').val();
                    document.getElementById("klarna_shipping_telephonenumber").value = jQuery('#billing_phone').val();
                    klarna_vars[payment_category].finished = true;
                    jQuery('#place_order').parents('form').submit();
                }
            } );
        }

        return klarna_vars[payment_category].finished;
    }
</script>
