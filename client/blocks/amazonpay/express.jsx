import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import AssetService from '../../services/AssetService';

const AmazonPayExpressButton = () => {
    const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
    const [errorMessage, setErrorMessage] = useState(null);

    useEffect(() => {
        // Load Amazon SDK and create new session for Express Button
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

    // Show Amazon Pay button or error
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
        // Only show on Cart page (before Amazon flow), NOT after return
        canMakePayment() {
            const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
            // Hide if Express session is already active (user returned from Amazon)
            if (amazonPayConfig.hasExpressSession && amazonPayConfig.expressWorkorderId) {
                return false;
            }
            return amazonPayConfig.isExpressAvailable;
        },
    },
);
