import {__} from '@wordpress/i18n';
import {useEffect, useState} from '@wordpress/element';
import {Label} from '@woocommerce/blocks-checkout';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

const PayoneEps = ({
    eventRegistration: {onPaymentSetup},
    emitResponse: {responseTypes},
}) => {
    const {epsBankGroups} = wc.wcSettings.getSetting('payone_data');
    const [bankgroupType, setBankgroupType] = useState(Object.keys(epsBankGroups)[0]);

    useEffect(() => {
        // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

        return onPaymentSetup(() => {
            if (bankgroupType) {
                return {
                    type: responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            bankgrouptype: bankgroupType,
                        },
                    },
                };
            }

            return {
                type: responseTypes.ERROR,
                message: __(
                    'Please select a valid bank group!',
                    'payone-woocommerce-3',
                ),
            };
        });
    }, [onPaymentSetup, bankgroupType]);

    return (
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
                {Object.keys(epsBankGroups).map((key) => (
                    <option key={key} value={key}>
                        {epsBankGroups[key]}
                    </option>
                ))}
            </select>
        </div>
    );
};

export default getPaymentMethodConfig(
    'bs_payone_eps',
    __('PAYONE eps', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-eps.png`,
    <PayoneEps />,
);
