/**
 * AmazonPay Express Checkout - Regular Payment Method
 *
 * This component is registered as a REGULAR payment method (not express)
 * and only appears when returning from Amazon Pay Express flow.
 * It allows the user to complete the order after authenticating with Amazon.
 */
import {__} from '@wordpress/i18n';
import {useEffect, useRef} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

const PAYMENT_METHOD_NAME = 'payone_amazonpay_express';

// Flag to prevent multiple auto-selection attempts
let hasTriedAutoSelect = false;

/**
 * Auto-select Amazon Pay Express when returning from Amazon.
 * This is called from canMakePayment() which runs when payment methods are loaded.
 */
const autoSelectPaymentMethod = () => {
    if (hasTriedAutoSelect) {
        return;
    }
    hasTriedAutoSelect = true;

    try {
        const {dispatch, select, subscribe} = wp.data;

        // Subscribe to store changes
        const unsubscribe = subscribe(() => {
            try {
                const paymentStore = select('wc/store/payment');
                const availableMethods = paymentStore?.getAvailablePaymentMethods?.();

                // Wait until payment methods are loaded
                if (!availableMethods || Object.keys(availableMethods).length === 0) {
                    return;
                }

                // Check if our method is available
                if (availableMethods[PAYMENT_METHOD_NAME]) {
                    const dispatchStore = dispatch('wc/store/payment');
                    if (dispatchStore?.__internalSetActivePaymentMethod) {
                        dispatchStore.__internalSetActivePaymentMethod(PAYMENT_METHOD_NAME);
                        unsubscribe();
                    }
                }
            } catch (e) {
                // Silently ignore
            }
        });
    } catch (e) {
        // wp.data not available
    }
};

const AmazonPayExpressCheckout = ({
    eventRegistration,
    emitResponse,
}) => {
    const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
    const {onPaymentSetup} = eventRegistration;
    const {responseTypes} = emitResponse;

    // useRef for stable values in callbacks
    const workorderIdRef = useRef(amazonPayConfig.expressWorkorderId);

    // Payment setup callback - called when user clicks "Place Order"
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

    return (
        <div className="payone-amazonpay-express-checkout">
            <p>
                {__('Sie haben sich erfolgreich mit Amazon Pay authentifiziert. Klicken Sie auf "Bestellung aufgeben" um fortzufahren.', 'payone-woocommerce-3')}
            </p>
        </div>
    );
};

export default getPaymentMethodConfig(
    PAYMENT_METHOD_NAME,
    __('Amazon Pay', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-amazon-pay.png`,
    <AmazonPayExpressCheckout />,
    {
        gatewayId: PAYMENT_METHOD_NAME,
        // Only show this payment method when returning from Amazon Express
        canMakePayment() {
            const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
            const shouldShow = amazonPayConfig.hasExpressSession && amazonPayConfig.expressWorkorderId;

            // Auto-select this payment method when Express Session is active
            if (shouldShow) {
                autoSelectPaymentMethod();
            }

            return shouldShow;
        },
    },
);
