<div class="wrap">
    <h1>Transaction Status Log</h1>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vorgangsnummer</th>
                <th>Bestellnummer</th>
                <th>Status</th>
                <th>Vorgangsdatum</th>
                <th>Sequenznummer</th>
                <th>Zahlart</th>
                <th>Betriebsmodus</th>
                <th>Portal-ID</th>
                <th>Forderung</th>
                <th>Saldo</th>
                <th>Erstelldatum</th>
                <th>Aktualisiert</th>
            </tr>
        </thead>
        <body>
            <?php foreach ($entries as $entry) { ?>
                <tr style="cursor:pointer" onclick="window.location = '?page=payone-transaction-log&id=<?php echo $entry->getId(); ?>'">
                    <td><?php echo $entry->getId(); ?></td>
                    <td><?php echo $entry->getTransactionId(); ?></td>
                    <td></td>
                    <td><?php echo $entry->getData()->get('status'); ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?php echo $entry->getData()->get('mode'); ?></td>
                    <td><?php echo $entry->getData()->get('portalid'); ?></td>
                    <td></td>
                    <td></td>
                    <td><?php echo $entry->getCreatedAt()->format('d.m.Y H:i'); ?></td>
                    <td></td>
                </tr>
            <?php } ?>
        </body>
    </table>
</div>