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
    'payone_amazonpay_express',
    __('Amazon Pay', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-amazon-pay.png`,
    <AmazonPayExpressCheckout />,
    {
        gatewayId: 'payone_amazonpay_express',
        // Only show this payment method when returning from Amazon Express
        canMakePayment() {
            const {amazonPayConfig} = wc.wcSettings.getSetting('payone_data');
            return amazonPayConfig.hasExpressSession && amazonPayConfig.expressWorkorderId;
        },
    },
);
