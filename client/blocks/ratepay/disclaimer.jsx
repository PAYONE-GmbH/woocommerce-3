import {__} from '@wordpress/i18n';

export default function RatepayDisclaimer() {
    const data = __(`With clicking on Place Order you agree to 
         <a href="https://www.ratepay.com/legal-payment-terms/" target="_blank" rel="noopener">
            Ratepay Terms of Payment
         </a> as well as to the performance of a 
         <a href="https://www.ratepay.com/legal-payment-dataprivacy/" target="_blank" rel="noopener">
            risk check by Ratepay
         </a>.`, 'payone-woocommerce-3');

    return (
        <p
            className="wc-block-checkout__terms wp-block-woocommerce-checkout-terms-block"
            dangerouslySetInnerHTML={{__html: data}}
        />
    );
}
