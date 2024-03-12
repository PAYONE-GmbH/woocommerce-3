import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {Label} from '@woocommerce/blocks-checkout';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

const PayoneIdeal = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const {idealBankGroups} = wc.wcSettings.getSetting('payone_data');
    const [bankgroupType, setBankgroupType] = useState(Object.keys(idealBankGroups)[0]);

    useEffect(() => {
        // TODO: Server antwortet mit Fehlercode 923 "Payment type not available for this currency or card type"
        // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

        return onPaymentSetup(() => {
            if (!bankgroupType) {
                return {
                    type: responseTypes.ERROR,
                    message: __(
                        'Please select a valid bank group!',
                        'payone-woocommerce-3',
                    ),
                };
            }

            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        bankgrouptype: bankgroupType,
                    },
                },
            };
        });
    }, [onPaymentSetup, bankgroupType]);

    return (
        <>
            <div className="wc-block-sort-select wc-block-components-sort-select">
                <Label
                    label={__('Bank group', 'payone-woocommerce-3')}
                    screenReaderLabel={__('Select bank group', 'payone-woocommerce-3')}
                    wrapperElement="label"
                    wrapperProps={{
                        className: 'wc-block-sort-select__label wc-block-components-sort-select__label',
                        htmlFor: 'bankgrouptype',
                    }}
                />

                <select
                    id="bankgrouptype"
                    className="wc-block-sort-select__select wc-block-components-sort-select__select payoneSelect"
                    onChange={(e) => setBankgroupType(e.target.value)}
                    value={bankgroupType}
                >
                    {Object.keys(idealBankGroups).map((key) => (
                        <option key={key} value={key}>
                            {idealBankGroups[key]}
                        </option>
                    ))}
                </select>
            </div>
        </>
    );
};

export default getPaymentMethodConfig(
    'payone_ideal',
    __('PAYONE iDEAL', 'payone-woocommerce-3'),
    'https://cdn.pay1.de/clearingtypes/sb/idl/default.svg',
    <PayoneIdeal />,
);
