<div id="payone-amazonpay-button"></div>
<script src="https://static-eu.payments-amazon.com/checkout.js"></script>
<script type="text/javascript" charset="utf-8">
    var amazonPayButton = amazon.Pay.renderButton('#payone-amazonpay-button', <?php echo wp_json_encode( $button_config ) ?>);
    document.getElementById('payone-amazonpay-button').click();
</script>