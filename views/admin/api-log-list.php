<?php
/**
 * @var \Payone\Admin\ApiLogListTable $list_table
 */

$payone_options     = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
$payone_log_enabled = is_array( $payone_options ) && ! empty( $payone_options['api_log'] );

$header_title       = __( 'API-Log', 'payone-woocommerce-3' );
$header_description = __( 'All requests to the PAYONE API and their responses.', 'payone-woocommerce-3' );
$header_badges      = [
	[
		'type'  => $payone_log_enabled ? 'success' : 'neutral',
		'label' => $payone_log_enabled ? __( 'Logging active', 'payone-woocommerce-3' ) : __( 'Logging inactive', 'payone-woocommerce-3' ),
	],
	[
		'type'  => 'neutral',
		'label' => sprintf( _n( '%d entry', '%d entries', $list_table->get_total_items(), 'payone-woocommerce-3' ), $list_table->get_total_items() ),
	],
];
$header_links       = [
	[ 'url' => admin_url( 'admin.php?page=payone-settings-account' ), 'label' => __( 'Global PAYONE settings', 'payone-woocommerce-3' ) ],
];
?>
<div class="wrap payone-admin payone-admin--wide">
    <h1 class="screen-reader-text"><?php echo esc_html( $header_title ); ?></h1>
	<?php include PAYONE_VIEW_PATH . '/admin/partials/page-header.php'; ?>
    <div class="payone-card payone-card--table">
		<?php $list_table->display(); ?>
    </div>
</div>
