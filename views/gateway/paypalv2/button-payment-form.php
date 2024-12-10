<div id="payone-paypalv2express-button"></div>
<script src="https://www.paypal.com/sdk/js?client-id=AUn5n-4qxBUkdzQBv6f8yd8F4AWdEvV6nLzbAifDILhKGCjOS62qQLiKbUbpIKH_O2Z3OL8CvX7ucZfh&merchant-id=3QK84QGGJE5HW&currency=EUR&intent=authorize&locale=de_DE&commit=true&vault=false&disable-funding=card,sepa,bancontact&enable-funding=paylater">
<script type="text/javascript">
    paypal.Buttons(<?php echo wp_json_encode( $button_config ) ?>).render('#payone-paypalv2express-button');
    document.getElementById('payone-paypalv2express-button').click();
</script>