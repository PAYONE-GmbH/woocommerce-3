import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import IBAN from 'iban';
import {PAYONE_ASSETS_URL} from '../../constants';
import AssetService from '../../services/AssetService';
import PaylaDisclaimer from './disclaimer';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

const InstallmentOptionsTable = ({
    options,
    onSelect,
}) => {
    const columnLabels = {
        number_of_payments: __('Numberja  of payments', 'payone-woocommerce-3'),
        monthly_amount: __('Monthly rate', 'payone-woocommerce-3'),
        total_amount_value: __('Total amount', 'payone-woocommerce-3'),
        nominal_interest_rate: __('Interest rate', 'payone-woocommerce-3'),
        effective_interest_rate: __('Annual percentage rate', 'payone-woocommerce-3'),
    };

    return options.map((row, index) => {
        const headline = __(
            'Payable in __num_installments__ installments, each __monthly_amount__',
            'payone-woocommerce-3',
        )
            .replace('__num_installments__', row.number_of_payments)
            .replace('__monthly_amount__', row.monthly_amount);

        return (
            <div>
                <input
                    type="radio"
                    className="input-radio"
                    id={`payone_secured_installment_option_${index}`}
                    name="payone_secured_installment_option"
                    value={row.option_id}
                    onClick={() => onSelect(row.option_id)}
                />

                <label htmlFor={`payone_secured_installment_option_${index}`}>{headline}</label>

                {selectedOption === row.option_id ? (
                    <table style={{marginTop: '0.5rem'}}>
                        <tbody>
                            {Object.keys(columnLabels).map((key) => (
                                <tr>
                                    <th>{columnLabels[key]}</th>
                                    <td>{row[key].replace('&nbsp;', ' ')}</td>
                                </tr>
                            ))}

                            <tr>
                                <th colSpan={2}>
                                    <a href={row.info_url} target="_blank" rel="noopener">
                                        {__('Link to credit information', 'payone-woocommerce-3')}
                                    </a>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                ) : null}
            </div>
        );
    });
};

const PaylaSecuredInstallment = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const {paylaConfig} = wc.wcSettings.getSetting('payone_data');

    const [birthday, setBirthday] = useState('');
    const [iban, setIban] = useState('');
    const [resultsTable, setResultsTable] = useState('');
    const [selectedOption, setSelectedOption] = useState(null);
    const [workOrderId, setWorkOrderId] = useState(null);

    const initWidget = () => {
        fetch(paylaConfig.urlSecuredInstallment, {
            method: 'POST',
        })
            .then((response) => response.json())
            .then((json) => {
                setWorkOrderId(json.workorderid);
                setResultsTable(json);
            });
    };

    useEffect(() => initWidget(), []);

    useEffect(() => {
        return onPaymentSetup(() => {
            if (!birthday) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Please enter your birthday!',
                        'payone-woocommerce-3',
                    ),
                };
            }

            if (!selectedOption) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Please choose a payment plan!',
                        'payone-woocommerce-3',
                    ),
                };
            }

            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        payone_secured_installment_birthday: birthday,
                        payone_secured_installment_iban: iban,
                        payone_secured_installment_token: paylaConfig.tokenSecuredInstallment,
                        payone_secured_installment_option: selectedOption,
                        payone_secured_installment_workorderid: workOrderId,
                    },
                },
            };
        });
    }, [onPaymentSetup, birthday, iban, selectedOption, workOrderId]);

    useEffect(() => {
        AssetService.loadJsScript(paylaConfig.jsUrl, () => {
            /* global paylaDcs */
            if (typeof paylaDcs !== 'undefined') {
                paylaDcs.init(paylaConfig.environmentKey, paylaConfig.tokenSecuredInstallment);
            }
        });

        AssetService.loadCssStylesheet(paylaConfig.cssUrl);
    }, []);

    return (
        <div>
            <ValidatedTextInput
                type="date"
                id="payone_secured_invoice_birthday"
                className="payone-validated-date-input is-active"
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

            {resultsTable ? (
                <div style={{
                    marginTop: '1rem',
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '0.5rem',
                }}>
                    <p style={{margin: 0, fontWeight: 'bold'}}>
                        {__('Select the number of payments', 'payone-woocommerce-3')}
                    </p>

                    <InstallmentOptionsTable
                        options={resultsTable}
                        onSelect={setSelectedOption}
                    />
                </div>
            ) : null}

            <PaylaDisclaimer />
        </div>
    );
};

export default getPaymentMethodConfig(
    'payone_secured_installment',
    __('PAYONE Secured Installment', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-rechnungskauf.png`,
    <PaylaSecuredInstallment />,
);
