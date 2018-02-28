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
            <?php foreach ($entry->get_request()->getAll() as $key => $value) { ?>
                <tr>
                    <td><?php echo $key; ?></td>
                    <td><?php echo $value; ?></td>
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
		<?php foreach ($entry->getResponse()->getAll() as $key => $value) { ?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo $value; ?></td>
            </tr>
		<?php } ?>
        </tbody>
    </table>
</div>