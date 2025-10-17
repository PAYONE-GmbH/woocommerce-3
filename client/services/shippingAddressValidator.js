import {select} from '@wordpress/data';
import isShallowEqual from '@wordpress/is-shallow-equal';

/**
 * Creates a canMakePayment validator for payment methods that require matching billing/shipping addresses.
 *
 * This function generates a validator that checks if a payment method should be available based on:
 * 1. The backend setting "allow_different_shopping_address" being enabled, OR
 * 2. The billing and shipping addresses being identical
 *
 * This mirrors the logic in PaylaBase::is_available() and RatepayBase::is_available() for traditional checkout.
 *
 * @param {string} configKey - The config key in payone_data (e.g., 'paylaConfig' or 'ratepayConfig')
 * @returns {function(string): boolean} A canMakePayment function that accepts a gateway ID
 *
 * @example
 * // In a payment block:
 * import {createShippingAddressValidator} from '../../services/shippingAddressValidator';
 * const canMakePayment = createShippingAddressValidator('paylaConfig');
 * export default {
 *   name: 'payone_secured_invoice',
 *   canMakePayment: () => canMakePayment('payone_secured_invoice'),
 * };
 */
export function createShippingAddressValidator(configKey) {
    return function canMakePayment(gatewayId) {
        const payoneData = wc.wcSettings.getSetting('payone_data');
        const config = payoneData[configKey];

        if (!config || !config.allowDifferentShippingAddress) {
            return true;
        }

        const allowDifferentShippingAddress = config.allowDifferentShippingAddress[gatewayId];

        if (allowDifferentShippingAddress) {
            return true;
        }

        const {CART_STORE_KEY} = wc.wcBlocksData;
        const store = select(CART_STORE_KEY);
        const {billingAddress, shippingAddress} = store.getCartData();

        const relevantFields = [
            'first_name',
            'last_name',
            'company',
            'address_1',
            'address_2',
            'city',
            'postcode',
            'country',
        ];

        const billingData = {};
        const shippingData = {};

        relevantFields.forEach((field) => {
            billingData[field] = billingAddress[field];
            shippingData[field] = shippingAddress[field];
        });

        return isShallowEqual(billingData, shippingData);
    };
}
