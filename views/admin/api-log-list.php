<div class="wrap">
    <h1>API-Log</h1>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Request</th>
                <th>Response</th>
                <th>Modus</th>
                <th>Merchant-ID</th>
                <th>Portal ID</th>
                <th>Erstelldatum</th>
            </tr>
        </thead>
        <body>
            <?php foreach ($entries as $entry) { ?>
                <tr style="cursor:pointer" onclick="window.location = '?page=payone-api-log&id=<?php echo $entry->getId(); ?>'">
                    <td><?php echo $entry->getId(); ?></td>
                    <td><?php echo $entry->getRequest()->get('request'); ?></td>
                    <td><?php echo $entry->getResponse()->get('status'); ?></td>
                    <td><?php echo $entry->getRequest()->get('mode'); ?></td>
                    <td><?php echo $entry->getRequest()->get('mid'); ?></td>
                    <td><?php echo $entry->getRequest()->get('portalid'); ?></td>
                    <td><?php echo $entry->getCreatedAt()->format('d.m.Y H:i'); ?></td>
                </tr>
            <?php } ?>
        </body>
    </table>
</div>