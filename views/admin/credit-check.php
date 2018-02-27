<div class="wrap">
	<h1><?php echo __( 'Credit Check', 'payone-woocommerce-3' ); ?></h1>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
		<?php
		settings_fields( 'payone_credit_check' );
		do_settings_sections( 'payone-credit-check' );
		submit_button();
		?>
	</form>
</div>