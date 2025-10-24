import IBAN from 'iban';
import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import RatepayDisclaimer from './disclaimer';
import {PAYONE_ASSETS_URL} from '../../constants';
import {createShippingAddressValidator} from '../../services/shippingAddressValidator';

const PAYMENT_METHOD_NAME = 'payone_ratepay_direct_debit';
const canMakePayment = createShippingAddressValidator('ratepayConfig');

const RatepayDirectDebit = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const [birthday, setBirthday] = useState('');
    const [iban, setIban] = useState('');

    useEffect(() => {
        // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

        const onSubmit = () => {
            if (birthday && iban) {
                return {
                    type: responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            ratepay_direct_debit_birthday: birthday,
                            ratepay_direct_debit_iban: iban,
                        },
                    },
                };
            }

            return {
                type: responseTypes.ERROR,
                message: __(
                    'Please enter a valid IBAN and birthday!',
                    'payone-woocommerce-3',
                ),
            };
        };

        const unsubscribeProcessing = onPaymentSetup(onSubmit);
        return () => {
            unsubscribeProcessing();
        };
    }, [onPaymentSetup, birthday, iban]);

    return (
        <div>
            <ValidatedTextInput
                type="date"
                className="payone-validated-date-input is-active"
                id="ratepay_direct_debit_birthday"
                label={__('Birthday', 'payone-woocommerce-3')}
                onChange={(value) => setBirthday(value)}
                value={birthday}
                required
            />

            <ValidatedTextInput
                id="ratepay_direct_debit_iban"
                type="text"
                label={__('IBAN', 'payone-woocommerce-3')}
                onChange={(value) => setIban(value)}
                customValidation={(inputObject) => {
                    if (!IBAN.isValid(inputObject.value)) {
                        inputObject.setCustomValidity(
                            __(
                                'Please enter a valid IBAN!',
                                'payone-woocommerce-3',
                            ),
                        );
                        return false;
                    }

                    return true;
                }}
                value={iban}
                required
            />

            <img src={`${PAYONE_ASSETS_URL}/icon-ratepay.svg`} alt="Ratepay" width="200" />

            <RatepayDisclaimer />
        </div>
    );
};

const Label = ({components}) => {
    const {PaymentMethodLabel} = components;
    return <PaymentMethodLabel text={__('Ratepay Direct Debit', 'payone-woocommerce-3')} />;
};

export default {
    name: PAYMENT_METHOD_NAME,
    label: <Label />,
    ariaLabel: __('Ratepay Direct Debit Zahlmethode', 'payone-woocommerce-3'),
    content: <RatepayDirectDebit />,
    edit: <RatepayDirectDebit />,
    canMakePayment: () => canMakePayment(PAYMENT_METHOD_NAME),
    paymentMethodId: PAYMENT_METHOD_NAME,
    supports: {
        showSavedCards: false,
        showSaveOption: false,
    },
};
