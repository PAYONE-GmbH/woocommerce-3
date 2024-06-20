import {__} from '@wordpress/i18n';
import {useEffect, useRef, useState} from '@wordpress/element';
import {PAYONE_ASSETS_URL} from '../../constants';
import getPaymentMethodConfig from '../../services/getPaymentMethodConfig';

window.creditCardCheckCallbackEventProxy = (response) => {
    window.dispatchEvent(new CustomEvent('creditCardCheckCallbackEvent', {detail: response}));
};

const PayoneCreditCard = ({
    eventRegistration,
    emitResponse,
    onSubmit,
}) => {
    // Data from PayoneBlocksSupport.php - get_payment_method_data()
    const {
        creditCardCheckRequestConfig,
        cardTypes,
        payoneConfig,
    } = wc.wcSettings.getSetting('payone_data');
    const {
        onPaymentSetup,
        onCheckoutValidation,
        onCheckoutAfterProcessingWithError,
    } = eventRegistration;
    const {responseTypes, noticeContexts} = emitResponse;

    const [cardHolder, setCardHolder] = useState('');
    const [cardType, setCardType] = useState(cardTypes[0]?.value ?? '');
    const [payoneCheckSucceeded, setPayoneCheckSucceeded] = useState(false);
    const [paymentMethodData, setPaymentMethodData] = useState(null);
    const [errorMessage, setErrorMessage] = useState(null);
    const payoneIFrames = useRef(null);

    useEffect(() => {
        window.addEventListener('creditCardCheckCallbackEvent', ({detail}) => {
            if (detail.status === 'VALID') {
                setPayoneCheckSucceeded(true);

                setPaymentMethodData({
                    card_holder: cardHolder,
                    card_pseudopan: detail.pseudocardpan,
                    card_truncatedpan: detail.truncatedcardpan,
                    card_type: detail.cardtype,
                    card_expiredate: detail.cardexpiredate,
                });

                // Re-Trigger payment processing
                onSubmit();
            } else if (detail.errormessage) {
                setErrorMessage(detail.errormessage);
            }
        });
    }, []);

    useEffect(() => {
        if (payoneIFrames.current) {
            payoneIFrames.current.setCardType(cardType);
        }
    }, [cardType, payoneIFrames.current]);

    useEffect(() => {
        payoneIFrames.current = new Payone.ClientApi.HostedIFrames(
            {
                ...payoneConfig,
                returnType: 'handler',
                language: Payone.ClientApi.Language[payoneConfig.language],
            },
            {
                ...creditCardCheckRequestConfig,
                mid: creditCardCheckRequestConfig.merchant_id,
                aid: creditCardCheckRequestConfig.account_id,
                portalid: creditCardCheckRequestConfig.portal_id,
            },
        );
    }, [creditCardCheckRequestConfig, payoneConfig]);

    useEffect(() => onCheckoutValidation(async () => {
        if (payoneCheckSucceeded) {
            // Skip the test, as it already succeeded.
            return true;
        }

        if (cardHolder.length > 50 || cardHolder.match(/[^a-zA-Z \-äöüÄÖÜß]/g)) {
            return setErrorMessage(__(
                // eslint-disable-next-line max-len
                'Bitte geben Sie maximal 50 Zeichen für den Karteninhaber ein, Sonderzeichen außer Deutsche Umlaute und einem Bindestrich sind nicht erlaubt.',
                'payone-woocommerce-3',
            ));
        }

        if (!payoneIFrames.current.isComplete()) {
            return setErrorMessage(__(
                'Bitte Formular vollständig ausfüllen!',
                'payone-woocommerce-3',
            ));
        }

        payoneIFrames.current.creditCardCheck('creditCardCheckCallbackEventProxy');

        // Prevent automatical submit
        return false;
    }), [onCheckoutValidation, payoneCheckSucceeded, cardHolder]);

    useEffect(() => onPaymentSetup(() => {
        if (errorMessage) {
            return {
                type: responseTypes.ERROR,
                message: errorMessage,
            };
        }

        if (payoneCheckSucceeded) {
            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData,
                },
            };
        }

        return {
            type: responseTypes.ERROR,
            message: __(
                'Die Zahlung konnte nicht erfolgreich durchgeführt werden.',
                'payone-woocommerce-3',
            ),
        };
    }), [onPaymentSetup, paymentMethodData, errorMessage]);

    // hook into and register callbacks for events.
    useEffect(() => {
        return () => onCheckoutAfterProcessingWithError(({processingResponse}) => {
            if (processingResponse?.paymentDetails?.errorMessage) {
                return {
                    type: responseTypes.ERROR,
                    message: processingResponse.paymentDetails.errorMessage,
                    messageContext: noticeContexts.PAYMENTS,
                };
            }

            // so we don't break the observers.
            return true;
        });
    }, [
        onCheckoutAfterProcessingWithError,
        noticeContexts.PAYMENTS,
        responseTypes.ERROR,
    ]);

    return (
        <fieldset>
            <div className="form-row form-row-wide">
                <label htmlFor="card_holder" title={__('as printed on card', 'payone-woocommerce-3')}>
                    {__('Card Holder', 'payone-woocommerce-3')}
                </label>

                <input
                    className="payoneInput"
                    id="card_holder"
                    type="text"
                    name="card_holder"
                    value={cardHolder}
                    onChange={(e) => setCardHolder(e.target.value)}
                    maxLength="50"
                />
            </div>

            <div className="form-row form-row-wide">
                <label htmlFor="cardtype">{__('Card type', 'payone-woocommerce-3')}</label>
                <select
                    id="cardtype"
                    className="payoneSelect"
                    onChange={(e) => setCardType(e.target.value)}
                >
                    {cardTypes.map(({value, title}) => (
                        <option key={value} value={value} selected={cardType === value}>{title}</option>
                    ))}
                </select>
            </div>

            <div className="form-row form-row-wide">
                <label htmlFor="cardpan">{__('Cardpan', 'payone-woocommerce-3')}</label>
                <div className="inputIframe" id="cardpan"></div>
            </div>

            <div className="form-row form-row-wide">
                <label htmlFor="cardcvc2">{__('CVC', 'payone-woocommerce-3')}</label>
                <div className="inputIframe" id="cardcvc2"></div>
            </div>

            <div className="form-row form-row-wide">
                <label htmlFor="expireInput">{__('Expire Date', 'payone-woocommerce-3')}</label>
                <div className="inputIframe" id="expireInput">
                    <span id="cardexpiremonth"></span>
                    <span id="cardexpireyear"></span>
                </div>
            </div>
        </fieldset>
    );
};

export default getPaymentMethodConfig(
    'bs_payone_creditcard',
    __('PAYONE Kreditkarte', 'payone-woocommerce-3'),
    `${PAYONE_ASSETS_URL}/icon-creditcard.png`,
    <PayoneCreditCard />,
);
