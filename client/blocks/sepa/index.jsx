import {useEffect, useRef, useState} from '@wordpress/element';
import {select} from '@wordpress/data';
import {__} from '@wordpress/i18n';
import IBAN from 'iban';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';
import {PAYONE_ASSETS_URL} from '../../constants';

const PAYONE_TEST_IBAN = 'DE00123456782599100003';

const isValidIban = (value) => value === PAYONE_TEST_IBAN || IBAN.isValid(value);

const SepaDirectDebit = ({
    eventRegistration: {onPaymentSetup, onCheckoutValidation},
    emitResponse: {responseTypes, noticeContexts},
}) => {
    const {sepaManageMandateUrl} = wc.wcSettings.getSetting('payone_data');
    const {CART_STORE_KEY} = wc.wcBlocksData;

    const [iban, setIban] = useState('');
    // Result of the last mandate request: {key, status, reference, text, message}.
    const [mandate, setMandate] = useState(null);
    const [confirmationChecked, setConfirmationChecked] = useState(false);
    const mandateRequest = useRef(null);
    const confirmationCheckedRef = useRef(false);
    // Payment data for onPaymentSetup, set by the validation observer.
    const paymentMethodData = useRef(null);

    const getBillingAddress = () => select(CART_STORE_KEY).getCartData().billingAddress;

    const getMandateKey = (ibanValue, billingAddress) => [
        ibanValue,
        billingAddress.last_name,
        billingAddress.country,
        billingAddress.city,
    ].join('|');

    /**
     * Requests the mandate for the current IBAN and billing address once and caches the result,
     * so the mandate text can be shown before the customer places the order.
     */
    const loadMandate = (ibanValue) => {
        const billingAddress = getBillingAddress();
        const key = getMandateKey(ibanValue, billingAddress);

        if (mandateRequest.current?.key === key) {
            return mandateRequest.current.promise;
        }

        const body = new FormData();
        body.append('currency', wc.wcSettings.CURRENCY.code);
        body.append('lastname', billingAddress.last_name);
        body.append('country', billingAddress.country);
        body.append('city', billingAddress.city);
        body.append('confirmation_check', -1);
        body.append('mandate_identification', '');
        body.append('iban', ibanValue);

        const promise = fetch(sepaManageMandateUrl, {method: 'POST', body})
            .then((response) => response.json())
            .catch(() => null)
            .then((json) => {
                const result = {
                    key,
                    status: json?.status || 'error',
                    reference: json?.reference || '',
                    text: json?.text || '',
                    message: json?.message || '',
                };

                if (result.status === 'error') {
                    // Try again on the next request.
                    mandateRequest.current = null;
                }
                if (key === getMandateKey(ibanValue, getBillingAddress())) {
                    setMandate(result);
                    if (result.status === 'pending') {
                        confirmationCheckedRef.current = false;
                        setConfirmationChecked(false);
                    }
                }

                return result;
            });

        mandateRequest.current = {key, promise};

        return promise;
    };

    // Load the mandate as soon as a valid IBAN was entered.
    useEffect(() => {
        paymentMethodData.current = null;
        if (!isValidIban(iban)) {
            setMandate(null);
            return undefined;
        }

        const timeout = setTimeout(() => loadMandate(iban), 400);

        return () => clearTimeout(timeout);
    }, [iban]);

    // WooCommerce waits for this observer, so the mandate is checked before the order is placed.
    // Errors must be returned as an observer response, plain `false` does not stop the checkout.
    useEffect(() => onCheckoutValidation(async () => {
        const validationError = (errorMessage) => ({
            type: responseTypes.ERROR,
            errorMessage,
            context: noticeContexts.PAYMENTS,
        });

        paymentMethodData.current = null;

        if (!isValidIban(iban)) {
            return validationError(__('Please enter a valid IBAN!', 'payone-woocommerce-3'));
        }

        const result = await loadMandate(iban);

        if (result.status === 'pending' && !confirmationCheckedRef.current) {
            return validationError(__(
                'Bitte bestätige das SEPA-Lastschriftmandat.',
                'payone-woocommerce-3',
            ));
        }

        if (result.status !== 'active' && result.status !== 'pending') {
            return validationError(result.message || __(
                'Die Zahlung konnte nicht erfolgreich durchgeführt werden.',
                'payone-woocommerce-3',
            ));
        }

        paymentMethodData.current = {
            direct_debit_iban: iban,
            direct_debit_confirmation_check: 1,
            direct_debit_reference: result.reference,
        };

        return true;
    }), [onCheckoutValidation, iban]);

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

    const currentMandate = mandate && mandate.key.startsWith(`${iban}|`) ? mandate : null;

    return (
        <>
            <ValidatedTextInput
                id="direct_debit_iban_field"
                type="text"
                label={__('IBAN', 'payone-woocommerce-3')}
                onChange={(value) => setIban(value.replace(/\s+/g, '').toUpperCase())}
                customValidation={(inputObject) => {
                    if (isValidIban(inputObject.value.replace(/\s+/g, '').toUpperCase())) return true;

                    inputObject.setCustomValidity(
                        __(
                            'Please enter a valid IBAN!',
                            'payone-woocommerce-3',
                        ),
                    );
                    return false;
                }}
                value={iban}
                required
            />

            {currentMandate?.status === 'error' && currentMandate.message ? (
                <p style={{color: '#cc1818', marginTop: '0.5rem'}}>{currentMandate.message}</p>
            ) : null}

            {currentMandate?.status === 'pending' ? (
                <>
                    {currentMandate.text ? (
                        <div
                            className="payone-sepa-mandate-text"
                            dangerouslySetInnerHTML={{__html: currentMandate.text}}
                            style={{marginTop: '1rem'}}
                        />
                    ) : null}

                    <div style={{display: 'flex', alignItems: 'center', gap: '0.5rem', marginTop: '0.5rem'}}>
                        <input
                            type="checkbox"
                            id="direct_debit_confirmation_check"
                            onChange={(event) => {
                                confirmationCheckedRef.current = event.target.checked;
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
