import {__} from '@wordpress/i18n';

export default function PaylaDisclaimer() {
    const data = __(`By placing this order, I agree to the 
         <a href="https://legal.paylater.payone.com/en/terms-of-payment.html" target="_blank" rel="noopener">
            supplementary payment terms
         </a> and the performance of a risk assessment for the selected payment method. I am aware of the 
         <a href="https://legal.paylater.payone.com/en/data-protection-payments.html" target="_blank" rel="noopener">
            supplementary data protection notice
         </a>.`, 'payone-woocommerce-3');

    return (
        <p
            className="wc-block-checkout__terms wp-block-woocommerce-checkout-terms-block"
            dangerouslySetInnerHTML={{__html: data}}
        />
    );
}
