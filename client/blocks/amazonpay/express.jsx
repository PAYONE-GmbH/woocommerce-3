import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import AssetService from '../../services/AssetService';

const AmazonPayExpressButton = ({
    eventRegistration,
    emitResponse,
}) => {
    const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
    const {onPaymentSetup} = eventRegistration;
    const {responseTypes} = emitResponse;
    const [workorderId, setWorkorderId] = useState(null);
    const [isReady, setIsReady] = useState(false);
    const [errorMessage, setErrorMessage] = useState(null);

    useEffect(() => {
        // Load Amazon SDK
        AssetService.loadJsScript(amazonPayConfig.sdkUrl, () => {
            // Fetch button configuration from backend for Express checkout
            fetch(amazonPayConfig.createSessionExpressUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then((res) => res.json())
                .then((config) => {
                    // Check for backend error
                    if (config.error) {
                        setErrorMessage(config.error);
                        setIsReady(false);
                        return;
                    }

                    setWorkorderId(config.workorderId);
                    setIsReady(true);

                    // Render Amazon Pay Express button
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
                    setErrorMessage(__('Failed to initialize AmazonPay Express. Please try again.', 'payone-woocommerce-3'));
                    setIsReady(false);
                });
        });
    }, []);

    useEffect(() => onPaymentSetup(() => {
        if (!isReady || !workorderId) {
            return {
                type: responseTypes.ERROR,
                message: __(
                    'AmazonPay Express is not ready. Please try again.',
                    'payone-woocommerce-3',
                ),
            };
        }

        return {
            type: responseTypes.SUCCESS,
            meta: {
                paymentMethodData: {
                    amazonpay_workorderid: workorderId,
                    amazonpay_express_used: true,
                },
            },
        };
    }), [onPaymentSetup, isReady, workorderId]);

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
    'payone_amazonpay_express',
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
