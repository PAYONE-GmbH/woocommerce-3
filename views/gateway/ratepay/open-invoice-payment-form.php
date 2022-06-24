<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<div id="ratepay_open_invoice_error"></div>
<fieldset>
    <p class="form-row form-row-full validate-required" id="ratepay_open_invoice_birthday_field">
        <label for="ratepay_open_invoice_birthday">
            <?php _e('Birthday', 'payone-woocommerce-3'); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="date" class="input-text " name="ratepay_open_invoice_birthday" id="ratepay_open_invoice_birthday">
        </span>
    </p>
</fieldset>

<?php include( '_disclaimer.php' );
