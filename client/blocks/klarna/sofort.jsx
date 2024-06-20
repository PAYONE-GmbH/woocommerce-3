import {__} from '@wordpress/i18n';
import IconLabel from '../../components/IconLabel';
import {PAYONE_ASSETS_URL} from '../../constants';
import KlarnaBase from './klarna-base';
import {KLARNA_CATEGORIES} from './KlarnaService';

const PAYMENT_METHOD_NAME = 'payone_klarna_sofort';
const label = __('PAYONE Klarna Sofort bezahlen', 'payone-woocommerce-3');

const KlarnaInvoice = (paymentMethodProps) => {
    return <KlarnaBase
        klarnaCategory={KLARNA_CATEGORIES.DIRECT_DEBIT}
        label={label}
        {...paymentMethodProps}
    />;
};

export default {
    name: PAYMENT_METHOD_NAME,
    label: <IconLabel text={label} icon={`${PAYONE_ASSETS_URL}/icon-klarna.png`} />,
    ariaLabel: label,
    content: <KlarnaInvoice />,
    edit: <KlarnaInvoice />,
    canMakePayment: () => true,
    paymentMethodId: PAYMENT_METHOD_NAME,
    supports: {
        showSavedCards: false,
        showSaveOption: false,
    },
};
