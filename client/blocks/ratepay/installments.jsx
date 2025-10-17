import {__} from '@wordpress/i18n';
import {useEffect, useMemo, useState} from '@wordpress/element';
import {Label, ValidatedTextInput, Button} from '@woocommerce/blocks-checkout';
import IBAN from 'iban';
import RatepayDisclaimer from './disclaimer';
import {PAYONE_ASSETS_URL} from '../../constants';
import IconLabel from '../../components/IconLabel';
import {createShippingAddressValidator} from '../../services/shippingAddressValidator';
import InstallmentsCalculationLong from './installments/installments-calculation-long';
import InstallmentsCalculationShort from './installments/installments-calculation-short';

const PAYMENT_METHOD_NAME = 'payone_ratepay_installments';
const canMakePayment = createShippingAddressValidator('ratepayConfig');

// TODO: Auf Typescript umbauen
/**
 * @typedef CalculationResult
 * @property {string} amount
 * @property {string} annual_percentage_rate
 * @property {string} interest_amount
 * @property {string} interest_rate
 * @property {string} last_rate
 * @property {string} monthly_debit_interest
 * @property {string} number_of_rates
 * @property {string} payment_firstday
 * @property {string} rate
 * @property {string} service_charge
 * @property {string} total_amount
 * @property {object} form
 * @property {string} form.amount
 * @property {string} form.installment_amount
 * @property {string} form.installment_number
 * @property {number} form.interest_rate - only field thats a number ?! :D
 * @property {string} form.last_installment_amount
 */

const RatepayInstallments = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const {ratepayCalculationUrl, installmentMonthOptions} = wc.wcSettings.getSetting('payone_data');

    const [selectedNumberOfMonths, setSelectedNumberOfMonths] = useState('0');
    const [monthlyRate, setMonthlyRate] = useState('');
    const [birthday, setBirthday] = useState('');
    const [iban, setIban] = useState('');
    const [showDetailedCalculation, setShowDetailedCalculation] = useState(false);
    const [showCalculation, setShowCalculation] = useState(false);
    const [calculationResult, setCalculationResult] = useState(/** @type {CalculationResult|null} */ null);

    const updateCalculation = (calculationType) => {
        const body = new FormData();
        body.append('calculation-type', calculationType);
        body.append('month', selectedNumberOfMonths);
        body.append('rate', monthlyRate);

        fetch(ratepayCalculationUrl, {
            method: 'POST',
            body,
        })
            .then((response) => response.json())
            .then((/** @type CalculationResult */json) => {
                if (json === -1) {
                    // TODO: Fehlermeldung ausgeben?
                    setCalculationResult(null);
                    setShowCalculation(false);
                } else {
                    setCalculationResult(json);
                    setShowCalculation(true);
                }
            });
    };

    const hiddenFormFields = useMemo(() => {
        if (!calculationResult || !calculationResult.form) {
            return {};
        }

        return {
            ratepay_installments_installment_amount: calculationResult.form.installment_amount,
            ratepay_installments_installment_number: calculationResult.form.installment_number,
            ratepay_installments_last_installment_amount: calculationResult.form.last_installment_amount,
            ratepay_installments_interest_rate: calculationResult.form.interest_rate.toString(),
            ratepay_installments_amount: calculationResult.form.amount,
        };
    }, [calculationResult]);

    useEffect(() => {
        if (calculationResult !== null) {
            setSelectedNumberOfMonths(calculationResult.number_of_rates);
        }
    }, [calculationResult]);

    useEffect(() => {
        return onPaymentSetup(() => {
            if (!calculationResult || !hiddenFormFields) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Could not calculate your installment plan. Please try again with different values',
                        'payone-woocommerce-3',
                    ),
                };
            }

            if (!birthday) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Please enter a valid birthday!',
                        'payone-woocommerce-3',
                    ),
                };
            }

            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        ratepay_installments_months: selectedNumberOfMonths,
                        ratepay_installments_rate: monthlyRate,
                        ratepay_installments_birthday: birthday,
                        ratepay_installments_iban: iban,
                        ...hiddenFormFields,
                    },
                },
            };
        });
    }, [onPaymentSetup, birthday, iban, selectedNumberOfMonths, monthlyRate, calculationResult, hiddenFormFields]);

    return (
        <div>
            <div className="wc-block-sort-select wc-block-components-sort-select">
                <Label
                    label={__('Number of monthly installments', 'payone-woocommerce-3')}
                    screenReaderLabel={__('Select bank group', 'payone-woocommerce-3')}
                    wrapperElement="label"
                    wrapperProps={{
                        className: 'wc-block-sort-select__label wc-block-components-sort-select__label',
                        htmlFor: 'bankgrouptype',
                    }}
                />
                <br />

                <select
                    id="ratepay_installments_months"
                    className="wc-block-sort-select__select wc-block-components-sort-select__select payoneSelect"
                    onChange={(event) => {
                        setSelectedNumberOfMonths(event.target.value);
                        updateCalculation('calculation-by-time');
                    }}
                    value={selectedNumberOfMonths}
                >
                    {installmentMonthOptions.map((month) => (
                        <option key={month} value={month}>
                            {month === '0' ? __('Choose', 'payone-woocommerce-3') : month}
                        </option>
                    ))}
                </select>
            </div>

            <ValidatedTextInput
                type="text"
                id="ratepay_installments_rate"
                label={__('Monthly rate', 'payone-woocommerce-3')}
                onChange={(value) => setMonthlyRate(value)}
                value={monthlyRate}
                required
            />

            <div className="wc-block-checkout__actions">
                <Button onClick={(event) => {
                    event.preventDefault();
                    updateCalculation('calculation-by-rate');
                }}>
                    {__('Calculate', 'payone-woocommerce-3')}
                </Button>
            </div>

            {showCalculation ? (
                <>
                    <h3>
                        {__('Personal calculation', 'payone-woocommerce-3')}
                    </h3>

                    {showDetailedCalculation ? (
                        <InstallmentsCalculationLong calculationResult={calculationResult} />
                    ) : (
                        <InstallmentsCalculationShort calculationResult={calculationResult} />
                    )}

                    <small>
                        <button onClick={(event) => {
                            event.preventDefault();
                            setShowDetailedCalculation(!showDetailedCalculation);
                        }}>
                            {showDetailedCalculation ? (
                                __('Hide details', 'payone-woocommerce-3')
                            ) : (
                                __('Show details', 'payone-woocommerce-3')
                            )}
                        </button>
                    </small>

                    <ValidatedTextInput
                        type="date"
                        className="payone-validated-date-input is-active"
                        id="ratepay_installments_birthday"
                        label={__('Birthday', 'payone-woocommerce-3')}
                        onChange={(value) => setBirthday(value)}
                        value={birthday}
                        required
                    />

                    <ValidatedTextInput
                        id="ratepay_installments_iban"
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
                </>
            ) : null}

            <RatepayDisclaimer />
        </div>
    );
};

const label = __('PAYONE Ratepay Installments', 'payone-woocommerce-3');

export default {
    name: PAYMENT_METHOD_NAME,
    label: <IconLabel text={label} icon={`${PAYONE_ASSETS_URL}/icon-ratepay.svg`} />,
    ariaLabel: label,
    content: <RatepayInstallments />,
    edit: <RatepayInstallments />,
    canMakePayment: () => canMakePayment(PAYMENT_METHOD_NAME),
    paymentMethodId: PAYMENT_METHOD_NAME,
    supports: {
        showSavedCards: false,
        showSaveOption: false,
    },
};
