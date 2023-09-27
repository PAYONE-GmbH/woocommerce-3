<script type="application/javascript">
    jQuery(document).ready(function () {
        jQuery('#ratepay_installments_birthday_field').hide();
        jQuery('#ratepay_installments_iban_field').hide();
        jQuery('#ratepay-installments-plan').hide();
        jQuery('#ratepay-installments-plan-short').show();
        jQuery('#ratepay-installments-plan-long').hide();
    });
    jQuery('#ratepay_installments_months').on('change', function () {
        payone_ratepay_installments_calculate('calculation-by-time');
    });
    jQuery('#ratepay_installments_calculate_button').on('click', function () {
        payone_ratepay_installments_calculate('calculation-by-rate');
        return false;
    });
    jQuery('#ratepay-installments-show-details').on('click', function () {
        jQuery('#ratepay-installments-plan-short').hide();
        jQuery('#ratepay-installments-plan-long').show();
        return false;
    });
    jQuery('#ratepay-installments-hide-details').on('click', function () {
        jQuery('#ratepay-installments-plan-short').show();
        jQuery('#ratepay-installments-plan-long').hide();
        return false;
    });
    function payone_ratepay_installments_calculate(calculation_type) {
        let ratepay_installment_data = {
            'calculation-type': calculation_type,
            'month': jQuery('#ratepay_installments_months').val(),
            'rate': jQuery('#ratepay_installments_rate').val(),
        };
        jQuery.post('<?php echo \Payone\Plugin::get_callback_url( [ 'type' => 'ajax-ratepay-calculate' ] ); ?>', ratepay_installment_data, function (result) {
            result = jQuery.parseJSON(result);
            jQuery('#ratepay_installments_birthday_field').show();
            jQuery('#ratepay_installments_iban_field').show();
            jQuery('#ratepay-installments-plan').show();
            jQuery('#ratepay_installments_months').val(result.number_of_rates);
            jQuery('#ratepay-installments-plan-short-rates').text(result.number_of_rates);
            jQuery('#ratepay-installments-plan-rates').text(result.number_of_rates - 1);
            jQuery('#ratepay_installments_rate').val(result.rate);
            jQuery('#ratepay-installments-plan-short-rate').text(result.rate);
            jQuery('#ratepay-installments-plan-rate').text(result.rate);
            jQuery('#ratepay-installments-plan-short-total-amount').text(result.total_amount);
            jQuery('#ratepay-installments-plan-total-amount').text(result.total_amount);
            jQuery('#ratepay-installments-plan-last-rate').text(result.last_rate);
            jQuery('#ratepay-installments-plan-amount').text(result.amount);
            jQuery('#ratepay-installments-plan-interest-service-charge').text(result.service_charge);
            jQuery('#ratepay-installments-plan-annual-percentage-rate').text(result.annual_percentage_rate);
            jQuery('#ratepay-installments-plan-interest-rate').text(result.interest_rate);
            jQuery('#ratepay-installments-plan-interest-amount').text(result.interest_amount);
            jQuery('#ratepay_installments_installment_amount').val(result.form.installment_amount);
            jQuery('#ratepay_installments_installment_number').val(result.form.installment_number);
            jQuery('#ratepay_installments_last_installment_amount').val(result.form.last_installment_amount);
            jQuery('#ratepay_installments_interest_rate').val(result.form.interest_rate);
            jQuery('#ratepay_installments_amount').val(result.form.amount);
        });
    }
    function payone_checkout_clicked_<?php echo \Payone\Gateway\RatepayInstallments::GATEWAY_ID; ?>() {
        var messages = '';
        jQuery('#ratepay_installments_error').html('');
        if ( jQuery('#ratepay_installments_birthday').val() === '' ) {
            messages += '<?php _e( 'Please enter your birthday!', 'payone-woocommerce-3' ); ?><br>';
        }
        if ( ! payone_valid_iban( jQuery('#ratepay_installments_iban').val() ) ) {
            messages += '<?php _e( 'Please enter a valid IBAN!', 'payone-woocommerce-3' ); ?><br>';
        }
        jQuery('#ratepay_installments_error').html('<strong style="color:red">' + messages + '</strong>');
        payone_unblock();
        return messages.length === 0;
    }
</script>
<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<input type="hidden" name="ratepay_installments_installment_amount" id="ratepay_installments_installment_amount"
       value="">
<input type="hidden" name="ratepay_installments_installment_number" id="ratepay_installments_installment_number"
       value="">
<input type="hidden" name="ratepay_installments_last_installment_amount"
       id="ratepay_installments_last_installment_amount" value="">
