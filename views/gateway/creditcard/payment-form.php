<?php
/**
 * @var \Payone\Gateway\CreditCard $this
 */
?>

<input type="hidden" name="card_pseudopan" id="card_pseudopan">
<input type="hidden" name="card_truncatedpan" id="card_truncatedpan">
<input type="hidden" name="card_firstname" id="card_firstname">
<input type="hidden" name="card_lastname" id="card_lastname">
<input type="hidden" name="card_type" id="card_type">
<input type="hidden" name="card_expiredate" id="card_expiredate">
<?php
    $cardnumber_css = '';
    if ( $this->get_option( 'cc_field_cardnumber_style' ) === 'custom' ) {
	    $cardnumber_css = $this->get_option( 'cc_field_cardnumber_css' );
    }
    $cvc2_css = '';
    if ( $this->get_option( 'cc_field_cvc2_style' ) === 'custom' ) {
	    $cvc2_css = $this->get_option( 'cc_field_cvc2_css' );
    }
    $month_css = '';
    if ( $this->get_option( 'cc_field_month_style' ) === 'custom' ) {
	    $month_css = $this->get_option( 'cc_field_month_css' );
    }
    $year_css = '';
    if ( $this->get_option( 'cc_field_year_style' ) === 'custom' ) {
        $year_css = $this->get_option( 'cc_field_year_css' );
    }
?>
<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<fieldset>
    <p class="form-row form-row-wide">
        <label for="cardtypeInput"><?php _e( 'Card type', 'payone-woocommerce-3' ) ?></label>
        <select class="payoneSelect" id="cardtype">
            <?php foreach ( $this->get_option( 'cc_brands' ) as $cc_brand ) { ?>
                <option value="<?php echo $cc_brand; ?>">
                    <?php echo $this->get_option( 'cc_brand_label_' . $cc_brand ); ?>
                </option>
            <?php } ?>
        </select>
    </p>

    <p class="form-row form-row-wide">
        <label for="cardpan"><?php _e( 'Cardpan', 'payone-woocommerce-3' ) ?></label>
        <span class="inputIframe" id="cardpan"></span>
    </p>

    <p class="form-row form-row-wide">
        <label for="cvcInput"><?php _e( 'CVC', 'payone-woocommerce-3' ) ?></label>
        <span id="cardcvc2" class="inputIframe"></span>
    </p>

    <p class="form-row form-row-wide">
        <label for="expireInput"><?php _e( 'Expire Date', 'payone-woocommerce-3' ) ?></label>
        <span id="expireInput" class="inputIframe">
            <span id="cardexpiremonth"></span>
            <span id="cardexpireyear"></span>
        </span>
    </p>

    <p class="form-row form-row-wide">
        <label for="payone_card_firstname"><?php _e( 'Firstname', 'payone-woocommerce-3' ) ?></label>
        <input class="payoneInput" id="payone_card_firstname" type="text" name="card_firstname" value="">
    </p>

    <p class="form-row form-row-wide">
        <label for="payone_card_lastname"><?php _e( 'Lastname', 'payone-woocommerce-3' ) ?></label>
        <input class="payoneInput" id="payone_card_lastname" type="text" name="card_lastname" value="">
    </p>

    <div id="errorOutput"></div>
