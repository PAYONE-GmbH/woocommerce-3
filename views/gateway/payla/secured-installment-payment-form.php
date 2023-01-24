<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<script id="paylaDcs" type="text/javascript" src="https://d.payla.io/dcs/<?php echo esc_attr(self::PAYLA_PARTNER_ID); ?>/<?php echo esc_attr($this->get_merchant_id()); ?>/dcs.js"></script>
<script>
    var paylaDcsT = paylaDcs.init("<?php echo $environment; ?>", "<?php echo $snippet_token; ?>");
    var payone_secured_installment_options_initialized = false;
    function payone_secured_installment_options_setup() {
        if ( payone_secured_installment_options_initialized ) {
            return;
        }
        payone_secured_installment_options_initialized = true;
        payone_block();
        jQuery.post('<?php echo \Payone\Plugin::get_callback_url( [ 'type' => 'ajax-secured-installment-options' ] ); ?>', function ( result ) {
            result = jQuery.parseJSON(result);
            var html = '<fieldset class="validate-required">';
            for (let i = 0; i<result.length; i++) {
                jQuery('#payone_secured_installment_workorderid').val(result[i].workorderid);
                html += '<h4><input class="input-radio" style="margin-right:10px;" type="radio" id="payone_secured_installment_option_' + i + '" name="payone_secured_installment_option" value="' + result[i].option_id + '">';
                html += '<label for="payone_secured_installment_option_' + i + '">';
                html += result[i].number_of_payments + ' ' + '<?php _e( 'monthly installments', 'payone-woocommerce-3' ); ?>' + ' à ' + result[i].monthly_amount + '</label></h4>';
                html += '<table class="table">';
                html += '<tr><th><?php _e( 'Total amount', 'payone-woocommerce-3' ); ?></th><td>' + result[i].total_amount_value + '</td></tr>';
                html += '<tr><th><?php _e( 'Interest rate', 'payone-woocommerce-3' ); ?></th><td>' + result[i].nominal_interest_rate + '</td></tr>';
                html += '<tr><th><?php _e( 'Annual percentage rate', 'payone-woocommerce-3' ); ?></th><td>' + result[i].effective_interest_rate + '</td></tr>';
                html += '<tr><th colspan="2"><a href="' + result[i].info_url + '" target="_blank" rel="noopener"><?php _e( 'Link to credit information', 'payone-woocommerce-3' ); ?></a></th></th></tr>';
                html += '</table>';
            }
            html += '</fieldset>';
            jQuery('#payone_secured_installments_options').html(html);
            payone_unblock();
        });
    }
    function payone_checkout_clicked_<?php echo \Payone\Gateway\SecuredInstallment::GATEWAY_ID; ?>() {
        var messages = '';

        if ( typeof jQuery('input[name="payone_secured_installment_option"]:checked').val() === 'undefined' ) {
            messages += '<?php _e( 'Please choose a payment plan!', 'payone-woocommerce-3' ); ?><br>';
        }
        if ( jQuery('#payone_secured_installment_birthday').val() === '' ) {
            messages += '<?php _e( 'Please enter your birthday!', 'payone-woocommerce-3' ); ?><br>';
        }
        if ( jQuery('#payone_secured_installment_iban').val() === '' ) {
            messages += '<?php _e( 'Please enter your IBAN!', 'payone-woocommerce-3' ); ?><br>';
        }

        jQuery('#payoneSecuredInstallmentErrorOutput').html('<strong style="color:red">' + messages + '</strong>');

        return messages.length === 0;
    }
</script>

<link id="paylaDcsCss" type="text/css" rel="stylesheet" href="https://d.payla.io/dcs/dcs.css?st=<?php echo esc_url($snippet_token); ?>&pi=<?php echo esc_url(self::PAYLA_PARTNER_ID); ?>&psi=<?php echo esc_url($this->get_merchant_id()); ?>&e=<?php echo esc_url($environment); ?>">

<input type="hidden" name="payone_secured_installment_token" value="<?php echo esc_attr($snippet_token); ?>">
<input type="hidden" id="payone_secured_installment_workorderid" name="payone_secured_installment_workorderid" value="">
<fieldset>
    <p class="form-row form-row-full validate-required" id="payone_secured_installment_birthday_field">
        <label for="payone_secured_installment_birthday">
			<?php _e( 'Birthday', 'payone-woocommerce-3' ); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="date" class="input-text " name="payone_secured_installment_birthday" id="payone_secured_installment_birthday">
        </span>
    </p>
    <p class="form-row form-row-full validate-required" id="payone_secured_installment_iban_field">
        <label for="payone_secured_installment_birthday">
			<?php _e( 'IBAN', 'payone-woocommerce-3' ); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="payone_secured_installment_iban" id="payone_secured_installment_iban">
        </span>
    </p>
</fieldset>
<div id="payone_secured_installments_options"></div>
<?php include( '_disclaimer.php' ); ?>
<div id="payoneSecuredInstallmentErrorOutput"></div>
