<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<script>
    function payone_checkout_clicked_<?php echo \Payone\Gateway\Giropay::GATEWAY_ID; ?>() {
        jQuery('#payone_giropay_error_output').html('');
        if ( ! payone_valid_iban( jQuery('#giropay_iban').val() ) ) {
            jQuery('#payone_giropay_error_output').html('<strong style="color:red">' + '<?php _e( 'Please enter a valid IBAN!', 'payone-woocommerce-3' ); ?>' + '</strong>');
            payone_unblock();
            return false;
        }
        payone_unblock();
        return true;
    }
</script>
<input type="hidden" id="giropay_reference" name="giropay_reference" value="">
<div id="giropay_wrapper">
    <p id="giropay_iban_field" class="form-row form-row-wide">
        <label for="giropay_iban"><?php _e( 'IBAN', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="giropay_iban" id="giropay_iban">
    </p>
    <p id="giropay_bic_field" class="form-row form-row-wide">
        <label for="giropay_bic"><?php _e( 'BIC', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="giropay_bic" id="giropay_bic">
    </p>
</div>
<div id="payone_giropay_error_output"></div>