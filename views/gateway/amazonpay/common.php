<script>
    function payone_checkout_clicked_amazonpay_common() {
        jQuery('#amazonpay_express_error').html('');
        jQuery('#amazonpay_error').html('');
        var message = '';
        if (jQuery('#billing_phone').val() === '') {
            message = '<?php _e('Please enter your phone number!', 'payone-woocommerce-3'); ?><br>';
            jQuery('#amazonpay_express_error').html('<strong style="color:red">' + message + '</strong>');
            jQuery('#amazonpay_error').html('<strong style="color:red">' + message + '</strong>');
        }

        payone_unblock();
        return message.length === 0;
    }
</script>