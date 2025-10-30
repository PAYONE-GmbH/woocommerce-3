import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {select} from '@wordpress/data';
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
    const [workorderId, setWorkorderId] = useState(null);
    const [isReady, setIsReady] = useState(false);
    const [errorMessage, setErrorMessage] = useState(null);

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
                    // Check for backend error
                    if (config.error) {
                        setErrorMessage(config.error);
                        setIsReady(false);
                        return;
                    }

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
                    setErrorMessage(__('Failed to initialize AmazonPay. Please try again.', 'payone-woocommerce-3'));
                    setIsReady(false);
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

        // Validate that a phone number is provided for Amazon Pay
        const {CART_STORE_KEY} = wc.wcBlocksData;
        const store = select(CART_STORE_KEY);
        const {shippingAddress} = store.getCartData();

        if (!shippingAddress.phone || shippingAddress.phone.trim() === '') {
            return {
                type: responseTypes.ERROR,
                message: __(
                    'A phone number is required for Amazon Pay. Please add a phone number to your shipping address.',
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
            {errorMessage && (
                <div id="amazonpay_error" style={{color: 'red', marginTop: '10px'}}>
                    {errorMessage}
                </div>
            )}
            {!errorMessage && <div id="payone-amazonpay-button"></div>}
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
