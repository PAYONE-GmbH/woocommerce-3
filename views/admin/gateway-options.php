<?php
/**
 * Settings page of a PAYONE payment method (WooCommerce > Settings > Payments).
 *
 * @var \Payone\Gateway\GatewayBase $gateway
 * @var array[] $sections
 * @var bool|null $connection_ok null if the payment method has no connection test
 */

$is_live    = $gateway->get_mode() === 'live';
$is_enabled = $gateway->enabled === 'yes';
?>
<div class="payone-admin">
    <p class="payone-admin__back">
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ); ?>">&larr; <?php esc_html_e( 'Back to payments', 'payone-woocommerce-3' ); ?></a>
    </p>

    <div class="payone-header">
		<?php if ( $gateway->icon ) { ?>
            <div class="payone-header__icon"><img src="<?php echo esc_url( $gateway->icon ); ?>" alt=""></div>
		<?php } ?>
        <div class="payone-header__text">
            <p class="payone-header__eyebrow"><img class="payone-header__brand" src="<?php echo esc_url( PAYONE_PLUGIN_URL . 'assets/payone-logo.svg' ); ?>" width="80" height="20" alt="PAYONE"></p>
            <h2 class="payone-header__title"><?php echo esc_html( $gateway->get_method_title() ); ?></h2>
			<?php if ( $gateway->get_method_description() ) { ?>
                <p class="payone-header__description"><?php echo wp_kses_post( $gateway->get_method_description() ); ?></p>
			<?php } ?>
        </div>
        <div class="payone-header__status">
            <span class="payone-badge <?php echo $is_enabled ? 'payone-badge--success' : 'payone-badge--neutral'; ?>">
                <?php echo $is_enabled ? esc_html__( 'Active', 'payone-woocommerce-3' ) : esc_html__( 'Inactive', 'payone-woocommerce-3' ); ?>
            </span>
            <span class="payone-badge <?php echo $is_live ? 'payone-badge--live' : 'payone-badge--test'; ?>">
                <?php echo $is_live ? esc_html__( 'Live mode', 'payone-woocommerce-3' ) : esc_html__( 'Test mode', 'payone-woocommerce-3' ); ?>
            </span>
			<?php if ( $connection_ok !== null ) { ?>
                <span class="payone-badge <?php echo $connection_ok ? 'payone-badge--success' : 'payone-badge--error'; ?>">
                    <?php echo $connection_ok ? esc_html__( 'Connection OK', 'payone-woocommerce-3' ) : esc_html__( 'No connection', 'payone-woocommerce-3' ); ?>
                </span>
			<?php } ?>
            <a class="payone-header__link" href="<?php echo esc_url( admin_url( 'admin.php?page=payone-settings-account' ) ); ?>">
				<?php esc_html_e( 'Global PAYONE settings', 'payone-woocommerce-3' ); ?>
            </a>
        </div>
    </div>

	<?php if ( $connection_ok === false ) { ?>
        <div class="payone-alert payone-alert--error">
            <strong><?php esc_html_e( 'Connection to PAYONE API failed', 'payone-woocommerce-3' ); ?></strong>
            <span><?php esc_html_e( 'The payment method was deactivated. Please check the PAYONE account data.', 'payone-woocommerce-3' ); ?></span>
        </div>
	<?php } ?>

	<?php foreach ( $sections as $section_id => $section ) { ?>
        <details class="payone-card" id="payone-<?php echo esc_attr( $section_id ); ?>" <?php echo $section['collapsed'] ? '' : 'open'; ?>>
            <summary class="payone-card__header">
                <span class="payone-card__title"><?php echo esc_html( $section['title'] ); ?></span>
				<?php if ( $section['description'] ) { ?>
                    <span class="payone-card__description"><?php echo wp_kses_post( $section['description'] ); ?></span>
				<?php } ?>
            </summary>
            <div class="payone-card__body">
                <table class="form-table">
					<?php $gateway->generate_settings_html( $section['fields'] ); ?>
                </table>
            </div>
        </details>
	<?php } ?>
</div>
