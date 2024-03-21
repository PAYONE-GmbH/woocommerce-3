import {__} from '@wordpress/i18n';

/**
 * @param {CalculationResult} calculationResult
 */
export default function InstallmentsCalculationLong({calculationResult}) {
    const currency = wc.wcSettings.CURRENCY.symbol;

    if (!calculationResult) return null;

    return (
        <table className="table">
            <tbody>
                <tr>
                    <th>
                        {__('Basket amount', 'payone-woocommerce-3')}
                    </th>
                    <td>
                        {calculationResult.amount}
                        &nbsp;{currency}
                    </td>
                </tr>

                <tr>
                    <th>{__('Servicecharge', 'payone-woocommerce-3')}</th>
                    <td>
                        {calculationResult.service_charge}
                        &nbsp;{currency}
                    </td>
                </tr>

                <tr>
                    <th>{__('Annual percentage rate', 'payone-woocommerce-3')}</th>
                    <td>
                        {calculationResult.annual_percentage_rate}
                        &nbsp;%
                    </td>
                </tr>

                <tr>
                    <th>{__('Interest rate', 'payone-woocommerce-3')}</th>
                    <td>
                        {calculationResult.interest_rate}
                        &nbsp;{currency}
                    </td>
                </tr>

                <tr>
                    <th>{__('Interest amount', 'payone-woocommerce-3')}</th>
                    <td>
                        {calculationResult.interest_amount}
                        &nbsp;{currency}
                    </td>
                </tr>

                <tr />

                <tr>
                    <th>
                        {calculationResult.number_of_rates - 1}&nbsp;
                        {__('monthly installments', 'payone-woocommerce-3')}
                        &nbsp;à
                    </th>
                    <td>
                        {calculationResult.rate}
                        &nbsp;{currency}
                    </td>
                </tr>

                <tr>
                    <th>
                        {__('incl. one final installment', 'payone-woocommerce-3')}
                    </th>
                    <td>
                        {calculationResult.last_rate}
                        &nbsp;{currency}
                    </td>
                </tr>

                <tr>
                    <th>
                        {__('Total amount', 'payone-woocommerce-3')}
                    </th>
                    <td>
                        {calculationResult.total_amount}
                        &nbsp;{currency}
                    </td>
                </tr>
            </tbody>
        </table>
    );
}