<input type="hidden" name="ratepay_installments_interest_rate" id="ratepay_installments_interest_rate" value="">
<input type="hidden" name="ratepay_installments_amount" id="ratepay_installments_amount" value="">
<fieldset>
    <p class="form-row form-row-full" id="ratepay_installments_months_field">
        <label for="ratepay_installments_months">
			<?php _e( 'Number of monthly installments', 'payone-woocommerce-3' ); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <select class="input-text " name="ratepay_installments_months" id="ratepay_installments_months">
                <option value="0">
                    <?php _e( 'Choose', 'payone-woocommerce-3' ); ?>
                </option>
                <?php foreach ( $installment_months as $months ) { ?>
                    <option value="<?php echo $months; ?>">
                        <?php echo $months ?>
                    </option>
                <?php } ?>
            </select>
        </span>
    </p>
    <p class="form-row form-row-full form-row-first" id="ratepay_installments_rate_field">
        <label for="ratepay_installments_rate">
			<?php _e( 'Monthly rate', 'payone-woocommerce-3' ); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="ratepay_installments_rate" id="ratepay_installments_rate">
        </span>
    </p>
    <p class="form-row form-row-full form-row-last">
        <label>&nbsp;</label>
        <span class="woocommerce-input-wrapper">
            <button id="ratepay_installments_calculate_button">
                <?php _e( 'Calculate', 'payone-woocommerce-3' ); ?>
            </button>
        </span>
</fieldset>
<div id="ratepay-installments-plan">
    <h3><?php _e( 'Personal calculation', 'payone-woocommerce-3' ); ?></h3>
    <div id="ratepay-installments-plan-short">
        <table class="table">
            <tr>
                <th>
                    <span id="ratepay-installments-plan-short-rates"></span> <?php _e( 'monthly installments', 'payone-woocommerce-3' ); ?>
                </th>
                <td><span id="ratepay-installments-plan-short-rate"></span>&nbsp;<?php echo $currency; ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Total amount', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-short-total-amount"></span>&nbsp;<?php echo $currency; ?></td>
            </tr>
        </table>
        <small><a href="#"
                  id="ratepay-installments-show-details"><?php _e( 'Show details', 'payone-woocommerce-3' ); ?></a></small>
    </div>
    <div id="ratepay-installments-plan-long">
        <table class="table">
            <tr>
                <th><?php _e( 'Basket amount', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-amount">&nbsp;<?php echo $currency; ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Servicecharge', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-interest-service-charge"></span>&nbsp;<?php echo $currency; ?>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Annual percentage rate', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-annual-percentage-rate"></span>&nbsp;%</td>
            </tr>
            <tr>
                <th><?php _e( 'Interest rate', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-interest-rate"></span>&nbsp;<?php echo $currency; ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Interest amount', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-interest-amount"></span>&nbsp;<?php echo $currency; ?></td>
            </tr>
            <tr></tr>
            <tr>
                <th>
                    <span id="ratepay-installments-plan-rates"></span> <?php _e( 'monthly installments', 'payone-woocommerce-3' ); ?>
                    à
                </th>
                <td><span id="ratepay-installments-plan-rate"></span>&nbsp;<?php echo $currency; ?></td>
            </tr>
            <tr>
                <th><?php _e( 'incl. one final installment', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-last-rate"></span>&nbsp;<?php echo $currency; ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Total amount', 'payone-woocommerce-3' ); ?></th>
                <td><span id="ratepay-installments-plan-total-amount"></span>&nbsp;<?php echo $currency; ?></td>
            </tr>
        </table>
        <small><a href="#"
                  id="ratepay-installments-hide-details"><?php _e( 'Hide details', 'payone-woocommerce-3' ); ?></a></small>
    </div>
</div>
<fieldset>
    <p class="form-row form-row-full validate-required" id="ratepay_installments_birthday_field">
        <label for="ratepay_installments_birthday">
			<?php _e( 'Birthday', 'payone-woocommerce-3' ); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="date" class="input-text " name="ratepay_installments_birthday"
                   id="ratepay_installments_birthday">
        </span>
    </p>
    <p class="form-row form-row-full validate-required" id="ratepay_installments_iban_field">
        <label for="ratepay_installments_iban">
			<?php _e( 'IBAN', 'payone-woocommerce-3' ); ?>
        </label>
        <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="ratepay_installments_iban" id="ratepay_installments_iban">
        </span>
    </p>
</fieldset>

<div id="ratepay_installments_error"></div>

<?php include( '_disclaimer.php' );
