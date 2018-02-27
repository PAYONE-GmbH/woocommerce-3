<input type="hidden" name="card_pseudopan" id="card_pseudopan">
<input type="hidden" name="card_truncatedpan" id="card_truncatedpan">
<input type="hidden" name="card_firstname" id="card_firstname">
<input type="hidden" name="card_lastname" id="card_lastname">
<input type="hidden" name="card_type" id="card_type">
<input type="hidden" name="card_expiredate" id="card_expiredate">

<script type="text/javascript" src="https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js"></script>
<!--form name="paymentform" action="" method="post"-->
	<fieldset>
		<!-- configure your cardtype-selection here -->
		<label for="cardtypeInput">Card type</label>
		<select id="cardtype">
			<option value="V">VISA</option>
			<option value="M">Mastercard</option>
			<option value="A">Amex</option>
		</select>

		<label for="cardpanInput">Cardpan:</label>
		<span class="inputIframe" id="cardpan"></span>

		<label for="cvcInput">CVC:</label>
		<span id="cardcvc2" class="inputIframe"></span>

		<label for="expireInput">Expire Date:</label>
		<span id="expireInput" class="inputIframe">
            <span id="cardexpiremonth"></span>
            <span id="cardexpireyear"></span>
        </span>

		<label for="card_firstname">Firstname:</label>
		<input id="card_firstname" type="text" name="card_firstname" value="">
		<label for="card_lastname">Lastname:</label>
		<input id="card_lastname" type="text" name="card_lastname" value="">

		<div id="errorOutput"></div>
	</fieldset>
<!--/form-->
<div id="paymentform"></div>
<script>
    var request, config;

    config = {
        fields: {
            cardpan: {
                selector: "cardpan",                 // put name of your div-container here
                type: "text",                        // text (default), password, tel
                style: "font-size: 1em; border: 1px solid #000;"
            },
            cardcvc2: {
                selector: "cardcvc2",                // put name of your div-container here
                type: "password",                    // select(default), text, password, tel
                style: "font-size: 1em; border: 1px solid #000;",
                size: "4",
                maxlength: "4"
            },
            cardexpiremonth: {
                selector: "cardexpiremonth",         // put name of your div-container here
                type: "select",                      // select(default), text, password, tel
                size: "2",
                maxlength: "2",
                iframe: {
                    width: "50px"
                }
            },
            cardexpireyear: {
                selector: "cardexpireyear",          // put name of your div-container here
                type: "select",                      // select(default), text, password, tel
                iframe: {
                    width: "80px"
                }
            }
        },
        defaultStyle: {
            input: "font-size: 1em; border: 1px solid #000; width: 175px;",
            select: "font-size: 1em; border: 1px solid #000;",
            iframe: {
                height: "33px",
                width: "180px"
            }
        },
        error: "errorOutput",                        // area to display error-messages (optional)
        language: Payone.ClientApi.Language.de       // Language to display error-messages
                                                     // (default: Payone.ClientApi.Language.en)
    };

    request = {
        request: 'creditcardcheck',
        responsetype: 'JSON',
        mode: '<?php echo $options['mode']; ?>',
        mid: '<?php echo $options['merchant_id']; ?>',
        aid: '<?php echo $options['account_id']; ?>',
        portalid: '<?php echo $options['portal_id']; ?>',
        encoding: 'UTF-8',
        storecarddata: 'yes',
        hash: '<?php echo $hash; ?>'
    };
    var iframes = new Payone.ClientApi.HostedIFrames(config, request);
    iframes.setCardType("V");

    document.getElementById('cardtype').onchange = function () {
        iframes.setCardType(this.value);              // on change: set new type of credit card to process
    };

    var check_status = false;

    jQuery( 'form.woocommerce-checkout' ).on( 'checkout_place_order', function(event) {
        if (jQuery('input[name=payment_method]:checked').val() !== '<?php echo \Payone\Gateway\CreditCard::GATEWAY_ID; ?>') {
            // Only needed for creditcard payment
            return true;
        }
        if (check_status === true) {
            // Skip the test, as it already succeeded.
            return true;
        }

        if (iframes.isComplete()) {
            iframes.creditCardCheck('checkCallback');// Perform "CreditCardCheck" to create and get a
                                                     // PseudoCardPan; then call your function "checkCallback"
        } else {
            jQuery('#errorOutput').html('<strong style="color:red">Bitte Formular vollständig ausfüllen!</strong>');
        }

        // Bearbeitung hier abschließen. Das Submit wird dann über "checkCallback" realisiert.
        return false;
    });

    function checkCallback(response) {
        if (response.status === "VALID") {
            check_status = true;
            document.getElementById("card_pseudopan").value = response.pseudocardpan;
            document.getElementById("card_truncatedpan").value = response.truncatedcardpan;
            document.getElementById("card_type").value = response.cardtype;
            document.getElementById("card_expiredate").value = response.cardexpiredate;
            jQuery('#place_order').parents('form').submit();
        }
    }
</script>