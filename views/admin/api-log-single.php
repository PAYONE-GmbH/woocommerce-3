<div class="wrap">
    <h1>API-Logeintrag <?php echo $id; ?></h1>
    <h2>REQUEST</h2>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Wert</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entry->get_request()->get_all() as $key => $value) { ?>
                <tr>
                    <td><?php echo esc_html( $key ); ?></td>
                    <td><?php echo esc_html( $value ); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <h2>RESPONSE</h2>
    <table class="widefat fixed" cellspacing="0">
        <thead>
        <tr>
            <th>Parameter</th>
            <th>Wert</th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($entry->get_response()->get_all() as $key => $value) { ?>
            <?php if ( $key === '_DATA' ) {
                $value = substr( $value, 0, 50 ) . ' [...]';
            } ?>
            <tr>
                <td><?php echo esc_html( $key ); ?></td>
                <td><?php echo esc_html( $value ); ?></td>
            </tr>
		<?php } ?>
        </tbody>
    </table>
</div>