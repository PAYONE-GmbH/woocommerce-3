import {useEffect, useRef, useState} from '@wordpress/element';
import {select} from '@wordpress/data';
import {__} from '@wordpress/i18n';
import IBAN from 'iban';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import {PAYONE_ASSETS_URL} from '../../constants';

const PAYONE_TEST_IBAN = 'DE00123456782599100003';

const SepaDirectDebit = ({
    eventRegistration: {onPaymentSetup, onCheckoutValidation},
    emitResponse: {responseTypes, noticeContexts},
}) => {
    const {sepaManageMandateUrl} = wc.wcSettings.getSetting('payone_data');
    const {CART_STORE_KEY} = wc.wcBlocksData;

    const [iban, setIban] = useState('');
    const [showConfirmationCheck, setShowConfirmationCheck] = useState(false);
    const [confirmationChecked, setConfirmationChecked] = useState(true);
    const [mandateConfirmationText, setMandateConfirmationText] = useState('');
    const [mandateReference, setMandateReference] = useState('');
    // Payment data of the confirmed mandate; read by onPaymentSetup.
    const paymentMethodData = useRef(null);

    // WooCommerce waits for this observer, so the mandate is managed before the order is placed.
    // Errors must be returned as an observer response, plain `false` does not stop the checkout.
    useEffect(() => onCheckoutValidation(async () => {
        const validationError = (errorMessage) => ({
            type: responseTypes.ERROR,
            errorMessage,
            context: noticeContexts.PAYMENTS,
        });
        const confirmationRequired = () => validationError(__(
            'Du musst diese Checkbox ankreuzen, um fortfahren zu können\n',
            'payone-woocommerce-3',
        ));

        if (paymentMethodData.current?.direct_debit_iban === iban) {
            // Mandate for this IBAN already confirmed.
            return true;
        }
        paymentMethodData.current = null;

        if (showConfirmationCheck && !confirmationChecked) {
            return confirmationRequired();
        }

        const store = select(CART_STORE_KEY);
        const {billingAddress} = store.getCartData();

        const body = new FormData();
        body.append('currency', wc.wcSettings.CURRENCY.code);
        body.append('lastname', billingAddress.last_name);
        body.append('country', billingAddress.country);
        body.append('city', billingAddress.city);
        body.append('confirmation_check', showConfirmationCheck ? (confirmationChecked ? 1 : 0) : -1);
        body.append('mandate_identification', mandateReference);
        body.append('iban', iban);

        let json;
        try {
            const response = await fetch(sepaManageMandateUrl, {
                method: 'POST',
                body,
            });
            json = await response.json();
        } catch (e) {
            json = null;
        }

        const confirmMandate = (reference) => {
            paymentMethodData.current = {
                direct_debit_iban: iban,
                direct_debit_confirmation_check: confirmationChecked ? 1 : 0,
                direct_debit_reference: reference,
            };
            setMandateConfirmationText('');
            setShowConfirmationCheck(false);

            return true;
        };

        if (json?.status === 'active') {
            setMandateReference(json.reference);

            return confirmMandate(json.reference);
        }

        if (json?.status === 'pending') {
            setMandateReference(json.reference);

            // Re-submitted after the customer confirmed the mandate text.
            if (showConfirmationCheck && confirmationChecked) {
                return confirmMandate(json.reference);
            }

            setConfirmationChecked(false);
            setMandateConfirmationText(json.text);
            setShowConfirmationCheck(true);

            return confirmationRequired();
        }

        return validationError(json?.message || __(
            'Die Zahlung konnte nicht erfolgreich durchgeführt werden.',
            'payone-woocommerce-3',
        ));
    }), [onCheckoutValidation, confirmationChecked, showConfirmationCheck, mandateReference, iban]);

    useEffect(() => onPaymentSetup(() => {
        if (paymentMethodData.current) {
            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: paymentMethodData.current,
                },
            };
        }

        return {
            type: responseTypes.ERROR,
            message: __(
                'Die Zahlung konnte nicht erfolgreich durchgeführt werden.',
                'payone-woocommerce-3',
            ),
        };
    }), [onPaymentSetup]);

    return (
        <>
            <ValidatedTextInput
                id="direct_debit_iban_field"
                type="text"
                label={__('IBAN', 'payone-woocommerce-3')}
                onChange={(value) => setIban(value)}
                customValidation={(inputObject) => {
                    if (inputObject.value === PAYONE_TEST_IBAN) return true;

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

            {showConfirmationCheck ? (
                <>
                    {mandateConfirmationText ? (
                        <p
                            dangerouslySetInnerHTML={{__html: mandateConfirmationText}}
                            style={{marginTop: '1rem'}}
                        />
                    ) : null}

                    <div style={{display: 'flex', alignItems: 'center'}}>
                        <input
                            type="checkbox"
                            id="direct_debit_confirmation_check"
                            onChange={(event) => {
                                setConfirmationChecked(event.target.checked);
                            }}
                            checked={confirmationChecked}
                        />

                        <label htmlFor="direct_debit_confirmation_check">
                            {__('Ich erteile das SEPA-Lastschriftmandat', 'payone-woocommerce-3')}
                        </label>
                    </div>
                </>
            ) : null}
        </>
    );
};

export default getPaymentMethodConfig(
    'bs_payone_sepa',
    __('PAYONE Direct Debit', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-lastschrift.png`,
    <SepaDirectDebit />,
);
