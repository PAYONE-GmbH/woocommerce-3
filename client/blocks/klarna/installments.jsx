import {__} from '@wordpress/i18n';
import IconLabel from '../../components/IconLabel';
import {PAYONE_ASSETS_URL} from '../../constants';
import KlarnaBase from './klarna-base';
import {KLARNA_CATEGORIES} from './KlarnaService';

const PAYMENT_METHOD_NAME = 'payone_klarna_installments';
const label = __('PAYONE Klarna Ratenkauf', 'payone-woocommerce-3');

const KlarnaInstallments = (paymentMethodProps) => {
    return <KlarnaBase
        klarnaCategory={KLARNA_CATEGORIES.PAY_OVER_TIME}
        label={label}
        {...paymentMethodProps}
    />;
};

export default {
    name: PAYMENT_METHOD_NAME,
    label: <IconLabel text={label} icon={`${PAYONE_ASSETS_URL}/icon-klarna.png`} />,
    ariaLabel: label,
    content: <KlarnaInstallments />,
    edit: <KlarnaInstallments />,
    canMakePayment: () => true,
    paymentMethodId: PAYMENT_METHOD_NAME,
    supports: {
        showSavedCards: false,
        showSaveOption: false,
    },
};
