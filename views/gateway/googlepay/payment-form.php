<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<input type="hidden" id="payone_googlepay_token" name="payone_googlepay_token" value="">
<script async src="https://pay.google.com/gp/p/js/pay.js"></script>
<script>
    var payone_google_pay_finished = false;
    function payone_checkout_clicked_<?php echo \Payone\Gateway\GooglePay::GATEWAY_ID; ?>() {
        if (payone_google_pay_finished === true) {
            return true;
        }

        const baseRequest = {
            apiVersion: 2,
            apiVersionMinor: 0
        };
        const tokenizationSpecification = {
            type: 'PAYMENT_GATEWAY',
            parameters: {
                'gateway': 'payonegmbh',
                'gatewayMerchantId': '<?php echo $this->get_merchant_id(); ?>'
            }
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
            {tokenizationSpecification: tokenizationSpecification},
            baseCardPaymentMethod
        );
        const paymentsClient = new google.payments.api.PaymentsClient({environment: '<?php echo $environment ?>'});
        const isReadyToPayRequest = Object.assign({}, baseRequest);
        isReadyToPayRequest.allowedPaymentMethods = [baseCardPaymentMethod];

        var ajax_url = '<?php echo \Payone\Plugin::get_callback_url( [ 'type' => 'ajax-cart-info' ] ); ?>';
        jQuery.post(ajax_url, function ( result ) {
            var cartInfo = jQuery.parseJSON( result );

            paymentsClient.isReadyToPay(isReadyToPayRequest)
                .then(function(response) {
                    if (response.result) {
                        const paymentDataRequest = Object.assign({}, baseRequest);
                        paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
                        paymentDataRequest.transactionInfo = {
                            totalPriceStatus: 'FINAL',
                            totalPrice: cartInfo.amount,
                            currencyCode: cartInfo.currency,
                            countryCode: cartInfo.country
                        };
                        paymentsClient.loadPaymentData(paymentDataRequest).then(function(paymentData){
                            paymentToken = paymentData.paymentMethodData.tokenizationData.token;
                            document.getElementById("payone_googlepay_token").value = btoa(paymentData.paymentMethodData.tokenizationData.token);
                            payone_google_pay_finished = true;
                            jQuery('#place_order').parents('form').submit();
                        }).catch(function(err){
                            payone_google_pay_finished = false;
                            console.error(err);
                        });
                    }
                })
                .catch(function(err) {
                    payone_google_pay_finished = false;
                    console.error(err);
                });
        });

        return false;
    }
</script>