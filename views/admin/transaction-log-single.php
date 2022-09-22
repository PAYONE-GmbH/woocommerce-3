<div class="wrap">
    <h1>Transaction Status Logeintrag <?php echo $id; ?></h1>
    <h2>
        DATA (Transaktion <?php echo $entry->get_transaction_id(); ?>)
        <small>(<?php echo $entry->get_created_at()->format( 'd.m.Y H:i' ) ?> Uhr)</small>
    </h2>
    <table class="widefat fixed" cellspacing="0">
        <thead>
        <tr>
            <th>Parameter</th>
            <th>Wert</th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ( $entry->get_data()->get_all() as $key => $value ) { ?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo $value; ?></td>
            </tr>
		<?php } ?>
        </tbody>
    </table>
</div>
