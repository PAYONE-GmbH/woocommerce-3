<input type="hidden" name="card_pseudopan" id="card_pseudopan">
<input type="hidden" name="card_truncatedpan" id="card_truncatedpan">
<input type="hidden" name="card_firstname" id="card_firstname">
<input type="hidden" name="card_lastname" id="card_lastname">
<input type="hidden" name="card_type" id="card_type">
<input type="hidden" name="card_expiredate" id="card_expiredate">

<?php
    $cardnumber_css = '';
    if ( $this->get_option( 'cc_field_cardnumber_style' ) === 'custom' ) {
	    $cardnumber_css = ' style="' . $this->get_option( 'cc_field_cardnumber_css' ) . '"';
    }
    $cvc2_css = '';
    if ( $this->get_option( 'cc_field_cvc2_style' ) === 'custom' ) {
	    $cvc2_css = ' style="' . $this->get_option( 'cc_field_cvc2_css' ) . '"';
    }
    $month_css = '';
    if ( $this->get_option( 'cc_field_month_style' ) === 'custom' ) {
	    $month_css = ' style="' . $this->get_option( 'cc_field_month_css' ) . '"';
    }
    $year_css = '';
    if ( $this->get_option( 'cc_field_year_style' ) === 'custom' ) {
        $year_css = ' style="' . $this->get_option( 'cc_field_year_css' ) . '"';
    }
?>
<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<fieldset>
    <label for="cardtypeInput">Card type</label>
    <select id="cardtype">
        <?php foreach ( $this->get_option( 'cc_brands' ) as $cc_brand ) { ?>
            <option value="<?php echo $cc_brand; ?>">
                <?php echo $this->get_option( 'cc_brand_label_' . $cc_brand ); ?>
            </option>
        <?php } ?>
    </select>

    <label for="cardpanInput">Cardpan:</label>
    <span class="inputIframe" id="cardpan"<?php echo $cardnumber_css; ?>></span>

    <label for="cvcInput">CVC:</label>
    <span id="cardcvc2" class="inputIframe"<?php echo $cvc2_css; ?>></span>

    <label for="expireInput">Expire Date:</label>
    <span id="expireInput" class="inputIframe">
        <span id="cardexpiremonth"<?php echo $month_css; ?>></span>
        <span id="cardexpireyear"<?php echo $year_css; ?>></span>
    </span>

    <label for="card_firstname">Firstname:</label>
    <input id="card_firstname" type="text" name="card_firstname" value="">
    <label for="card_lastname">Lastname:</label>
    <input id="card_lastname" type="text" name="card_lastname" value="">

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
                style: "font-size: 1em; border: 1px solid #000;",
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
                style: "font-size: 1em; border: 1px solid #000;",
                size: "<?php echo $this->get_option( 'cc_field_cvc2_length' ); ?>",
                maxlength: "<?php echo $this->get_option( 'cc_field_cvc2_maxchars' ); ?>"
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
        language: Payone.ClientApi.Language.<?php echo $this->get_option( 'cc_error_output_language' ); ?>
    };

    request = {
        request: 'creditcardcheck',
        responsetype: 'JSON',
        mode: '<?php echo $options['mode']; // @todo Ausgaben escapen ?>',
        mid: '<?php echo $options['merchant_id']; ?>',
        aid: '<?php echo $options['account_id']; ?>',
        portalid: '<?php echo $options['portal_id']; ?>',
        encoding: 'UTF-8',
        storecarddata: 'yes',
        hash: '<?php echo $hash; ?>'
    };
    var iframes = new Payone.ClientApi.HostedIFrames(config, request);
    iframes.setCardType('<?php $cc_brand_choices = $this->get_option('cc_brands'); echo $cc_brand_choices[0]; ?>');

    document.getElementById('cardtype').onchange = function () {
        iframes.setCardType(this.value);              // on change: set new type of credit card to process
    };

    var check_status = false;

    function payone_checkout_clicked_<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>() {
        if (check_status === true) {
            // Skip the test, as it already succeeded.
            return true;
        }

        if (iframes.isComplete()) {
            iframes.creditCardCheck('checkCallback');// Perform "CreditCardCheck" to create and get a
                                                     // PseudoCardPan; then call your function "checkCallback"
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