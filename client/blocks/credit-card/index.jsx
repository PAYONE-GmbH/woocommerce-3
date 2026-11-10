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
    const [detectedCardType, setDetectedCardType] = useState('');
    const autoDetection = Boolean(payoneConfig?.autoCardtypeDetection);
    const modernStyle = Boolean(payoneConfig?.modernStyle);
    // Result of the last successful credit card check; read by onPaymentSetup.
    const paymentMethodData = useRef(null);
    // Resolves the pending credit card check started in onCheckoutValidation.
    const pendingCheck = useRef(null);
    const payoneIFrames = useRef(null);
    const cardHolderInput = useRef(null);
    const cardTypeInput = useRef(null);

    useEffect(() => {
        const handleCheckResult = ({detail}) => {
            if (!pendingCheck.current) {
                return;
            }
            const resolve = pendingCheck.current;
            pendingCheck.current = null;
            resolve(detail);
        };
        window.addEventListener('creditCardCheckCallbackEvent', handleCheckResult);

        return () => window.removeEventListener('creditCardCheckCallbackEvent', handleCheckResult);
    }, []);

    useEffect(() => {
        if (payoneIFrames.current && !autoDetection) {
            payoneIFrames.current.setCardType(cardType);
        }
    }, [cardType, payoneIFrames.current]);

    useEffect(() => {
        if (cardHolderInput.current && payoneConfig?.fields?.cardholder) {
            if (!modernStyle) {
                cardHolderInput.current.setAttribute('style', payoneConfig.fields.cardholder.style);
            }
            cardHolderInput.current.setAttribute('size', payoneConfig.fields.cardholder.size);
            cardHolderInput.current.setAttribute('maxlength', payoneConfig.fields.cardholder.maxlength);
            cardHolderInput.current.setAttribute('type', payoneConfig.fields.cardholder.type);
        }
    }, [payoneConfig, cardHolderInput.current]);

    useEffect(() => {
        if (cardTypeInput.current && payoneConfig?.defaultStyle?.select) {
            cardTypeInput.current.setAttribute('style', payoneConfig?.defaultStyle?.select);
        }
    }, [payoneConfig, cardTypeInput.current]);

    useEffect(() => {
        const iframeConfig = {
            ...payoneConfig,
            returnType: 'handler',
            language: Payone.ClientApi.Language[payoneConfig.language],
            events: {
                rendered: () => {
                    if (payoneIFrames.current && cardType && !autoDetection) {
                        payoneIFrames.current.setCardType(cardType);
                    }
                },
            },
        };
        if (autoDetection) {
            iframeConfig.autoCardtypeDetection = {
                ...payoneConfig.autoCardtypeDetection,
                callback: (detected) => setDetectedCardType(detected),
            };
        }

        payoneIFrames.current = new Payone.ClientApi.HostedIFrames(iframeConfig, creditCardCheckRequestConfig);
    }, [creditCardCheckRequestConfig, payoneConfig]);

    // WooCommerce waits for this observer, so the credit card check runs before the order is placed.
    // Errors must be returned as an observer response, plain `false` does not stop the checkout.
    useEffect(() => onCheckoutValidation(async () => {
        const validationError = (errorMessage) => ({
            type: responseTypes.ERROR,
            errorMessage,
            context: noticeContexts.PAYMENTS,
        });

        paymentMethodData.current = null;

        if (cardHolder.length > 50 || cardHolder.match(/[^a-zA-Z \-äöüÄÖÜß]/g)) {
            return validationError(__(
                // eslint-disable-next-line max-len
                'Bitte geben Sie maximal 50 Zeichen für den Karteninhaber ein, Sonderzeichen außer Deutsche Umlaute und einem Bindestrich sind nicht erlaubt.',
                'payone-woocommerce-3',
            ));
        }

        if (!payoneIFrames.current || !payoneIFrames.current.isComplete()) {
            return validationError(__(
                'Bitte Formular vollständig ausfüllen!',
                'payone-woocommerce-3',
            ));
        }

        const detail = await new Promise((resolve) => {
            pendingCheck.current = resolve;
            payoneIFrames.current.creditCardCheck('creditCardCheckCallbackEventProxy');
        });

        if (detail?.status !== 'VALID') {
            return validationError(detail?.errormessage || __(
                'Die Zahlung konnte nicht erfolgreich durchgeführt werden.',
                'payone-woocommerce-3',
            ));
        }

        paymentMethodData.current = {
            card_holder: cardHolderInput.current?.value || '',
            card_pseudopan: detail.pseudocardpan,
            card_truncatedpan: detail.truncatedcardpan,
            card_type: detail.cardtype,
            card_expiredate: detail.cardexpiredate,
        };

        return true;
    }), [onCheckoutValidation, cardHolder]);

    useEffect(() => onPaymentSetup(() => {
        if (paymentMethodData.current) {
            return {
                type: responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: paymentMethodData.current,
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
    }), [onPaymentSetup]);

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

    const isKnownBrand = cardTypes.some(({value}) => value === detectedCardType);

    return (
        <fieldset className={`payone-cc-form${modernStyle ? ' payone-cc-form--modern' : ''}`}>
            <div className="form-row form-row-wide">
                <label htmlFor="card_holder" title={__('as printed on card', 'payone-woocommerce-3')}>
                    {__('Card Holder', 'payone-woocommerce-3')}
                </label>

                <input
                    className="payoneInput"
                    ref={cardHolderInput}
                    id="card_holder"
                    type="text"
                    name="card_holder"
                    value={cardHolder}
                    onChange={(e) => setCardHolder(e.target.value)}
                    maxLength="50"
                />
            </div>

            {!autoDetection && (
                <div className="form-row form-row-wide">
                    <label htmlFor="cardtype">{__('Card type', 'payone-woocommerce-3')}</label>
                    <select
                        id="cardtype"
                        ref={cardTypeInput}
                        className="payoneSelect"
                        onChange={(e) => setCardType(e.target.value)}
                    >
                        {cardTypes.map(({value, title}) => (
                            <option key={value} value={value} selected={cardType === value}>{title}</option>
                        ))}
                    </select>
                </div>
            )}

            <div className="form-row form-row-wide">
                <span className="payone-cc-label-row">
                    <label htmlFor="cardpan">{__('Cardpan', 'payone-woocommerce-3')}</label>
                    {autoDetection && (
                        <span className={`payone-cc-brands${isKnownBrand ? ' has-active' : ''}`}>
                            {cardTypes.map(({value, title, logo}) => (
                                <span
                                    key={value}
                                    className={`payone-cc-brand${detectedCardType === value ? ' is-active' : ''}`}
                                    title={title}
                                >
                                    {logo
                                        ? <img src={logo} alt={title} />
                                        : <span className="payone-cc-brand__text">{title}</span>}
                                </span>
                            ))}
                        </span>
                    )}
                </span>
                <div className="inputIframe" id="cardpan"></div>
            </div>

            <div className="payone-cc-row">
                <div className="form-row form-row-wide">
                    <label htmlFor="expireInput">{__('Expire Date', 'payone-woocommerce-3')}</label>
                    <div className="inputIframe" id="expireInput">
                        <span id="cardexpiremonth"></span>
                        <span id="cardexpireyear"></span>
                    </div>
                </div>

                <div className="form-row form-row-wide">
                    <label htmlFor="cardcvc2">{__('CVC', 'payone-woocommerce-3')}</label>
                    <div className="inputIframe" id="cardcvc2"></div>
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
