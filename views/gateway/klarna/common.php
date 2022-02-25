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
        pay_now: {
            widget_shown: false,
            result_start_session: null,
            finished: false,
        }
    };

    function payone_checkout_clicked_klarna_generic( payment_category ) {
        var data = {
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
            shipping_firstname: jQuery('#shipping_first_name').val(),
            shipping_lastname: jQuery('#shipping_last_name').val(),
            shipping_company: jQuery('#shipping_company').val(),
            shipping_street: jQuery('#shipping_address_1').val(),
            shipping_addressaddition: jQuery('#shipping_address_2').val(),
            shipping_zip: jQuery('#shipping_postcode').val(),
            shipping_city: jQuery('#shipping_city').val(),
            shipping_country: jQuery('#shipping_country').val(),
            'shipping_telephonenumber': jQuery('#billing_phone').val(),
            'shipping_email': jQuery('#billing_email').val(),
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
                    jQuery('#klarna_' + payment_category + '_error').html('<strong style="color:red">' + klarna_vars[payment_category].result_start_session.message + '</strong>');
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
