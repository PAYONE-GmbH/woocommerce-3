<div class="wrap">
    <h1><?php _e( 'Address Checks', 'payone-woocommerce-3' ); ?></h1>
	<?php settings_errors(); ?>
    <form method="post" action="options.php">
		<?php
		settings_fields( 'payone_address_checks' );
		do_settings_sections( 'payone-address-checks' );
		submit_button();
		?>
    </form>
</div>
