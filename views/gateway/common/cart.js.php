<?php
use Payone\Plugin;\Payone\Plugin::delete_session_value( \Payone\Gateway\PayPalExpress::SESSION_KEY_WORKORDERID );

$paypal_express_button_image = PAYONE_PLUGIN_URL . 'assets/' . __( 'checkout-paypal-en.png', 'payone-woocommerce-3' );
$paypal_express_button_image = apply_filters( 'payone_paypal_express_button_image_url', $paypal_express_button_image );
?>
<script type="text/javascript">
    jQuery('#payone-paypal-express-button').html('<button style="text-align:center;width:100%" title="PayPal Express"><img style="margin:auto; text-align:left;" src="<?php echo $paypal_express_button_image; ?>" alt="PayPal Express"></button>');
    jQuery('#payone-paypal-express-button').on('click', function (event) {
    jQuery('.cart_totals').block({
        message: null,
        overlayCSS: {
            background: '#fff',
            opacity: 0.6
        }
    });
    event.preventDefault();
    jQuery.post('<?php echo \Payone\Plugin::get_callback_url( [ 'type' => 'ajax-paypal-express-set-checkout' ] ); ?>', function (result) {
        var json = jQuery.parseJSON(result);
        if (typeof json.status !== 'undefined' && json.status === 'ok') {
            window.location.href = json.url;
        }
    });
    return false;
});
</script>
<?php
$cart = WC()->cart;
/** @var \Payone\Gateway\AmazonPayExpress $amazonpay_express_gateway */
$amazonpay_express_gateway = \Payone\Plugin::find_gateway( \Payone\Gateway\AmazonPayExpress::GATEWAY_ID );
if ( $amazonpay_express_gateway->is_available() ) {
    $button_config = $amazonpay_express_gateway->process_create_checkout_session( $cart );
    ?>
    <script src="https://static-eu.payments-amazon.com/checkout.js"></script>
    <script type="text/javascript">
        const amazonPayExpressButton = amazon.Pay.renderButton('#payone-amazonpay-express-button', <?php echo wp_json_encode( $button_config ) ?>);
    </script>
<?php }

/** @var \Payone\Gateway\AmazonPayExpress $amazonpay_express_gateway */
$paypalv2_express_gateway = \Payone\Plugin::find_gateway( \Payone\Gateway\PayPalV2Express::GATEWAY_ID );
if ( $paypalv2_express_gateway->is_available() ) {
    #$button_config = $paypalv2_express_gateway->process_set_checkout_session( $cart );
    ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypalv2_express_gateway->get_payone_client_id(); ?>&merchant-id=<?php echo $paypalv2_express_gateway->get_payone_merchant_id(); ?>&currency=EUR&intent=authorize&locale=de_DE&commit=false&vault=false&disable-funding=card,sepa,bancontact<?php if ( $paypalv2_express_gateway->get_allow_paylater() ) { ?>&enable-funding=paylater<?php } ?>">
    </script>
    <script type="text/javascript">
    paypal.Buttons({
        style: {
        layout: 'vertical',
        color: 'gold',
        shape: 'rect',
        label: 'paypal',
        height: 55
    },
    createOrder: function(data, actions) {
        return fetch('<?php echo Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'express-set-checkout' ] ); ?>', {
            method: 'post'
        }).then(function(res) {
            return res.text();
        }).then(function(orderID) {
            return orderID;
        });
    },
        onApprove: function(data, actions) {
        window.location = '<?php echo Plugin::get_callback_url( [ 'type' => 'paypalv2', 'a' => 'express-get-checkout' ] ); ?>';
    },
        onCancel: function(data, actions) {
    },
        onError: function() {
    },
    }).render('#payone-paypalv2-express-button');
    </script>
<?php }
