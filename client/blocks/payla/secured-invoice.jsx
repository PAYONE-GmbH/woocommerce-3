import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import {PAYONE_ASSETS_URL} from '../../constants';
import IconLabel from '../../components/IconLabel';
import AssetService from '../../services/AssetService';
import PaylaDisclaimer from './disclaimer';

const PAYMENT_METHOD_NAME = 'payone_secured_invoice';

const PaylaSecuredInvoice = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const {paylaConfig} = wc.wcSettings.getSetting('payone_data');

    const [birthday, setBirthday] = useState('');
    const [vatId, setVatId] = useState('');

    useEffect(() => {
        return onPaymentSetup(() => {
            if (!birthday) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Please enter a valid birthday!',
                        'payone-woocommerce-3',
                    ),
                };
            }

            if (!vatId) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Please enter a valid VAT-ID!',
                        'payone-woocommerce-3',
                    ),
                };
            }

            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        payone_secured_invoice_birthday: birthday,
                        payone_secured_invoice_vatid: vatId,
                        payone_secured_invoice_token: paylaConfig.tokenSecuredIncoice,
                    },
                },
            };
        });
    }, [onPaymentSetup, birthday, vatId]);

    useEffect(() => {
        AssetService.loadJsScript(paylaConfig.jsUrl, () => {
            /* global paylaDcs */
            if (typeof paylaDcs !== 'undefined') {
                paylaDcs.init(paylaConfig.environmentKey, paylaConfig.tokenSecuredInvoice);
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
                type="text"
                id="payone_secured_invoice_vatid"
                label={__('VAT-ID', 'payone-woocommerce-3')}
                onChange={(value) => setVatId(value)}
                value={vatId}
                required
            />

            <PaylaDisclaimer />
        </div>
    );
};

const label = __('PAYONE Secured Invoice', 'payone-woocommerce-3');

export default {
    name: PAYMENT_METHOD_NAME,
    label: <IconLabel text={label} icon={`${PAYONE_ASSETS_URL}/icon-rechnungskauf.png`} />,
    ariaLabel: label,
    content: <PaylaSecuredInvoice />,
    edit: <PaylaSecuredInvoice />,
    canMakePayment: () => true,
    paymentMethodId: PAYMENT_METHOD_NAME,
    supports: {
        showSavedCards: false,
        showSaveOption: false,
    },
};
