<div class="wrap">
    <h1>Transaction Status Logeintrag <?php echo $id; ?></h1>
    <h2>DATA (Transaktion <?php echo $entry->getTransactionId(); ?>)</h2>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Wert</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entry->getData()->get_all() as $key => $value) { ?>
                <tr>
                    <td><?php echo $key; ?></td>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>