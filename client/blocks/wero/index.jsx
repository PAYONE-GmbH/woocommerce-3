import {__} from '@wordpress/i18n';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

export default getPaymentMethodConfig(
    'payone_wero',
    __('PAYONE Wero', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-wero.png`,
);
