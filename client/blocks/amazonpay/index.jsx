import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import AssetService from '../../services/AssetService';

const AmazonPayButton = ({
    eventRegistration,
    emitResponse,
}) => {
    const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
    const {onPaymentSetup} = eventRegistration;
    const {responseTypes} = emitResponse;
    const [buttonConfig, setButtonConfig] = useState(null);
    const [workorderId, setWorkorderId] = useState(null);
    const [isReady, setIsReady] = useState(false);

    useEffect(() => {
        // Load Amazon SDK
        AssetService.loadJsScript(amazonPayConfig.sdkUrl, () => {
            // Fetch button configuration from backend
            fetch(amazonPayConfig.createSessionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then((res) => res.json())
                .then((config) => {
                    setButtonConfig(config);
                    setWorkorderId(config.workorderId);
                    setIsReady(true);

                    // Render Amazon Pay button
                    /* global amazon */
                    if (typeof amazon !== 'undefined' && amazon.Pay) {
                        amazon.Pay.renderButton('#payone-amazonpay-button', {
                            merchantId: config.merchantId,
                            publicKeyId: config.publicKeyId,
                            ledgerCurrency: config.ledgerCurrency,
                            checkoutLanguage: config.checkoutLanguage,
                            productType: config.productType,
                            placement: config.placement,
                            buttonColor: config.buttonColor,
                            sandbox: config.sandbox,
                            createCheckoutSessionConfig: config.createCheckoutSessionConfig,
                        });
                    }
                })
                .catch((error) => {
                    console.error('AmazonPay button config error:', error);
                });
        });
    }, []);

    useEffect(() => onPaymentSetup(() => {
        if (!isReady || !workorderId) {
            return {
                type: responseTypes.ERROR,
                message: __(
                    'AmazonPay is not ready. Please try again.',
                    'payone-woocommerce-3',
                ),
            };
        }

        return {
            type: responseTypes.SUCCESS,
            meta: {
                paymentMethodData: {
                    amazonpay_workorderid: workorderId,
                },
            },
        };
    }), [onPaymentSetup, isReady, workorderId]);

    return (
        <div className="payone-amazonpay-container">
            <p>{amazonPayConfig.description || ''}</p>
            <div id="payone-amazonpay-button"></div>
            <div id="amazonpay_error" style={{color: 'red', marginTop: '10px'}}></div>
        </div>
    );
};

export default getPaymentMethodConfig(
    'payone_amazonpay',
    __('PAYONE Amazon Pay', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-amazon-pay.png`,
    <AmazonPayButton />,
    {
        canMakePayment() {
            const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
            return amazonPayConfig.isAvailable;
        },
    },
);
