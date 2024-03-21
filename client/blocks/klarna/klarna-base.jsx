import {useEffect, useState} from '@wordpress/element';
import {select} from '@wordpress/data';
import {__} from '@wordpress/i18n';
import KlarnaService from './KlarnaService';

/* global Klarna */
export default function KlarnaBase(props) {
    const {
        eventRegistration: {onPaymentSetup, onCheckoutValidation},
        emitResponse: {responseTypes},
        onSubmit,
    } = props;
    const {klarnaCategory, label} = props;

    const [widgetShown, setWidgetShown] = useState(false);
    const [klarnaWorkOrderId, setKlarnaWorkOrderId] = useState(null);
    const [paymentMethodData, setPaymentMethodData] = useState(null);
    const [klarnaCheckSucceeded, setKlarnaCheckSucceeded] = useState(false);
    const [errorMessage, setErrorMessage] = useState(null);
    const [klarnaData, setKlarnaData] = useState(null);
    const {klarnaStartSessionUrl} = wc.wcSettings.getSetting('payone_data');
    const {CART_STORE_KEY} = wc.wcBlocksData;

    const initKlarnaWidget = () => {
        KlarnaService.loadKlarnaScript();

        const store = select(CART_STORE_KEY);
        const {billingAddress, shippingAddress} = store.getCartData();

        const body = new FormData();
        body.append('category', klarnaCategory);
        body.append('currency', wc.wcSettings.CURRENCY.code);
        body.append('country', billingAddress.country);
        body.append('firstname', billingAddress.first_name);
        body.append('lastname', billingAddress.last_name);
        body.append('company', billingAddress.company);
        body.append('street', billingAddress.address_1);
        body.append('addressaddition', billingAddress.address_2);
        body.append('zip', billingAddress.postcode);
        body.append('city', billingAddress.city);
        body.append('email', billingAddress.email);
        body.append('telephonenumber', billingAddress.phone);
        body.append('shipping_country', shippingAddress.country);
        body.append('shipping_firstname', shippingAddress.first_name);
        body.append('shipping_lastname', shippingAddress.last_name);
        body.append('shipping_company', shippingAddress.company);
        body.append('shipping_street', shippingAddress.address_1);
        body.append('shipping_addressaddition', shippingAddress.address_2);
        body.append('shipping_zip', shippingAddress.postcode);
        body.append('shipping_city', shippingAddress.city);
        body.append('shipping_email', billingAddress.email);
        body.append('shipping_telephonenumber', billingAddress.phone);

        fetch(klarnaStartSessionUrl, {
            method: 'POST',
            body,
        })
            .then((response) => response.json())
            .then((json) => {
                if (json.status === 'ok') {
                    setKlarnaWorkOrderId(json.workorderid);
                    setKlarnaData(json.data);

                    Klarna.Payments.init({client_token: json.client_token});
                    Klarna.Payments.load({
                        container: `#klarna_${klarnaCategory}_container`,
                        payment_method_category: klarnaCategory,
                    }, (klarnaResult) => {
                        if (klarnaResult.show_form) {
                            setWidgetShown(true);
                            setErrorMessage(null);
                        } else {
                            setErrorMessage(__(
                                `${label} kann nicht genutzt werden!`,
                                'payone-woocommerce-3',
                            ));
                        }
                    });
                } else if (json.message) {
                    setErrorMessage(json.message);
                }
            });
    };

    useEffect(() => initKlarnaWidget(), []);

    useEffect(() => onCheckoutValidation(async () => {
        if (klarnaCheckSucceeded) {
            // Skip the test, as it already succeeded.
            return true;
        }

        if (errorMessage) {
            return false;
        }

        const store = select(CART_STORE_KEY);
        const {billingAddress} = store.getCartData();

        Klarna.Payments.authorize(
            {payment_method_category: klarnaCategory},
            klarnaData,
            (klarnaResult) => {
                if (!klarnaResult.approved && !klarnaResult.show_form) {
                    setErrorMessage(__(
                        `${label} kann nicht genutzt werden!`,
                        'payone-woocommerce-3',
                    ));
                } else if (!klarnaResult.approved) {
                    setErrorMessage(__(
                        'Der Vorgang wurde abgebrochen',
                        'payone-woocommerce-3',
                    ));
                } else {
                    setErrorMessage(null);
                    setPaymentMethodData({
                        klarna_authorization_token: klarnaResult.klarna_authorization_token,
                        klarna_workorderid: klarnaWorkOrderId,
                        klarna_shipping_email: billingAddress.email,
                        klarna_shipping_telephonenumber: billingAddress.phone,
                    });

                    setKlarnaCheckSucceeded(true);

                    // Re-Trigger payment processing
                    onSubmit();
                }
            },
        );

        // Prevent automatical submit
        return false;
    }), [onCheckoutValidation, klarnaCheckSucceeded, klarnaWorkOrderId, errorMessage]);

    useEffect(() => {
        return onPaymentSetup(() => {
            if (errorMessage) {
                return {
                    type: responseTypes.ERROR,
                    message: errorMessage,
                };
            }

            if (!widgetShown) {
                initKlarnaWidget();
            }

            if (klarnaCheckSucceeded && paymentMethodData) {
                return {
                    type: responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData,
                    },
                };
            }

            return false;
        });
    }, [onPaymentSetup, klarnaCheckSucceeded, paymentMethodData]);

    return (
        <>
            {errorMessage ? <strong style={{color: 'red'}}>{errorMessage}</strong> : null}

            <div id={`klarna_${klarnaCategory}_container`} />
        </>
    );
}
