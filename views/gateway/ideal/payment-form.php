<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<fieldset>
    <p class="form-row form-row-wide">
        <label for="bankgrouptype"><?php _e( 'Bank group', 'payone-woocommerce-3' ) ?></label>
        <select class="payoneSelect" id="bankgrouptype" name="bankgrouptype">
            <?php foreach ( $bankgroups as $value => $label ) { ?>
                <option value="<?php echo $value; ?>">
                    <?php echo $label; ?>
                </option>
            <?php } ?>
        </select>
    </p>
</fieldset>
