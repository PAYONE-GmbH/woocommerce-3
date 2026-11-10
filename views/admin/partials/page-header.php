<?php
/**
 * Header of the PAYONE admin pages.
 *
 * @var string $header_title
 * @var string $header_description optional
 * @var array[] $header_badges optional, each [ 'type' => success|error|test|live|neutral, 'label' => string ]
 * @var array $header_back optional, [ 'url' => string, 'label' => string ]
 * @var array[] $header_links optional, each [ 'url' => string, 'label' => string ]
 */
?>
<?php if ( ! empty( $header_back ) ) { ?>
    <p class="payone-admin__back">
        <a href="<?php echo esc_url( $header_back['url'] ); ?>">&larr; <?php echo esc_html( $header_back['label'] ); ?></a>
    </p>
<?php } ?>
<div class="payone-header">
    <img class="payone-header__logo" src="<?php echo esc_url( PAYONE_PLUGIN_URL . 'assets/payone-logo.svg' ); ?>" width="120" height="30" alt="PAYONE">
    <div class="payone-header__text">
        <p class="payone-header__eyebrow">WooCommerce</p>
        <h2 class="payone-header__title"><?php echo esc_html( $header_title ); ?></h2>
		<?php if ( ! empty( $header_description ) ) { ?>
            <p class="payone-header__description"><?php echo wp_kses_post( $header_description ); ?></p>
		<?php } ?>
    </div>
	<?php if ( ! empty( $header_badges ) || ! empty( $header_links ) ) { ?>
        <div class="payone-header__status">
			<?php foreach ( isset( $header_badges ) ? $header_badges : [] as $badge ) { ?>
                <span class="payone-badge payone-badge--<?php echo esc_attr( $badge['type'] ); ?>"><?php echo esc_html( $badge['label'] ); ?></span>
			<?php } ?>
			<?php foreach ( isset( $header_links ) ? $header_links : [] as $link ) { ?>
                <a class="payone-header__link" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
			<?php } ?>
        </div>
	<?php } ?>
</div>
