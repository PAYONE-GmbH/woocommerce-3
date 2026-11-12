const selectedMethods = new Set();

/**
 * Selects the given payment method in the checkout block as soon as it is available.
 * Used when the customer returns from an express checkout.
 *
 * @param {string} name Payment method name
 */
export default function autoSelectPaymentMethod(name) {
    if (selectedMethods.has(name)) {
        return;
    }
    selectedMethods.add(name);

    try {
        const {dispatch, select, subscribe} = wp.data;

        const unsubscribe = subscribe(() => {
            try {
                const availableMethods = select('wc/store/payment')?.getAvailablePaymentMethods?.();
                if (!availableMethods || !availableMethods[name]) {
                    return;
                }

                const paymentStore = dispatch('wc/store/payment');
                if (paymentStore?.__internalSetActivePaymentMethod) {
                    paymentStore.__internalSetActivePaymentMethod(name);
                    unsubscribe();
                }
            } catch (e) {
                // Ignore, the customer can still select the payment method manually.
            }
        });
    } catch (e) {
        // wp.data is not available.
    }
}
