import {__} from '@wordpress/i18n';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

export default getPaymentMethodConfig(
    'bs_payone_paypal',
    __('PAYONE PayPal', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-paypal.png`,
);
