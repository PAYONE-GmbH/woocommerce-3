<input type="hidden" name="card_pseudopan" id="card_pseudopan">
<input type="hidden" name="card_truncatedpan" id="card_truncatedpan">
<input type="hidden" name="card_type" id="card_type">
<input type="hidden" name="card_expiredate" id="card_expiredate">

<?php
$payone_cc_config = $this->javascript_payone_config();
// Classic checkout uses the card holder input of the form below.
$payone_cc_config['fields']['cardholder']['selector'] = 'card_holder';
$payone_cc_auto_detection = isset( $payone_cc_config['autoCardtypeDetection'] );
$payone_cc_brands         = $this->get_card_brands();
?>
<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<fieldset class="payone-cc-form<?php echo $payone_cc_config['modernStyle'] ? ' payone-cc-form--modern' : ''; ?>">
    <p class="form-row form-row-wide">
        <label for="card_holder"
               title="<?php _e( 'as printed on card', 'payone-woocommerce-3' ) ?>"><?php _e( 'Card Holder', 'payone-woocommerce-3' ) ?></label>
        <input class="payoneInput" id="card_holder" type="text" name="card_holder" value="" maxlength="50" autocomplete="cc-name">
    </p>

	<?php if ( ! $payone_cc_auto_detection ) { ?>
    <p class="form-row form-row-wide">
        <label for="cardtype"><?php _e( 'Card type', 'payone-woocommerce-3' ) ?></label>
        <select class="payoneSelect" id="cardtype">
			<?php foreach ( $payone_cc_brands as $cc_brand ) { ?>
                <option value="<?php echo esc_attr( $cc_brand['value'] ); ?>">
					<?php echo esc_html( $cc_brand['title'] ); ?>
                </option>
			<?php } ?>
        </select>
    </p>
	<?php } ?>

    <p class="form-row form-row-wide">
        <span class="payone-cc-label-row">
            <label for="cardpan"><?php _e( 'Cardpan', 'payone-woocommerce-3' ) ?></label>
			<?php if ( $payone_cc_auto_detection ) { ?>
                <span class="payone-cc-brands" id="payone-cc-brands">
				<?php foreach ( $payone_cc_brands as $cc_brand ) { ?>
                    <span class="payone-cc-brand" data-brand="<?php echo esc_attr( $cc_brand['value'] ); ?>" title="<?php echo esc_attr( $cc_brand['title'] ); ?>">
					<?php if ( $cc_brand['logo'] !== '' ) { ?>
                        <img src="<?php echo esc_url( $cc_brand['logo'] ); ?>" alt="<?php echo esc_attr( $cc_brand['title'] ); ?>">
					<?php } else { ?>
                        <span class="payone-cc-brand__text"><?php echo esc_html( $cc_brand['title'] ); ?></span>
					<?php } ?>
                    </span>
				<?php } ?>
                </span>
			<?php } ?>
        </span>
        <span class="inputIframe" id="cardpan"></span>
    </p>

    <div class="payone-cc-row">
        <p class="form-row form-row-wide">
            <label for="expireInput"><?php _e( 'Expire Date', 'payone-woocommerce-3' ) ?></label>
            <span id="expireInput" class="inputIframe">
                <span id="cardexpiremonth"></span>
                <span id="cardexpireyear"></span>
            </span>
        </p>

        <p class="form-row form-row-wide">
            <label for="cardcvc2"><?php _e( 'CVC', 'payone-woocommerce-3' ) ?></label>
            <span id="cardcvc2" class="inputIframe"></span>
        </p>
    </div>

    <div id="errorOutput"></div>
</fieldset>
<div id="paymentform"></div>
<script>
    var payone_request, payone_config;
    payone_config = <?php echo wp_json_encode( $payone_cc_config ); ?>;
    payone_config.language = Payone.ClientApi.Language[payone_config.language] || Payone.ClientApi.Language.de;
    payone_config.events = {
        rendered: function () {
            if (!payone_config.autoCardtypeDetection && payone_config.cardType) {
                payone_iframes.setCardType(payone_config.cardType);
            }
        }
    };
    if (payone_config.autoCardtypeDetection) {
        payone_config.autoCardtypeDetection.callback = function (detectedCardtype) {
            var brands = document.querySelectorAll('#payone-cc-brands .payone-cc-brand');
            var known = false;
            for (var i = 0; i < brands.length; i++) {
                var match = brands[i].getAttribute('data-brand') === detectedCardtype;
                known = known || match;
                brands[i].classList.toggle('is-active', match);
            }
            document.getElementById('payone-cc-brands').classList.toggle('has-active', known);
        };
    }
    payone_request = {
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
    var payone_iframes = new Payone.ClientApi.HostedIFrames(payone_config, payone_request);
    if (document.getElementById('cardtype')) {
        document.getElementById('cardtype').onchange = function () {
            payone_iframes.setCardType(this.value);
        };
    }
    jQuery(document).on('click', '#place_order', function () {
        var currentGateway = jQuery('input[name=payment_method]:checked').val();
        return currentGateway === '<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>'
            ? payone_checkout_clicked_<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>()
            : true;
    });
    var payone_check_status = false;
    function payone_checkout_clicked_<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>() {
        if (payone_check_status === true) {
            // Skip the test, as it already succeeded.
            return true;
        }
        var cardholder_ok = payone_check_cardholder();
        if (payone_iframes.isComplete() && cardholder_ok === true) {
            payone_iframes.creditCardCheck('payone_check_callback');
        } else {
            var error_message = 'Bitte Formular vollständig ausfüllen!';
            if (cardholder_ok !== true) {
                error_message += '<br>' + cardholder_ok;
            }
            jQuery('#errorOutput').html('<strong style="color:red">' + error_message + '</strong>');
            payone_unblock();
        }
        // Bearbeitung hier abschließen. Das Submit wird dann über "checkCallback" realisiert.
        return false;
    }
    function payone_check_cardholder() {
        var cardholder = document.getElementById("card_holder").value;
        if (cardholder.length > 50 || cardholder.match(/[^a-zA-Z \-äöüÄÖÜß]/g)) {
            return 'Bitte geben Sie maximal 50 Zeichen für den Karteninhaber ein, Sonderzeichen außer Deutsche Umlaute und einem Bindestrich sind nicht erlaubt.';
        }
        return true;
    }
    function payone_check_callback(response) {
        if (response.status === "VALID") {
            payone_check_status = true;
            document.getElementById("card_pseudopan").value = response.pseudocardpan;
            document.getElementById("card_truncatedpan").value = response.truncatedcardpan;
            document.getElementById("card_type").value = response.cardtype;
            document.getElementById("card_expiredate").value = response.cardexpiredate;
            payone_unblock();
            jQuery('#place_order').parents('form').submit();
        } else {
            jQuery('#errorOutput').html('<strong style="color:red">' + response.errormessage + '</strong>');
            payone_unblock();
            return false;
        }
    }
</script>
