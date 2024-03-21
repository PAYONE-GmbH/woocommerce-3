import {useEffect, useState} from '@wordpress/element';
import {select} from '@wordpress/data';
import {__} from '@wordpress/i18n';
import IBAN from 'iban';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import {PAYONE_ASSETS_URL} from '../../constants';

const PAYONE_TEST_IBAN = 'DE00123456782599100003';

const SepaDirectDebit = ({
    eventRegistration: {onPaymentSetup, onCheckoutValidation},
    emitResponse: {responseTypes},
    onSubmit,
}) => {
    const {sepaManageMandateUrl} = wc.wcSettings.getSetting('payone_data');
    const {CART_STORE_KEY} = wc.wcBlocksData;

    const [iban, setIban] = useState('');
    const [mandateCheckSucceeded, setMandateCheckSucceeded] = useState(false);
    const [showConfirmationCheck, setShowConfirmationCheck] = useState(false);
    const [confirmationChecked, setConfirmationChecked] = useState(true);
    const [mandateConfirmationText, setMandateConfirmationText] = useState('');
    const [mandateReference, setMandateReference] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    useEffect(() => onCheckoutValidation(async () => {
        if (mandateCheckSucceeded) {
            // Skip the test, as it already succeeded.
            return true;
        }

        if (errorMessage) {
            setErrorMessage('');
            return false;
        }

        const store = select(CART_STORE_KEY);
        const {billingAddress} = store.getCartData();

        const body = new FormData();
        body.append('currency', wc.wcSettings.CURRENCY.code);
        body.append('lastname', billingAddress.last_name);
        body.append('country', billingAddress.country);
        body.append('city', billingAddress.city);
        body.append('confirmation_check', confirmationChecked ? 1 : 0);
        body.append('mandate_identification', mandateReference);
        body.append('iban', iban);

        if (!showConfirmationCheck) {
            body.set('confirmation_check', -1);
        }

        setMandateCheckSucceeded(false);

        fetch(sepaManageMandateUrl, {
            method: 'POST',
            body,
        })
            .then((response) => response.json())
            .then((json) => {
                if (json.status === 'error') {
                    setErrorMessage(json.message);
                } else if (json.status === 'active') {
                    setMandateReference(json.reference);
                    setMandateCheckSucceeded(true);
                    setMandateConfirmationText('');
                    setShowConfirmationCheck(false);

                    // Re-Trigger payment processing
                    onSubmit();
                } else if (json.status === 'pending') {
                    setMandateReference(json.reference);

                    // If has re-submitted after confirmation
                    if (showConfirmationCheck && confirmationChecked) {
                        setMandateCheckSucceeded(true);
                        setMandateConfirmationText('');
                        setShowConfirmationCheck(false);
                    } else {
                        setConfirmationChecked(false);
                        setMandateConfirmationText(json.text);
                        setShowConfirmationCheck(true);
                    }
                }
            });

        // Prevent automatical submit
        return false;
    }), [onCheckoutValidation, mandateCheckSucceeded, confirmationChecked, mandateReference, iban, errorMessage]);

    useEffect(() => {
        return onPaymentSetup(() => {
            if (errorMessage) {
                return {
                    type: responseTypes.ERROR,
                    message: errorMessage,
                };
            }

            if (showConfirmationCheck && !confirmationChecked) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Du musst diese Checkbox ankreuzen, um fortfahren zu können\n',
                        'payone-woocommerce-3',
                    ),
                };
            }

            if (mandateReference) {
                return {
                    type: responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            direct_debit_iban: iban,
                            direct_debit_confirmation_check: confirmationChecked ? 1 : 0,
                            direct_debit_reference: mandateReference,
                        },
                    },
                };
            }

            return {
                type: responseTypes.ERROR,
            };
        });
    }, [
        onPaymentSetup,
        mandateCheckSucceeded,
        errorMessage,
        iban,
        mandateReference,
        confirmationChecked,
        showConfirmationCheck,
    ]);

    return (
        <>
            {errorMessage ? <strong style={{color: 'red'}}>{errorMessage}</strong> : null}

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
