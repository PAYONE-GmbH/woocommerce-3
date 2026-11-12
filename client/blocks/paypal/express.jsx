/**
 * PayPal Express (v1) - Express payment method for the cart and checkout blocks.
 *
 * Starts the PayPal Express checkout and redirects the customer to PayPal. After the approval
 * PayPal sends the customer back to the checkout, where express-checkout.jsx completes the order.
 */
import {__} from '@wordpress/i18n';
import {useState} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

const PAYMENT_METHOD_NAME = 'payone_paypal_express';

const PayPalExpress = ({onClick, onClose, setExpressPaymentError}) => {
    const {paypalV1ExpressConfig} = wc.wcSettings.getSetting('payone_data');
    const [isLoading, setIsLoading] = useState(false);

    const startCheckout = (event) => {
        event.preventDefault();
        if (isLoading) {
            return;
        }

        setIsLoading(true);
        setExpressPaymentError('');
        onClick();

        fetch(paypalV1ExpressConfig.setCheckoutUrl, {method: 'POST'})
            .then((response) => response.json())
            .catch(() => null)
            .then((json) => {
                if (json?.status === 'ok' && json.url) {
                    window.location.href = json.url;
                    return;
                }

                setIsLoading(false);
                onClose();
                setExpressPaymentError(json?.message || __(
                    'Die Zahlung konnte nicht erfolgreich durchgeführt werden.',
                    'payone-woocommerce-3',
                ));
            });
    };

    return (
        <div id="payone-paypal-express-button" className="payone-paypal-express-button">
            <button
                type="button"
                title="PayPal Express"
                onClick={startCheckout}
                disabled={isLoading}
                style={{
                    width: '100%',
                    padding: 0,
                    border: 0,
                    background: 'none',
                    textAlign: 'center',
                    cursor: isLoading ? 'wait' : 'pointer',
                    opacity: isLoading ? 0.6 : 1,
                }}
            >
                <img
                    src={paypalV1ExpressConfig.buttonImage}
                    alt="PayPal Express"
                    style={{margin: 'auto'}}
                />
            </button>
        </div>
    );
};

export default getPaymentMethodConfig(
    PAYMENT_METHOD_NAME,
    __('PayPal Express', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-paypal.png`,
    <PayPalExpress />,
    {
        gatewayId: PAYMENT_METHOD_NAME,
        canMakePayment() {
            const {paypalV1ExpressConfig} = wc.wcSettings.getSetting('payone_data');
            // After the approval at PayPal the order is completed with express-checkout.jsx.
            if (paypalV1ExpressConfig.hasExpressSession) {
                return false;
            }
            return paypalV1ExpressConfig.isAvailable;
        },
    },
);
