<?php
/**
 * Global PAYONE settings.
 *
 * @var array $testable_gateways gateway id => name
 * @var bool $is_live
 * @var bool $is_complete
 */

global $wp_settings_sections;
$payone_sections = isset( $wp_settings_sections['payone-settings-account'] ) ? $wp_settings_sections['payone-settings-account'] : [];
?>
<div class="wrap payone-admin">
    <h1 class="screen-reader-text"><?php esc_html_e( 'PAYONE Settings', 'payone-woocommerce-3' ); ?></h1>

    <div class="payone-header">
        <img class="payone-header__logo" src="<?php echo esc_url( PAYONE_PLUGIN_URL . 'assets/payone-logo.svg' ); ?>" width="120" height="30" alt="PAYONE">
        <div class="payone-header__text">
            <p class="payone-header__eyebrow">WooCommerce</p>
            <h2 class="payone-header__title"><?php esc_html_e( 'Settings', 'payone-woocommerce-3' ); ?></h2>
            <p class="payone-header__description">
				<?php esc_html_e( 'These settings apply to all PAYONE payment methods that use the global settings.', 'payone-woocommerce-3' ); ?>
            </p>
        </div>
        <div class="payone-header__status">
            <span class="payone-badge <?php echo $is_live ? 'payone-badge--live' : 'payone-badge--test'; ?>">
                <?php echo $is_live ? esc_html__( 'Live mode', 'payone-woocommerce-3' ) : esc_html__( 'Test mode', 'payone-woocommerce-3' ); ?>
            </span>
            <span class="payone-badge <?php echo $is_complete ? 'payone-badge--success' : 'payone-badge--error'; ?>">
                <?php echo $is_complete ? esc_html__( 'Account data complete', 'payone-woocommerce-3' ) : esc_html__( 'Account data incomplete', 'payone-woocommerce-3' ); ?>
            </span>
            <a class="payone-header__link" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ); ?>">
				<?php esc_html_e( 'Payment methods', 'payone-woocommerce-3' ); ?>
            </a>
        </div>
    </div>

	<?php settings_errors(); ?>

    <form method="post" action="options.php">
		<?php settings_fields( 'payone' ); ?>

		<?php foreach ( $payone_sections as $section ) { ?>
            <details class="payone-card" id="<?php echo esc_attr( $section['id'] ); ?>" open>
                <summary class="payone-card__header">
                    <span class="payone-card__title"><?php echo esc_html( $section['title'] ); ?></span>
                </summary>
                <div class="payone-card__body">
					<?php
					if ( $section['callback'] ) {
						call_user_func( $section['callback'], $section );
					}
					?>
                    <table class="form-table" role="presentation">
						<?php do_settings_fields( 'payone-settings-account', $section['id'] ); ?>
                    </table>
                </div>
            </details>
		<?php } ?>

		<?php submit_button(); ?>
    </form>

	<?php if ( count( $testable_gateways ) > 0 ) { ?>
        <details class="payone-card" open>
            <summary class="payone-card__header">
                <span class="payone-card__title"><?php esc_html_e( 'Test API settings', 'payone-woocommerce-3' ); ?></span>
                <span class="payone-card__description"><?php esc_html_e( 'The following payment methods are enabled and can be tested:', 'payone-woocommerce-3' ); ?></span>
            </summary>
            <div class="payone-card__body">
                <ul class="payone-test" id="payone-connection-results">
					<?php foreach ( $testable_gateways as $gateway_id => $gateway_name ) { ?>
                        <li data-gateway="<?php echo esc_attr( $gateway_id ); ?>">
                            <strong><?php echo esc_html( $gateway_name ); ?></strong>
                            <span class="payone-test__status"></span>
                        </li>
					<?php } ?>
                </ul>
            </div>
            <div class="payone-card__footer">
                <button type="button" class="button button-secondary payone-connection-test"
                        data-url="<?php echo esc_url( \Payone\Plugin::get_callback_url( [ 'type' => 'ajax-test-api-settings' ] ) ); ?>"
                        data-running="<?php esc_attr_e( 'Running tests...', 'payone-woocommerce-3' ); ?>"
                        data-failed="<?php esc_attr_e( 'failed', 'payone-woocommerce-3' ); ?>">
					<?php esc_html_e( 'Run tests', 'payone-woocommerce-3' ); ?>
                </button>
            </div>
        </details>
	<?php } ?>
</div>
