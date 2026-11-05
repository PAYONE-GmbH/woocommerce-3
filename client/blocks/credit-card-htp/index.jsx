import {__} from '@wordpress/i18n';
import {useEffect, useRef, useState} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

const GATEWAY_ID = 'payone_creditcard_htp';
const CONTAINER_ID = 'payone-htp-block-container';

let sdkPromise = null;

const loadSdk = (config) => {
    if (window.HostedTokenizationSdk) {
        return Promise.resolve();
    }
    if (!sdkPromise) {
        sdkPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = config.sdkUrl;
            if (config.sdkIntegrity) {
                script.integrity = config.sdkIntegrity;
                script.crossOrigin = 'anonymous';
            }
            script.onload = resolve;
            script.onerror = () => {
                sdkPromise = null;
                reject(new Error('SDK could not be loaded'));
            };
            document.head.appendChild(script);
        });
    }
    return sdkPromise;
};

const fetchJwt = async (url) => {
    const response = await fetch(url, {credentials: 'same-origin'});
    const data = await response.json();
    if (!data?.token) {
        throw new Error('No token');
    }
    return data.token;
};

/**
 * Tokenizes the card data in the iframe. The SDK registers a new message listener
 * on every call, so we only accept the first answer.
 */
const tokenize = () => new Promise((resolve, reject) => {
    let done = false;
    window.HostedTokenizationSdk.submitForm(
        (statusCode, token, card, inputMode) => {
            if (!done) {
                done = true;
                resolve({token, card: card || {}, inputMode: inputMode || 'manual'});
            }
        },
        (statusCode, error) => {
            if (!done) {
                done = true;
                reject(error);
            }
        },
    );
});

const PayoneCreditCardHtp = ({
    eventRegistration,
    emitResponse,
    billing,
}) => {
    const {creditCardHtpConfig: config} = wc.wcSettings.getSetting('payone_data');
    const {onPaymentSetup} = eventRegistration;
    const {responseTypes, noticeContexts} = emitResponse;

    const [ready, setReady] = useState(false);
    const [loadError, setLoadError] = useState(false);
    const renderedAmount = useRef(null);

    const amount = String(billing?.cartTotal?.value ?? '');
    const currency = billing?.currency?.code ?? '';
    const email = billing?.billingAddress?.email ?? '';

    useEffect(() => {
        if (!config) {
            setLoadError(true);
            return;
        }
        // With Click to Pay the amount is part of the form, so render again when it changes.
        if (renderedAmount.current !== null && (!config.ctp || renderedAmount.current === amount)) {
            return;
        }
        renderedAmount.current = amount;

        let cancelled = false;
        setReady(false);
        Promise.all([loadSdk(config), fetchJwt(config.jwtUrl)])
            .then(async ([, token]) => {
                await window.HostedTokenizationSdk.init();
                if (cancelled || !document.getElementById(CONTAINER_ID)) {
                    return;
                }
                const pageConfig = {
                    iframe: {iframeWrapperId: CONTAINER_ID, width: '100%'},
                    token,
                    mode: config.mode,
                    locale: config.locale,
                    allowedCardSchemes: config.allowedCardSchemes,
                    showCardholderName: config.showCardholderName,
                    isSecurityCodeMasked: true,
                };
                if (config.ctp) {
                    pageConfig.CTPConfig = {
                        ...config.ctp,
                        transactionAmount: {amount, currencyCode: currency},
                    };
                    if (email) {
                        pageConfig.email = email;
                    }
                }
                window.HostedTokenizationSdk.getPaymentPage(pageConfig, () => {});
                setReady(true);
            })
            .catch(() => {
                if (!cancelled) {
                    setLoadError(true);
                }
            });

        return () => {
            cancelled = true;
        };
    }, [amount]);

    useEffect(() => onPaymentSetup(async () => {
        if (!ready || !window.HostedTokenizationSdk) {
            return {
                type: responseTypes.ERROR,
                message: __('Das Kreditkartenformular ist noch nicht geladen.', 'payone-woocommerce-3'),
                messageContext: noticeContexts.PAYMENTS,
            };
        }

        try {
            const {token, card, inputMode} = await tokenize();

            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        card_pseudopan: token,
                        card_holder: card.cardholderName || '',
                        card_input_mode: inputMode,
                        card_type: card.cardType || '',
                    },
                },
            };
        } catch (error) {
            const detail = typeof error === 'string' ? error : (error?.error || error?.message || '');
            return {
                type: responseTypes.ERROR,
                message: __('Bitte prüfen Sie Ihre Kreditkartendaten.', 'payone-woocommerce-3')
                    + (detail ? ` (${detail})` : ''),
                messageContext: noticeContexts.PAYMENTS,
            };
        }
    }), [onPaymentSetup, ready]);

    if (loadError) {
        return (
            <p>
                {__(
                    'Das Kreditkartenformular konnte nicht geladen werden. Bitte laden Sie die Seite neu oder wählen Sie eine andere Zahlungsart.',
                    'payone-woocommerce-3',
                )}
            </p>
        );
    }

    return <div id={CONTAINER_ID} />;
};

export default getPaymentMethodConfig(
    GATEWAY_ID,
    wc.wcSettings.getSetting('payone_data')?.creditCardHtpConfig?.title || __('PAYONE Kreditkarte', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-creditcard.png`,
    <PayoneCreditCardHtp />,
);
