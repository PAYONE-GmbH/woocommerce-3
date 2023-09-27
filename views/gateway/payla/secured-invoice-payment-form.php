<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<script id="paylaDcs" type="text/javascript" src="https://d.payla.io/dcs/<?php echo esc_attr(self::PAYLA_PARTNER_ID); ?>/<?php echo esc_attr($this->get_merchant_id()); ?>/dcs.js"></script>
<script>
    var paylaDcsT = paylaDcs.init("<?php echo $environment; ?>", "<?php echo $snippet_token; ?>");
    function payone_checkout_clicked_<?php echo \Payone\Gateway\SecuredInvoice::GATEWAY_ID; ?>() {
        var messages = '';
        if ( jQuery('#payone_secured_invoice_birthday').val() === '' ) {
            messages += '<?php _e( 'Please enter your birthday!', 'payone-woocommerce-3' ); ?>';
        }
        jQuery('#payoneSecuredInvoiceErrorOutput').html('<strong style="color:red">' + messages + '</strong>');
        return messages.length === 0;
    }
</script>

<link id="paylaDcsCss" type="text/css" rel="stylesheet" href="https://d.payla.io/dcs/dcs.css?st=<?php echo esc_url($snippet_token); ?>&pi=<?php echo esc_url(self::PAYLA_PARTNER_ID); ?>&psi=<?php echo esc_url($this->get_merchant_id()); ?>&e=<?php echo esc_url($environment); ?>">

<input type="hidden" name="payone_secured_invoice_token" value="<?php echo esc_attr($snippet_token); ?>">
<fieldset>
    <p class="form-row form-row-full validate-required" id="payone_secured_invoice_birthday_field">
        <label for="payone_secured_invoice_birthday">
			<?php _e( 'Birthday', 'payone-woocommerce-3' ); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="date" class="input-text " name="payone_secured_invoice_birthday" id="payone_secured_invoice_birthday">
        </span>
    </p>
	<?php if ( $this->is_b2b() ) { ?>
        <p class="form-row form-row-full" id="payone_secured_invoice_vatid_field">
            <label for="payone_secured_invoice_vatid_field">
                <?php _e( 'VAT-ID', 'payone-woocommerce-3' ); ?>
            </label>
            <span class="woocommerce-input-wrapper">
                <input type="text" class="input-text " name="payone_secured_invoice_vatid" id="payone_secured_invoice_vatid">
            </span>
        </p>
	<?php } ?>
</fieldset>

<?php include( '_disclaimer.php' ); ?>
<div id="payoneSecuredInvoiceErrorOutput"></div>
