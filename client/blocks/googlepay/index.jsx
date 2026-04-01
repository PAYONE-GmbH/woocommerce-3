import {__} from '@wordpress/i18n';
import {useEffect, useRef, useState} from '@wordpress/element';
import {select} from '@wordpress/data';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import AssetService from '../../services/AssetService';

const PLACE_ORDER_BUTTON_SELECTOR = '.wc-block-components-checkout-place-order-button';

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
    const buttonContainerRef = useRef(null);

    // Load Google Pay SDK via AssetService
    useEffect(() => {
        if (sdkReady) {
            return;
        }
        AssetService.loadJsScript(googlePayConfig.sdkUrl, () => {
            setSdkReady(true);
        });
    }, []);

    // Render Google Pay button once SDK is ready
    useEffect(() => {
        if (!sdkReady || !buttonContainerRef.current) {
            return;
        }

        const baseCardPaymentMethod = {
            type: 'CARD',
            parameters: {
                allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                allowedCardNetworks: ['MASTERCARD', 'VISA'],
                allowPrepaidCards: true,
                allowCreditCards: true,
            },
        };

        const paymentsClient = new google.payments.api.PaymentsClient({
            environment: googlePayConfig.environment,
        });

        paymentsClient.isReadyToPay({
            apiVersion: 2,
            apiVersionMinor: 0,
            allowedPaymentMethods: [baseCardPaymentMethod],
        }).then((response) => {
            if (response.result) {
                const button = paymentsClient.createButton({
                    onClick: handleGooglePayClick,
                    buttonColor: 'black',
                    buttonType: 'buy',
                    buttonSizeMode: 'fill',
                    buttonLocale: 'de',
                    allowedPaymentMethods: [baseCardPaymentMethod],
                });
                buttonContainerRef.current.innerHTML = '';
                buttonContainerRef.current.appendChild(button);
                togglePlaceOrderButton(false);
            }
        }).catch((err) => {
            console.error('Google Pay isReadyToPay error:', err);
        });

        return () => {
            togglePlaceOrderButton(true);
        };
    }, [sdkReady]);

    const togglePlaceOrderButton = (show) => {
        const placeOrderButton = document.querySelector(PLACE_ORDER_BUTTON_SELECTOR);
        if (placeOrderButton) {
            placeOrderButton.style.display = show ? '' : 'none';
        }
    };

    const handleGooglePayClick = () => {
        const baseRequest = {apiVersion: 2, apiVersionMinor: 0};
        const tokenizationSpecification = {
            type: 'PAYMENT_GATEWAY',
            parameters: {
                gateway: googlePayConfig.merchantName,
                gatewayMerchantId: googlePayConfig.googlePayMerchantId,
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

        const {CART_STORE_KEY} = wc.wcBlocksData;
        const store = select(CART_STORE_KEY);
        const cartTotals = store.getCartTotals();
        const cartData = store.getCartData();

        const totalPrice = (parseInt(cartTotals.total_price, 10) / 100).toFixed(2);
        const currencyCode = cartTotals.currency_code;
        const countryCode = cartData.billingAddress.country;

        const paymentsClient = new google.payments.api.PaymentsClient({
            environment: googlePayConfig.environment,
        });

        paymentsClient.loadPaymentData({
            ...baseRequest,
            allowedPaymentMethods: [cardPaymentMethod],
            transactionInfo: {
                totalPriceStatus: 'FINAL',
                totalPrice,
                currencyCode,
                countryCode,
            },
        }).then((paymentData) => {
            const token = btoa(paymentData.paymentMethodData.tokenizationData.token);
            googlePayTokenRef.current = token;
            googlePayFinishedRef.current = true;
            setGooglePayToken(token);
            setGooglePayFinished(true);
        }).catch((err) => {
            googlePayFinishedRef.current = false;
            setGooglePayFinished(false);
            if (err.statusCode !== 'CANCELED') {
                setErrorMessage(
                    err.message
                    || __('Google Pay konnte nicht durchgeführt werden.', 'payone-woocommerce-3'),
                );
                console.error('Google Pay error:', err);
            }
        });
    };

    // Validate checkout — only allow submit when token is present
    useEffect(() => onCheckoutValidation(() => {
        return !!(googlePayFinishedRef.current && googlePayTokenRef.current);
    }), [onCheckoutValidation]);

    // Provide token on payment setup and trigger re-submit
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

    return <div ref={buttonContainerRef} />;
};

export default getPaymentMethodConfig(
    'payone_googlepay',
    __('PAYONE Google Pay', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-googlepay.png`,
    <PayoneGooglePay />,
);
