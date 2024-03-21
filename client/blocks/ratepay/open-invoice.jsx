import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import RatepayDisclaimer from './disclaimer';
import {PAYONE_ASSETS_URL} from '../../constants';
import IconLabel from '../../components/IconLabel';

const PAYMENT_METHOD_NAME = 'payone_ratepay_open_invoice';

const RatepayOpenInvoice = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const [birthday, setBirthday] = useState('');

    useEffect(() => {
        // TODO: Server antwortet mit Fehlercode 1000 "Parameter faulty or missing"
        // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

        return onPaymentSetup(() => {
            if (birthday) {
                return {
                    type: responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            ratepay_open_invoice_birthday: birthday,
                        },
                    },
                };
            }

            return {
                type: responseTypes.ERROR,
                message: __(
                    'Please enter a valid birthday!',
                    'payone-woocommerce-3',
                ),
            };
        });
    }, [onPaymentSetup, birthday]);

    return (
        <div>
            <ValidatedTextInput
                type="date"
                id="ratepay_open_invoice_birthday"
                className="payone-validated-date-input is-active"
                label={__('Birthday', 'payone-woocommerce-3')}
                onChange={(value) => setBirthday(value)}
                value={birthday}
                required
            />

            <RatepayDisclaimer />
        </div>
    );
};

const label = __('Ratepay Open Invoice', 'payone-woocommerce-3');

export default {
    name: PAYMENT_METHOD_NAME,
    label: <IconLabel text={label} icon={`${PAYONE_ASSETS_URL}/icon-ratepay.svg`} />,
    ariaLabel: label,
    content: <RatepayOpenInvoice />,
    edit: <RatepayOpenInvoice />,
    canMakePayment: () => true,
    paymentMethodId: PAYMENT_METHOD_NAME,
    supports: {
        showSavedCards: false,
        showSaveOption: false,
    },
};
