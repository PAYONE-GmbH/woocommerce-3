import {select} from '@wordpress/data';
import isShallowEqual from '@wordpress/is-shallow-equal';

/**
 * Determines if PayLa payment methods should be available based on shipping address configuration.
 *
 * PayLa payment methods are only available when:
 * 1. The backend setting "allow_different_shopping_address" is enabled, OR
 * 2. The billing and shipping addresses are identical
 *
 * This mirrors the logic in PaylaBase::is_available() for traditional checkout.
 *
 * @returns {boolean} Whether the payment method can be used
 */
export default function canMakePayment() {
    const {paylaConfig} = wc.wcSettings.getSetting('payone_data');

    // If different shipping addresses are allowed, payment method is always available
    if (paylaConfig.allowDifferentShippingAddress) {
        return true;
    }

    // If different shipping addresses are not allowed, check if addresses match
    const {CART_STORE_KEY} = wc.wcBlocksData;
    const store = select(CART_STORE_KEY);
    const {billingAddress, shippingAddress} = store.getCartData();

    // Fields to compare for address matching (matches PHP logic in PaylaBase::is_available())
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

    // Extract only relevant fields for comparison
    const billingData = {};
    const shippingData = {};

    relevantFields.forEach((field) => {
        billingData[field] = billingAddress[field];
        shippingData[field] = shippingAddress[field];
    });

    // Compare the two address objects
    return isShallowEqual(billingData, shippingData);
}
