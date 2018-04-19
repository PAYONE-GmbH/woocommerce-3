<h1><?php echo __('SOFORT.com', 'payone-woocommerce-3'); ?></h1>
<input type="hidden" id="sofort_reference" name="sofort_reference" value="">
<div id="sofort_wrapper">
    <p id="sofort_iban_field" class="form-row form-row-wide">
        <label for="sofort_iban"><?php echo __( 'IBAN', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="sofort_iban" id="sofort_iban">
    </p>
    <p id="sofort_bic_field" class="form-row form-row-wide">
        <label for="sofort_bic"><?php echo __( 'BIC', 'payone-woocommerce-3' ); ?></label>
        <input type="text" name="sofort_bic" id="sofort_bic">
    </p>
</div>