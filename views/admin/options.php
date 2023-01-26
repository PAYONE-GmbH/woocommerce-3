<div class="wrap">
    <h1>
        Payone - Einstellungen
        <?php if ( count( $testable_gateways ) > 0 ) { ?>
            <a href="#TB_inline?&width=400&height=300&inlineId=payone-modal-test-api-settings" class="button thickbox">
                <?php _e( 'Test API settings', 'payone-woocommerce-3'); ?>
            </a>
        <?php } ?>
    </h1>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
		<?php
		settings_fields( 'payone' );
		do_settings_sections( 'payone-settings-account' );
		submit_button();
		?>
    </form>
</div>

<div id="payone-modal-test-api-settings" style="display:none;">
    <p>
		<?php _e( 'The following payment methods are enabled and can be tested:', 'payone-woocommerce-3' ); ?>
    </p>
    <form onsubmit="return payone_test_api_settings( event );">
        <ul>
            <?php foreach ( $testable_gateways as $gateway_id => $gateway_name ) { ?>
                <li>
                    <strong><?php echo $gateway_name; ?></strong>
                    <span id="gw-status-<?php echo $gateway_id; ?>">
                </li>
            <?php } ?>
        </ul>
        <p id="payone_test_api_settings_submit_button" class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Run tests', 'payone-woocommerce-3' ); ?>">
        </p>
    </form>
</div>

<script>
    function payone_test_api_settings( e ) {
        e.preventDefault();

        var testable_gateways = <?php echo json_encode( $testable_gateways ); ?>;
        jQuery( '#payone_test_api_settings_submit_button' ).html( '<strong><?php _e( 'Running tests...', 'payone-woocommerce-3' ) ?></strong>' );

        var ajax_url = '<?php echo \Payone\Plugin::get_callback_url( [ 'type' => 'ajax-test-api-settings' ] ); ?>';

        var num_gateways_to_test = Object.keys( testable_gateways ).length;
        for ( gateway_id in testable_gateways ) {
            jQuery('#gw-status-' + gateway_id ).text(' is being tested...');

            jQuery.post(ajax_url, {gateway_id: gateway_id}, function (result) {
                var json = jQuery.parseJSON( result );
                var message = ( json.result ? '<span style="color:green">' : '<span style="color:red">' ) + json.message + '</span>';
                jQuery('#gw-status-' + json.gateway_id ).html( message );
                num_gateways_to_test--;
                if ( num_gateways_to_test < 1 ) {
                    jQuery( '#payone_test_api_settings_submit_button' ).html( '<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Re-Run tests', 'payone-woocommerce-3' ); ?>">' );

                }
            });
        }

        return false;
    }
</script>
