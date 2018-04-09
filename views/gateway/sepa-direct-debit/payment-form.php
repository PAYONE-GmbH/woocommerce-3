<h1>SEPA-Lastschrift</h1>
<input type="hidden" id="direct_debit_reference" name="direct_debit_reference" value="">
<div id="direct_debit_wrapper">
    <p id="direct_debit_iban_field" class="form-row form-row-wide">
        <label for="direct_debit_iban"><?php echo __( 'IBAN', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="direct_debit_iban" id="direct_debit_iban">
    </p>
    <p id="direct_debit_bic_field" class="form-row form-row-wide">
        <label for="direct_debit_bic"><?php echo __( 'BIC', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="direct_debit_bic" id="direct_debit_bic">
    </p>
</div>
<div id="direct_debit_confirmation">
    <p>
        <?php echo __( 'direct.debit.mandate.headline', 'payone-woocommerce-3' ); ?>
    </p>
    <span id="direct_debit_confirmation_text"></span>
    <input id="direct_debit_confirmation_check" type="checkbox" name="direct_debit_confirmation_check" value="1">
    <label for="direct_debit_confirmation_check">
        <?php echo __( 'direct.debit.mandate.checkbox.label', 'payone-woocommerce-3' ); ?>
    </label>
</div>
<div id="direct_debit_error"></div>
<script type="text/javascript">
    var mandate_ok = false;
    jQuery('#direct_debit_confirmation').hide();
    function payone_checkout_clicked_<?php echo \Payone\Gateway\SepaDirectDebit::GATEWAY_ID; ?>() {
        if (mandate_ok) {
            return true;
        }

        var data = {
            lastname: jQuery('#billing_last_name').val(),
            country: jQuery('#billing_country').val(),
            city: jQuery('#billing_city').val(),
            iban: jQuery('#direct_debit_iban').val(),
            bic: jQuery('#direct_debit_bic').val(),
            currency: '<?php echo get_woocommerce_currency(); ?>',
            confirmation_check: jQuery('#direct_debit_confirmation_check').prop('checked') ? 1 : 0,
        };

        payone_manage_mandate(data);

        return false;
    }
    function payone_manage_mandate(data) {
        jQuery('#direct_debit_error').html('');
        jQuery.post('<?php echo \Payone\Plugin::get_callback_url('ajax-manage-mandate'); ?>', data, function(result) {
            result = jQuery.parseJSON(result);
            // result.status = 'active'; // @todo entfernen!
            if (result.status == 'error') {
                jQuery('#direct_debit_error').html('<strong style="color:red">' + result.message + '</strong>');
            } else if (result.status == 'active' ) {
                document.getElementById("direct_debit_reference").value = result['reference'];
                mandate_ok = true;
                payone_unblock();
                jQuery('#place_order').parents('form').submit();
            } else if (result.status == 'pending') {
                jQuery('#direct_debit_confirmation_text').html(result['text']);
                jQuery('#direct_debit_confirmation_check').prop('checked', false);
                jQuery('#direct_debit_wrapper').hide();
                jQuery('#direct_debit_confirmation').show();
            }
            payone_unblock();
        });
    }
</script>