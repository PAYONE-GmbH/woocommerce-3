import {__} from '@wordpress/i18n';
import {useEffect, useState, useRef} from '@wordpress/element';
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

    // Use refs instead of direct DOM access
    const buttonRef = useRef(null);
    const wrapperRef = useRef(null);
    const amazonButtonInstance = useRef(null);

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

                    // Render Amazon Pay button using DECOUPLED pattern
                    /* global amazon */
                    if (typeof amazon !== 'undefined' && amazon.Pay) {
                        // Store button instance in ref for later programmatic access
                        amazonButtonInstance.current = amazon.Pay.renderButton(
                            '#payone-amazonpay-button',
                            {
                                merchantId: config.merchantId,
                                publicKeyId: config.publicKeyId,
                                ledgerCurrency: config.ledgerCurrency,
                                checkoutLanguage: config.checkoutLanguage,
                                productType: config.productType,
                                placement: config.placement,
                                buttonColor: config.buttonColor,
                                sandbox: config.sandbox,
                            },
                        );

                        // Set up decoupled click handler
                        amazonButtonInstance.current.onClick(() => {
                            // Initiate Amazon Pay checkout when button is clicked
                            amazonButtonInstance.current.initCheckout({
                                createCheckoutSessionConfig: config.createCheckoutSessionConfig,
                            });
                        });

                        setIsReady(true);
                    }
                })
                .catch((error) => {
                    console.error('AmazonPay button config error:', error);
                    setErrorMessage(__('Failed to initialize AmazonPay. Please try again.', 'payone-woocommerce-3'));
                    setIsReady(false);
                });
        });
    }, []);

    // Post-render style enforcement to prevent Amazon SDK from causing layout shift
    useEffect(() => {
        if (isReady && wrapperRef.current && buttonRef.current) {
            wrapperRef.current.style.width = '0';
            wrapperRef.current.style.height = '0';
            wrapperRef.current.style.overflow = 'hidden';
            buttonRef.current.style.position = 'absolute';
        }
    }, [isReady]);

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

        // Programmatically trigger Amazon Pay button click via ref
        if (buttonRef.current) {
            buttonRef.current.click();
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
            {!errorMessage && (
                <div
                    ref={wrapperRef}
                    style={{
                        // CSS Containment wrapper to isolate layout impact
                        position: 'relative',
                        width: 0,
                        height: 0,
                        overflow: 'hidden',
                        contain: 'layout style',
                    }}
                >
                    <div
                        id="payone-amazonpay-button"
                        ref={buttonRef}
                        style={{
                            // Hide button like file input (not display:none)
                            // This preserves button functionality while making it invisible
                            position: 'absolute',
                            clipPath: 'polygon(0 0, 0 0, 0 0, 0 0)',
                            pointerEvents: 'none', // Prevent accidental user clicks
                            opacity: 0,
                        }}
                    />
                </div>
            )}
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
