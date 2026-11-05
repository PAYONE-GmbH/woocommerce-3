<?php
/**
 * Classic checkout form for "Kreditkarte neu" (PAYONE Hosted Tokenization).
 *
 * @var \Payone\Gateway\CreditCardHtp $this
 * @var array $htp_config
 * @var int $amount
 * @var string $currency
 */
?>
<input type="hidden" name="card_pseudopan" id="payone_htp_token">
<input type="hidden" name="card_holder" id="payone_htp_card_holder">
<input type="hidden" name="card_input_mode" id="payone_htp_input_mode">
<input type="hidden" name="card_type" id="payone_htp_card_type">

<?php if ( $this->get_option( 'description' ) ) { ?>
    <p><?php echo nl2br( esc_html( $this->get_option( 'description' ) ) ); ?></p>
<?php } ?>
<div id="payone-htp-container"></div>
<div id="payone-htp-error"></div>

<script>
(function () {
    var config = <?php echo wp_json_encode( $htp_config ); ?>;
    var amount = <?php echo (int) $amount; ?>;
    var currency = <?php echo wp_json_encode( $currency ); ?>;
    var gatewayId = <?php echo wp_json_encode( \Payone\Gateway\CreditCardHtp::GATEWAY_ID ); ?>;

    function showError(message) {
        var el = document.getElementById('payone-htp-error');
        if (el) {
            el.innerHTML = '';
            if (message) {
                var strong = document.createElement('strong');
                strong.style.color = 'red';
                strong.textContent = message;
                el.appendChild(strong);
            }
        }
    }

    function loadSdk() {
        if (window.HostedTokenizationSdk) {
            return Promise.resolve();
        }
        if (!window.payoneHtpSdkPromise) {
            window.payoneHtpSdkPromise = new Promise(function (resolve, reject) {
                var script = document.createElement('script');
                script.src = config.sdkUrl;
                if (config.sdkIntegrity) {
                    script.integrity = config.sdkIntegrity;
                    script.crossOrigin = 'anonymous';
                }
                script.onload = resolve;
                script.onerror = function () {
                    window.payoneHtpSdkPromise = null;
                    reject(new Error('SDK could not be loaded'));
                };
                document.head.appendChild(script);
            });
        }
        return window.payoneHtpSdkPromise;
    }

    function fetchJwt() {
        return fetch(config.jwtUrl, {credentials: 'same-origin'})
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data || !data.token) {
                    throw new Error('No token');
                }
                return data.token;
            });
    }

    function render() {
        if (!document.getElementById('payone-htp-container')) {
            return;
        }
        Promise.all([loadSdk(), fetchJwt()])
            .then(function (results) {
                return window.HostedTokenizationSdk.init().then(function () { return results[1]; });
            })
            .then(function (token) {
                var pageConfig = {
                    iframe: {iframeWrapperId: 'payone-htp-container', width: '100%'},
                    token: token,
                    mode: config.mode,
                    locale: config.locale,
                    allowedCardSchemes: config.allowedCardSchemes,
                    showCardholderName: config.showCardholderName,
                    isSecurityCodeMasked: true
                };
                if (config.ctp) {
                    pageConfig.CTPConfig = Object.assign({}, config.ctp, {
                        transactionAmount: {amount: String(amount), currencyCode: currency}
                    });
                    var email = document.getElementById('billing_email');
                    if (email && email.value) {
                        pageConfig.email = email.value;
                    }
                }
                window.HostedTokenizationSdk.getPaymentPage(pageConfig, function () {});
            })
            .catch(function () {
                showError(<?php echo wp_json_encode( __( 'The credit card form could not be loaded. Please reload the page or choose another payment method.', 'payone-woocommerce-3' ) ); ?>);
            });
    }

    function onSuccess(statusCode, token, card, inputMode) {
        if (!window.payoneHtpPending) {
            return;
        }
        window.payoneHtpPending = false;
        document.getElementById('payone_htp_token').value = token;
        document.getElementById('payone_htp_card_holder').value = (card && card.cardholderName) || '';
        document.getElementById('payone_htp_input_mode').value = inputMode || 'manual';
        document.getElementById('payone_htp_card_type').value = (card && card.cardType) || '';
        payone_unblock();
        jQuery('#place_order').parents('form').submit();
    }

    function onFailure(statusCode, error) {
        if (!window.payoneHtpPending) {
            return;
        }
        window.payoneHtpPending = false;
        var message = typeof error === 'string' ? error : (error && (error.error || error.message)) || '';
        showError(<?php echo wp_json_encode( __( 'Please check your credit card details.', 'payone-woocommerce-3' ) ); ?> + (message ? ' (' + message + ')' : ''));
        payone_unblock();
    }

    window.payoneHtpPlaceOrder = function () {
        if (document.getElementById('payone_htp_token').value) {
            return true;
        }
        if (!window.HostedTokenizationSdk) {
            return false;
        }
        showError('');
        window.payoneHtpPending = true;
        window.HostedTokenizationSdk.submitForm(onSuccess, onFailure);

        return false;
    };

    if (!window.payoneHtpClickHandler) {
        window.payoneHtpClickHandler = true;
        jQuery(document).on('click', '#place_order', function () {
            var currentGateway = jQuery('input[name=payment_method]:checked').val();
            return currentGateway === gatewayId ? window.payoneHtpPlaceOrder() : true;
        });
        // A token must not be used twice: after a failed checkout the customer has to submit the form again.
        jQuery(document.body).on('checkout_error', function () {
            var tokenField = document.getElementById('payone_htp_token');
            if (tokenField) {
                tokenField.value = '';
            }
        });
    }

    render();
})();
</script>
