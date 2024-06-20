import {__} from '@wordpress/i18n';

/**
 * @param {CalculationResult} calculationResult
 */
export default function InstallmentsCalculationShort({calculationResult}) {
    const currency = wc.wcSettings.CURRENCY.symbol;

    if (!calculationResult) return null;

    return (
        <table className="table">
            <tbody>
                <tr>
                    <th>
                        {calculationResult.number_of_rates}&nbsp;
                        {__('monthly installments', 'payone-woocommerce-3')}
                    </th>
                    <td>
                        {calculationResult.rate}
                        &nbsp;{currency}
                    </td>
                </tr>

                <tr>
                    <th>{__('Total amount', 'payone-woocommerce-3')}</th>
                    <td>
                        {calculationResult.total_amount}
                        &nbsp;{currency}
                    </td>
                </tr>
            </tbody>
        </table>
    );
}
