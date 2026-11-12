/**
 * PayPal Express (v1) - Regular payment method in the checkout block.
 *
 * Only shown when the customer returns from PayPal Express. The PAYONE workorder id is stored in
 * the session, so placing the order completes the payment without further input.
 */
import {__} from '@wordpress/i18n';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import autoSelectPaymentMethod from '../../services/autoSelectPaymentMethod';

const PAYMENT_METHOD_NAME = 'payone_paypal_express';

const PayPalExpressCheckout = () => (
    <div className="payone-paypal-express-checkout">
        <p>
            {__('Sie haben sich erfolgreich mit PayPal authentifiziert. Klicken Sie auf "Bestellung aufgeben" um fortzufahren.', 'payone-woocommerce-3')}
        </p>
    </div>
);

export default getPaymentMethodConfig(
    PAYMENT_METHOD_NAME,
    __('PayPal Express', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-paypal.png`,
    <PayPalExpressCheckout />,
    {
        gatewayId: PAYMENT_METHOD_NAME,
        canMakePayment() {
            const {paypalV1ExpressConfig} = wc.wcSettings.getSetting('payone_data');
            const shouldShow = paypalV1ExpressConfig.hasExpressSession && paypalV1ExpressConfig.isAvailable;

            if (shouldShow) {
                autoSelectPaymentMethod(PAYMENT_METHOD_NAME);
            }

            return shouldShow;
        },
    },
);
