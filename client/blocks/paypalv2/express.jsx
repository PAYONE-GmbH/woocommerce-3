import {__} from '@wordpress/i18n';
import {useEffect} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import AssetService from '../../services/AssetService';

const icon = `${PAYONE_ASSETS_URL}/${__('checkout-paypal-en.png', 'payone-woocommerce-3')}`;

const PayPalV2Express = () => {
    const {paypalExpressConfig} = wc.wcSettings.getSetting('payone_data');

    useEffect(() => {
        AssetService.loadJsScript(paypalExpressConfig.jsUrl, () => {
            /* global paypal */
            if (typeof paypal !== 'undefined') {
                paypal.Buttons({
                    style: {
                        layout: 'vertical',
                        color: 'gold',
                        shape: 'rect',
                        label: 'paypal',
                        height: 55,
                    },
                    createOrder(data, actions) {
                        console.log('createOrder', data, actions);
                        return fetch('', {
                            method: 'post',
                        }).then((res) => {
                            return res.text();
                        }).then((orderID) => {
                            return orderID;
                        });
                    },
                    onApprove(data, actions) {
                        console.log('onApprove', data, actions);
                        window.location = '';
                    },
                }).render('#payone-paypalv2-express-button');
            }
        });
    }, []);

    return (
        <div id="payone-paypalv2-express-button"></div>
    );
};

export default getPaymentMethodConfig(
    'payone_paypalv2_express',
    __('PAYONE PayPal v2 Express', 'payone-woocommerce-3'),
    icon,
    <PayPalV2Express />,
    {
        gatewayId: 'payone_paypalv2_express',
    },
);