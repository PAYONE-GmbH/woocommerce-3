import {__} from '@wordpress/i18n';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

export default getPaymentMethodConfig(
    'payone_przelewy24',
    __('PAYONE Przelewy24', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-przelewy24.png`,
);
