<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<input type="hidden" id="bancontact_reference" name="bancontact_reference" value="">
<div id="bancontact_wrapper">
    <p id="bancontact_iban_field" class="form-row form-row-wide">
        <label for="bancontact_iban"><?php _e( 'IBAN', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="bancontact_iban" id="bancontact_iban">
    </p>
    <p id="bancontact_bic_field" class="form-row form-row-wide">
        <label for="bancontact_bic"><?php _e( 'BIC', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="bancontact_bic" id="bancontact_bic">
    </p>
</div>
