import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {select, subscribe} from '@wordpress/data';
import {ValidatedTextInput} from '@woocommerce/blocks-checkout';
import {PAYONE_ASSETS_URL} from '../../constants';
import IconLabel from '../../components/IconLabel';
import AssetService from '../../services/AssetService';
import {createShippingAddressValidator} from '../../services/shippingAddressValidator';
import PaylaDisclaimer from './disclaimer';

const canMakePayment = createShippingAddressValidator('paylaConfig');

const PAYMENT_METHOD_NAME = 'payone_secured_invoice';

const PaylaSecuredInvoice = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const {paylaConfig} = wc.wcSettings.getSetting('payone_data');

    const [birthday, setBirthday] = useState('');
    const [vatId, setVatId] = useState('');
    const [isB2B, setIsB2B] = useState(false);

    // B2B detection: Check if billing company is filled
    useEffect(() => {
        const {CART_STORE_KEY} = wc.wcBlocksData;

        const checkB2B = () => {
            const store = select(CART_STORE_KEY);
            const {billingAddress} = store.getCartData();
            const hasCompany = billingAddress.company && billingAddress.company.trim() !== '';
            setIsB2B(hasCompany);
        };

        // Initial check
        checkB2B();

        // Subscribe to store changes
        const unsubscribe = subscribe(checkB2B);
        return () => unsubscribe();
    }, []);

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

            {isB2B && (
                <ValidatedTextInput
                    type="text"
                    id="payone_secured_invoice_vatid"
                    label={__('VAT-ID', 'payone-woocommerce-3')}
                    onChange={(value) => setVatId(value)}
                    value={vatId}
                />
            )}

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
    canMakePayment: () => canMakePayment(PAYMENT_METHOD_NAME),
    paymentMethodId: PAYMENT_METHOD_NAME,
    supports: {
        showSavedCards: false,
        showSaveOption: false,
    },
};
