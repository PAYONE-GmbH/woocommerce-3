<script type="text/javascript" src="https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js"></script>
<form name="paymentform" action="" method="post">
	<fieldset>
		<input type="hidden" name="pseudocardpan" id="pseudocardpan">
		<input type="hidden" name="truncatedcardpan" id="truncatedcardpan">

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

		<label for="firstname">Firstname:</label>
		<input id="firstname" type="text" name="firstname" value="">
		<label for="lastname">Lastname:</label>
		<input id="lastname" type="text" name="lastname" value="">

		<div id="errorOutput"></div>
		<input id="paymentsubmit" type="button" value="Submit" onclick="check();">
	</fieldset>
</form>
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

    function check() {                               // Function called by submitting PAY-button
	    if (iframes.isComplete()) {
            iframes.creditCardCheck('checkCallback');// Perform "CreditCardCheck" to create and get a
                                                     // PseudoCardPan; then call your function "checkCallback"
        } else {
            console.debug("not complete");
        }
    }

    function checkCallback(response) {
        console.debug(response);
        if (response.status === "VALID") {
            document.getElementById("pseudocardpan").value = response.pseudocardpan;
            document.getElementById("truncatedcardpan").value = response.truncatedcardpan;
            document.paymentform.submit();
        }
    }
</script>