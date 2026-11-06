<?php
/**
 * @var int $id
 * @var \Payone\Transaction\Log $entry
 */

$payone_data = $entry->get_data();

$header_title       = sprintf( __( 'Transaction status log entry #%d', 'payone-woocommerce-3' ), (int) $id );
$header_description = esc_html( trim( $payone_data->get( 'txaction' ) . ' · ' . __( 'Transaction', 'payone-woocommerce-3' ) . ' ' . $entry->get_transaction_id() ) )
                      . ' &middot; ' . esc_html( $entry->get_created_at()->format( 'd.m.Y H:i' ) );
$header_back        = [ 'url' => admin_url( 'admin.php?page=payone-transaction-log' ), 'label' => __( 'Back to Transaction Status Log', 'payone-woocommerce-3' ) ];
$header_badges      = [];
if ( $payone_data->get( 'mode' ) ) {
	$header_badges[] = [
		'type'  => $payone_data->get( 'mode' ) === 'live' ? 'live' : 'test',
		'label' => $payone_data->get( 'mode' ) === 'live' ? __( 'Live mode', 'payone-woocommerce-3' ) : __( 'Test mode', 'payone-woocommerce-3' ),
	];
}
?>
<div class="wrap payone-admin payone-admin--wide">
    <h1 class="screen-reader-text"><?php echo esc_html( $header_title ); ?></h1>
	<?php include PAYONE_VIEW_PATH . '/admin/partials/page-header.php'; ?>

    <div class="payone-card">
        <div class="payone-card__header payone-card__header--static">
            <span class="payone-card__title"><?php esc_html_e( 'Data', 'payone-woocommerce-3' ); ?></span>
        </div>
        <div class="payone-card__body">
            <table class="payone-kv">
                <tbody>
				<?php foreach ( $payone_data->get_all() as $key => $value ) { ?>
					<?php
					if ( $key === 'key' ) {
						$value = '••••••••';
					}
					?>
                    <tr>
                        <th scope="row"><?php echo esc_html( $key ); ?></th>
                        <td><?php echo esc_html( is_scalar( $value ) ? $value : wp_json_encode( $value ) ); ?></td>
                    </tr>
				<?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
