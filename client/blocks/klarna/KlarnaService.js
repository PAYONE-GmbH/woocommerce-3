import AssetService from '../../services/AssetService';

/**
 * @readonly
 * @enum {string}
 */
export const KLARNA_GATEWAY_IDS = {
    SOFORT: 'payone_klarna_sofort',
    INVOICE: 'payone_klarna_invoice',
    INSTALLMENTS: 'payone_klarna_installments',
};

export const KLARNA_CATEGORIES = {
    PAY_LATER: 'pay_later',
    PAY_OVER_TIME: 'pay_over_time',
    DIRECT_DEBIT: 'direct_debit',
};

export default class KlarnaService {
    /**
     * @param {string} gatewayId
     * @return {string}
     */
    static getCategoryForKlarnaGatewayId(gatewayId) {
        const klarnaCategories = {
            [KLARNA_GATEWAY_IDS.INVOICE]: KLARNA_CATEGORIES.PAY_LATER,
            [KLARNA_GATEWAY_IDS.INSTALLMENTS]: KLARNA_CATEGORIES.PAY_OVER_TIME,
            [KLARNA_GATEWAY_IDS.SOFORT]: KLARNA_CATEGORIES.DIRECT_DEBIT,
        };

        if ({}.hasOwnProperty.call(klarnaCategories, gatewayId)) {
            return klarnaCategories[gatewayId];
        }

        return '';
    }

    static loadKlarnaScript() {
        if (!window.klarnaApiInitiated) {
            AssetService.loadJsScript('https://x.klarnacdn.net/kp/lib/v1/api.js', () => {
                window.klarnaApiInitiated = true;
            });
        }
    }
}
