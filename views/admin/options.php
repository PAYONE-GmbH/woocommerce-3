<div class="wrap">
<h1>BS PAYONE - Einstellungen</h1>
<form method="post" action="options.php">
    <?php
        settings_fields( 'payone' );
        do_settings_sections( 'payone-settings-account' );
        submit_button();
    ?>
</form>
</div>