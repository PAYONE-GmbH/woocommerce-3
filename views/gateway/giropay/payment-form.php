<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
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