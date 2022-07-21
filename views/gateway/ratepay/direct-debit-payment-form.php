<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<div id="ratepay_direct_debit_error"></div>
<fieldset>
    <p class="form-row form-row-full validate-required" id="ratepay_direct_debit_birthday_field">
        <label for="ratepay_direct_debit_birthday">
            <?php _e('Birthday', 'payone-woocommerce-3'); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="date" class="input-text " name="ratepay_direct_debit_birthday" id="ratepay_direct_debit_birthday">
        </span>
    </p>
    <p class="form-row form-row-full validate-required" id="ratepay_direct_debit_iban_field">
        <label for="ratepay_direct_debit_iban">
            <?php _e('IBAN', 'payone-woocommerce-3'); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="ratepay_direct_debit_iban" id="ratepay_direct_debit_iban">
        </span>
    </p>
</fieldset>

<?php include( '_disclaimer.php' );
