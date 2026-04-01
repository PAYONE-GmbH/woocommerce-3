<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<input type="hidden" id="payone_googlepay_token" name="payone_googlepay_token" value="">
<div id="payone-googlepay-button-container" style="display:none;"></div>
<script>
(function() {
    const baseRequest = {
        apiVersion: 2,
        apiVersionMinor: 0
    };
    const baseCardPaymentMethod = {
        type: 'CARD',
        parameters: {
            allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
            allowedCardNetworks: ["MASTERCARD", "VISA"],
            allowPrepaidCards: true,
            allowCreditCards: true
        }
    };
    const cardPaymentMethod = Object.assign(
        {tokenizationSpecification: {
            type: 'PAYMENT_GATEWAY',
            parameters: {
                'gateway': '<?php echo $this->get_googlepay_merchant_name(); ?>',
                'gatewayMerchantId': '<?php echo $this->get_googlepay_merchant_id(); ?>'
            }
        }},
        baseCardPaymentMethod
    );
    const environment = '<?php echo $environment ?>';
    const ajaxUrl = '<?php echo \Payone\Plugin::get_callback_url( [ 'type' => 'ajax-cart-info' ] ); ?>';
    const gatewayId = '<?php echo \Payone\Gateway\GooglePay::GATEWAY_ID; ?>';

    window.payone_google_pay_finished = false;

    function toggleButtons() {
        const isGooglePay = jQuery('input[name=payment_method]:checked').val() === gatewayId;
        const container = jQuery('#payone-googlepay-button-container');
        const placeOrderButton = jQuery('#place_order');

        if (isGooglePay && container.length && placeOrderButton.length) {
            placeOrderButton.after(container);
        }

        placeOrderButton.toggle(!isGooglePay);
        container.toggle(isGooglePay);
    }

    function onButtonClicked() {
        jQuery.post(ajaxUrl, function (result) {
            const cartInfo = jQuery.parseJSON(result);
            const paymentsClient = new google.payments.api.PaymentsClient({environment: environment});
            const paymentDataRequest = Object.assign({}, baseRequest);
            paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
            paymentDataRequest.transactionInfo = {
                totalPriceStatus: 'FINAL',
                totalPrice: cartInfo.amount,
                currencyCode: cartInfo.currency,
                countryCode: cartInfo.country
            };
            paymentsClient.loadPaymentData(paymentDataRequest).then(function(paymentData) {
                document.getElementById("payone_googlepay_token").value = btoa(paymentData.paymentMethodData.tokenizationData.token);
                window.payone_google_pay_finished = true;
                jQuery('#place_order').parents('form').submit();
            }).catch(function(err) {
                window.payone_google_pay_finished = false;
                if (err.statusCode !== 'CANCELED') {
                    console.error('Google Pay error:', err);
                }
            });
        });
    }

    function renderGooglePayButton() {
        const paymentsClient = new google.payments.api.PaymentsClient({environment: environment});
        const isReadyToPayRequest = Object.assign({}, baseRequest);
        isReadyToPayRequest.allowedPaymentMethods = [baseCardPaymentMethod];

        paymentsClient.isReadyToPay(isReadyToPayRequest)
            .then(function(response) {
                if (response.result) {
                    const button = paymentsClient.createButton({
                        onClick: onButtonClicked,
                        buttonColor: 'black',
                        buttonType: 'buy',
                        buttonSizeMode: 'fill',
                        buttonLocale: 'de',
                        allowedPaymentMethods: [baseCardPaymentMethod]
                    });
                    const container = document.getElementById('payone-googlepay-button-container');
                    container.innerHTML = '';
                    container.appendChild(button);
                    toggleButtons();
                }
            })
            .catch(function(err) {
                console.error('Google Pay isReadyToPay error:', err);
            });
    }

    // SDK already loaded (cached) → render button directly
    if (typeof google !== 'undefined' && google.payments) {
        renderGooglePayButton();
    } else {
        // SDK not yet loaded → load it and render on load
        if (!document.querySelector('script[src*="pay.google.com"]')) {
            const script = document.createElement('script');
            script.src = 'https://pay.google.com/gp/p/js/pay.js';
            script.async = true;
            script.onload = renderGooglePayButton;
            document.head.appendChild(script);
        } else {
            // Script tag exists but SDK not ready yet → poll briefly
            const interval = setInterval(function() {
                if (typeof google !== 'undefined' && google.payments) {
                    clearInterval(interval);
                    renderGooglePayButton();
                }
            }, 100);
        }
    }

    // Fallback for checkout_place_order handler in checkout.js.php
    window[`payone_checkout_clicked_${gatewayId}`] = function() {
        if (window.payone_google_pay_finished === true) {
            return true;
        }
        onButtonClicked();
        return false;
    };

    jQuery('body').off('payment_method_selected.payone_googlepay')
        .on('payment_method_selected.payone_googlepay', toggleButtons);
    toggleButtons();
})();
</script>
