import {__} from '@wordpress/i18n';
import {useEffect, useState, useRef} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import AssetService from '../../services/AssetService';

const AmazonPayExpressButton = ({
    onSubmit,
    eventRegistration,
    emitResponse,
}) => {
    const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
    const {onPaymentSetup} = eventRegistration;
    const {responseTypes} = emitResponse;
    const [errorMessage, setErrorMessage] = useState(null);

    // useRef for stable values in callbacks
    const workorderIdRef = useRef(amazonPayConfig.expressWorkorderId || null);

    useEffect(() => {
        // If Express session is active (returned from Amazon), don't create new session
        if (amazonPayConfig.hasExpressSession && amazonPayConfig.expressWorkorderId) {
            workorderIdRef.current = amazonPayConfig.expressWorkorderId;
            return;
        }

        // Cart page: Load Amazon SDK and create new session for Express Button
        AssetService.loadJsScript(amazonPayConfig.sdkUrl, () => {
            fetch(amazonPayConfig.createSessionExpressUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
            })
                .then((res) => res.json())
                .then((config) => {
                    if (config.error) {
                        setErrorMessage(config.error);
                        return;
                    }

                    workorderIdRef.current = config.workorderId;

                    /* global amazon */
                    if (typeof amazon !== 'undefined' && amazon.Pay) {
                        amazon.Pay.renderButton('#payone-amazonpay-express-button', {
                            merchantId: config.merchantId,
                            publicKeyId: config.publicKeyId,
                            ledgerCurrency: config.ledgerCurrency,
                            checkoutLanguage: config.checkoutLanguage,
                            productType: config.productType,
                            placement: 'Cart',
                            buttonColor: config.buttonColor,
                            sandbox: config.sandbox,
                            estimatedOrderAmount: config.estimatedOrderAmount,
                            createCheckoutSessionConfig: config.createCheckoutSessionConfig,
                        });
                    }
                })
                .catch((error) => {
                    console.error('AmazonPay Express button config error:', error);
                    setErrorMessage(__('Failed to initialize AmazonPay Express.', 'payone-woocommerce-3'));
                });
        });
    }, []);

    // Payment setup callback - called when checkout is submitted via Express button
    useEffect(() => {
        const unsubscribe = onPaymentSetup(() => {
            if (!workorderIdRef.current) {
                return {
                    type: responseTypes.ERROR,
                    message: __('AmazonPay Express Session nicht gefunden.', 'payone-woocommerce-3'),
                };
            }

            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        amazonpay_workorderid: workorderIdRef.current,
                        amazonpay_express_used: true,
                    },
                },
            };
        });

        return unsubscribe;
    }, [onPaymentSetup, responseTypes]);

    // Checkout page after Amazon return: Show confirmation + button
    if (amazonPayConfig.hasExpressSession && amazonPayConfig.expressWorkorderId) {
        return (
            <div className="payone-amazonpay-express-container payone-amazonpay-ready wc-block-checkout__actions_row">
                <p style={{marginBottom: '15px', color: '#067D62', fontWeight: 'bold'}}>
                    {__('Sie haben Amazon Pay Express gewählt.', 'payone-woocommerce-3')}
                </p>
                <button
                    type="button"
                    onClick={() => onSubmit && onSubmit()}
                    className={`wc-block-components-button wp-element-button wc-block-components-checkout-place-order-button contained`}
                >
                    <div className={`wc-block-components-checkout-place-order-button__text`}>{__('Bestellung abschließen', 'payone-woocommerce-3')}</div>
                </button>
            </div>
        );
    }

    // Cart page: Show Amazon Pay button or error
    return (
        <div className="payone-amazonpay-express-container">
            {errorMessage && (
                <div id="amazonpay_express_error" style={{color: 'red', marginTop: '10px'}}>
                    {errorMessage}
                </div>
            )}
            {!errorMessage && <div id="payone-amazonpay-express-button"></div>}
        </div>
    );
};

export default getPaymentMethodConfig(
    'payone_amazonpay_express_button',
    __('PAYONE Amazon Pay Express', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-amazon-pay.png`,
    <AmazonPayExpressButton />,
    {
        gatewayId: 'payone_amazonpay_express',
        canMakePayment() {
            const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
            return amazonPayConfig.isExpressAvailable;
        },
    },
);
