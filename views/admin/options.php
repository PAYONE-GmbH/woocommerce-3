<div class="wrap">
    <h1>Payone - Einstellungen</h1>
	<?php settings_errors(); ?>
    <form method="post" action="options.php">
		<?php
		settings_fields( 'payone' );
		do_settings_sections( 'payone-settings-account' );
		submit_button();
		?>
    </form>
</div>
