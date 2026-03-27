import {__} from '@wordpress/i18n';
import {useEffect, useRef, useState} from '@wordpress/element';
import {select} from '@wordpress/data';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import AssetService from '../../services/AssetService';

const PayoneGooglePay = ({
    eventRegistration,
    emitResponse,
    onSubmit,
}) => {
    const {googlePayConfig} = wc.wcSettings.getSetting('payone_data');
    const {onPaymentSetup, onCheckoutValidation} = eventRegistration;
    const {responseTypes} = emitResponse;

    const [googlePayFinished, setGooglePayFinished] = useState(false);
    const [googlePayToken, setGooglePayToken] = useState(null);
    const [errorMessage, setErrorMessage] = useState(null);
    const [sdkReady, setSdkReady] = useState(typeof google !== 'undefined');

    const googlePayFinishedRef = useRef(false);
    const googlePayTokenRef = useRef(null);

    // Load Google Pay SDK via AssetService
    useEffect(() => {
        if (sdkReady) {
            return;
        }
        AssetService.loadJsScript(googlePayConfig.sdkUrl, () => {
            setSdkReady(true);
        });
    }, []);

    // Intercept checkout validation — open Google Pay popup
    useEffect(() => onCheckoutValidation(async () => {
        if (googlePayFinishedRef.current) {
            return true;
        }

        if (!sdkReady) {
            setErrorMessage(__('Google Pay wird geladen, bitte versuchen Sie es erneut.', 'payone-woocommerce-3'));
            return false;
        }

        const baseRequest = {apiVersion: 2, apiVersionMinor: 0};
        const tokenizationSpecification = {
            type: 'PAYMENT_GATEWAY',
            parameters: {
                gateway: 'payonegmbh',
                gatewayMerchantId: googlePayConfig.gatewayMerchantId,
            },
        };
        const baseCardPaymentMethod = {
            type: 'CARD',
            parameters: {
                allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                allowedCardNetworks: ['MASTERCARD', 'VISA'],
                allowPrepaidCards: true,
                allowCreditCards: true,
            },
        };
        const cardPaymentMethod = {
            ...baseCardPaymentMethod,
            tokenizationSpecification,
        };

        // Get cart totals from WooCommerce Blocks store
        const {CART_STORE_KEY} = wc.wcBlocksData;
        const store = select(CART_STORE_KEY);
        const cartTotals = store.getCartTotals();
        const cartData = store.getCartData();

        // total_price is in minor units as string (e.g. "1999" for 19.99 EUR)
        const totalPrice = (parseInt(cartTotals.total_price, 10) / 100).toFixed(2);
        const currencyCode = cartTotals.currency_code;
        const countryCode = cartData.billingAddress.country;

        const paymentsClient = new google.payments.api.PaymentsClient({
            environment: googlePayConfig.environment,
        });

        try {
            const readyResponse = await paymentsClient.isReadyToPay({
                ...baseRequest,
                allowedPaymentMethods: [baseCardPaymentMethod],
            });

            if (readyResponse.result) {
                const paymentData = await paymentsClient.loadPaymentData({
                    ...baseRequest,
                    allowedPaymentMethods: [cardPaymentMethod],
                    transactionInfo: {
                        totalPriceStatus: 'FINAL',
                        totalPrice,
                        currencyCode,
                        countryCode,
                    },
                });

                const token = btoa(paymentData.paymentMethodData.tokenizationData.token);
                googlePayTokenRef.current = token;
                googlePayFinishedRef.current = true;
                setGooglePayToken(token);
                setGooglePayFinished(true);
            }
        } catch (err) {
            googlePayFinishedRef.current = false;
            setGooglePayFinished(false);

            // User cancelled the popup — silently reset, no error message
            if (err.statusCode !== 'CANCELED') {
                setErrorMessage(
                    err.message
                    || __('Google Pay konnte nicht durchgeführt werden.', 'payone-woocommerce-3'),
                );
                console.error('Google Pay error:', err);
            }
        }

        return false;
    }), [onCheckoutValidation, googlePayFinished, sdkReady]);

    // Register onPaymentSetup and trigger re-submit when token arrives (Klarna pattern)
    useEffect(() => {
        const unsubscribe = onPaymentSetup(() => {
            if (errorMessage) {
                return {
                    type: responseTypes.ERROR,
                    message: errorMessage,
                };
            }

            if (googlePayFinishedRef.current && googlePayTokenRef.current) {
                return {
                    type: responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            payone_googlepay_token: googlePayTokenRef.current,
                        },
                    },
                };
            }

            return {
                type: responseTypes.ERROR,
                message: __(
                    'Die Zahlung konnte nicht erfolgreich durchgeführt werden.',
                    'payone-woocommerce-3',
                ),
            };
        });

        if (googlePayFinished && googlePayToken) {
            onSubmit();
        }

        return unsubscribe;
    }, [onPaymentSetup, googlePayFinished, googlePayToken]);

    return null;
};

export default getPaymentMethodConfig(
    'payone_googlepay',
    __('PAYONE Google Pay', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-googlepay.png`,
    <PayoneGooglePay />,
);
