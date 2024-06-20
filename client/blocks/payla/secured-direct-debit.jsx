import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import IBAN from 'iban';
import {PAYONE_ASSETS_URL} from '../../constants';
import AssetService from '../../services/AssetService';
import PaylaDisclaimer from './disclaimer';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

const PaylaSecuredDirectDebit = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const {paylaConfig} = wc.wcSettings.getSetting('payone_data');

    const [birthday, setBirthday] = useState('');
    const [iban, setIban] = useState('');

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

            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        payone_secured_direct_debit_birthday: birthday,
                        payone_secured_direct_debit_iban: iban,
                        payone_secured_direct_debit_token: paylaConfig.tokenSecuredDirectDebit,
                    },
                },
            };
        });
    }, [onPaymentSetup, birthday, iban]);

    useEffect(() => {
        AssetService.loadJsScript(paylaConfig.jsUrl, () => {
            /* global paylaDcs */
            if (typeof paylaDcs !== 'undefined') {
                paylaDcs.init(paylaConfig.environmentKey, paylaConfig.tokenSecuredDirectDebit);
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

            <PaylaDisclaimer />
        </div>
    );
};

export default getPaymentMethodConfig(
    'payone_secured_direct_debit',
    __('PAYONE Secured Direct Debit', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-secured-lastschrift.png`,
    <PaylaSecuredDirectDebit />,
);
