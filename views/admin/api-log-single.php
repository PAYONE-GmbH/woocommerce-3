<?php
/**
 * @var int $id
 * @var \Payone\Payone\Api\Log $entry
 */

$payone_request  = $entry->get_request();
$payone_response = $entry->get_response();
$payone_error    = $payone_response->get( 'errormessage' );

$header_title       = sprintf( __( 'API log entry #%d', 'payone-woocommerce-3' ), (int) $id );
$header_description = trim( $payone_request->get( 'request' ) . ' ' . $payone_request->get( 'clearingtype' ) )
                      . ' &middot; ' . esc_html( $entry->get_created_at()->format( 'd.m.Y H:i' ) );
$header_back        = [ 'url' => admin_url( 'admin.php?page=payone-api-log' ), 'label' => __( 'Back to API-Log', 'payone-woocommerce-3' ) ];
$header_badges      = [];
if ( $payone_response->get( 'status' ) ) {
	$header_badges[] = [
		'type'  => in_array( $payone_response->get( 'status' ), [ 'APPROVED', 'OK' ], true ) ? 'success'
			: ( $payone_response->get( 'status' ) === 'ERROR' ? 'error' : 'neutral' ),
		'label' => $payone_response->get( 'status' ),
	];
}
if ( $payone_request->get( 'mode' ) ) {
	$header_badges[] = [
		'type'  => $payone_request->get( 'mode' ) === 'live' ? 'live' : 'test',
		'label' => $payone_request->get( 'mode' ) === 'live' ? __( 'Live mode', 'payone-woocommerce-3' ) : __( 'Test mode', 'payone-woocommerce-3' ),
	];
}

$payone_tables = [
	__( 'Request', 'payone-woocommerce-3' )  => $payone_request->get_all(),
	__( 'Response', 'payone-woocommerce-3' ) => $payone_response->get_all(),
];
?>
<div class="wrap payone-admin payone-admin--wide">
    <h1 class="screen-reader-text"><?php echo esc_html( $header_title ); ?></h1>
	<?php include PAYONE_VIEW_PATH . '/admin/partials/page-header.php'; ?>

	<?php if ( $payone_error ) { ?>
        <div class="payone-alert payone-alert--error">
            <strong><?php echo esc_html( $payone_response->get( 'errorcode' ) ? '#' . $payone_response->get( 'errorcode' ) : 'ERROR' ); ?></strong>
            <span><?php echo esc_html( $payone_error ); ?></span>
			<?php if ( $payone_response->get( 'customermessage' ) && $payone_response->get( 'customermessage' ) !== $payone_error ) { ?>
                <span class="payone-muted">(<?php echo esc_html( $payone_response->get( 'customermessage' ) ); ?>)</span>
			<?php } ?>
        </div>
	<?php } ?>

    <div class="payone-grid">
		<?php foreach ( $payone_tables as $payone_table_title => $payone_rows ) { ?>
            <div class="payone-card">
                <div class="payone-card__header payone-card__header--static">
                    <span class="payone-card__title"><?php echo esc_html( $payone_table_title ); ?></span>
                </div>
                <div class="payone-card__body">
                    <table class="payone-kv">
                        <tbody>
						<?php foreach ( $payone_rows as $key => $value ) { ?>
							<?php
							if ( $key === 'key' ) {
								$value = '••••••••';
							} elseif ( $key === '_DATA' ) {
								$value = substr( $value, 0, 50 ) . ' [...]';
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
		<?php } ?>
    </div>
</div>