</fieldset>
<div id="paymentform"></div>
<script>
    var request, config;

    config = {
        fields: {
            cardpan: {
                selector: "cardpan",
                type: "<?php echo $this->get_option( 'cc_field_cardnumber_type' ); ?>",
                style: "<?php echo $cardnumber_css; ?>",
                size: "<?php echo $this->get_option( 'cc_field_cardnumber_length' ); ?>",
                maxlength: "<?php echo $this->get_option( 'cc_field_cardnumber_maxchars' ); ?>"
                <?php if ($this->get_option( 'cc_field_cardnumber_iframe' ) === 'custom') { ?>
                , iframe: {
                    width: "<?php echo $this->get_option( 'cc_field_cardnumber_width' ); ?>",
                    height: "<?php echo $this->get_option( 'cc_field_cardnumber_width' ); ?>"
                }
                <?php } ?>
            },
            cardcvc2: {
                selector: "cardcvc2",
                type: "<?php echo $this->get_option( 'cc_field_cvc2_type' ); ?>",
                style: "<?php echo $cvc2_css; ?>",
                size: "<?php echo $this->get_option( 'cc_field_cvc2_length' ); ?>",
                maxlength: "<?php echo $this->get_option( 'cc_field_cvc2_maxchars' ); ?>",
                length: { "V": 3, "M": 3, "A": 4, "D": 3, "J": 0, "O": 3, "P": 3, "U": 3 }
	            <?php if ($this->get_option( 'cc_field_cvc2_iframe' ) === 'custom') { ?>
                , iframe: {
                    width: "<?php echo $this->get_option( 'cc_field_cvc2_width' ); ?>",
                    height: "<?php echo $this->get_option( 'cc_field_cvc2_width' ); ?>"
                }
	            <?php } ?>
            },
            cardexpiremonth: {
                selector: "cardexpiremonth",
                type: "<?php echo $this->get_option( 'cc_field_month_type' ); ?>",
                style: "<?php echo $month_css; ?>",
                size: "<?php echo $this->get_option( 'cc_field_month_length' ); ?>",
                maxlength: "<?php echo $this->get_option( 'cc_field_month_maxchars' ); ?>"
                <?php if ($this->get_option( 'cc_field_month_iframe' ) === 'custom') { ?>
                , iframe: {
                    width: "<?php echo $this->get_option( 'cc_field_month_width' ); ?>",
                    height: "<?php echo $this->get_option( 'cc_field_month_width' ); ?>"
                }
                <?php } ?>
            },
            cardexpireyear: {
                selector: "cardexpireyear",
                type: "<?php echo $this->get_option( 'cc_field_year_type' ); ?>",
                style: "<?php echo $year_css; ?>",
                size: "<?php echo $this->get_option( 'cc_field_year_length' ); ?>",
                maxlength: "<?php echo $this->get_option( 'cc_field_year_maxchars' ); ?>"
	            <?php if ($this->get_option( 'cc_field_year_iframe' ) === 'custom') { ?>
                , iframe: {
                    width: "<?php echo $this->get_option( 'cc_field_year_width' ); ?>",
                    height: "<?php echo $this->get_option( 'cc_field_year_width' ); ?>"
                }
	            <?php } ?>
            }
        },
        defaultStyle: {
            input: "<?php echo $this->get_option( 'cc_default_style_input' ); ?>",
            select: "<?php echo $this->get_option( 'cc_default_style_select' ); ?>",
            iframe: {
                width: "<?php echo $this->get_option( 'cc_default_style_iframe_width' ); ?>",
                height: "<?php echo $this->get_option( 'cc_default_style_iframe_height' ); ?>"
            }
        },
        error: "<?php echo $this->get_option( 'cc_error_output_active' ) ? 'errorOutput' : ''; ?>",
        language: Payone.ClientApi.Language.<?php echo $this->get_option( 'cc_error_output_language' ); ?>,
        events: {
            rendered: function () {
                iframes.setCardType('<?php $cc_brand_choices = $this->get_option('cc_brands'); echo esc_attr( $cc_brand_choices[0] ); ?>');
            }
        }
    };

    request = {
        request: 'creditcardcheck',
        responsetype: 'JSON',
        mode: '<?php echo esc_attr( $options['mode'] ); ?>',
        mid: '<?php echo esc_attr( $options['merchant_id'] ); ?>',
        aid: '<?php echo esc_attr( $options['account_id'] ); ?>',
        portalid: '<?php echo esc_attr( $options['portal_id'] ); ?>',
        encoding: 'UTF-8',
        storecarddata: 'yes',
        hash: '<?php echo esc_attr( $hash ); ?>'
    };
    var iframes = new Payone.ClientApi.HostedIFrames(config, request);

    document.getElementById('cardtype').onchange = function () {
        iframes.setCardType(this.value);
    };

    jQuery(document).on('click', '#place_order', function () {
        var currentGateway = jQuery('input[name=payment_method]:checked').val();

        return currentGateway === '<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>'
            ? payone_checkout_clicked_<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>()
            : true;
    });

    var check_status = false;

    function payone_checkout_clicked_<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>() {
        if (check_status === true) {
            // Skip the test, as it already succeeded.
            return true;
        }

        if (iframes.isComplete()) {
            iframes.creditCardCheck('checkCallback');
        } else {
            jQuery('#errorOutput').html('<strong style="color:red">Bitte Formular vollständig ausfüllen!</strong>');
            payone_unblock();
        }

        // Bearbeitung hier abschließen. Das Submit wird dann über "checkCallback" realisiert.
        return false;
    }

    function checkCallback(response) {
        if (response.status === "VALID") {
            check_status = true;
            document.getElementById("card_pseudopan").value = response.pseudocardpan;
            document.getElementById("card_truncatedpan").value = response.truncatedcardpan;
            document.getElementById("card_type").value = response.cardtype;
            document.getElementById("card_expiredate").value = response.cardexpiredate;
            payone_unblock();
            jQuery('#place_order').parents('form').submit();
        } else {
            jQuery('#errorOutput').html('<strong style="color:red">'  + response.errormessage + '</strong>');
            payone_unblock();
            return false;
        }
    }
</script>
