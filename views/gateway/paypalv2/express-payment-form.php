<?php use Payone\Plugin;

include 'common.php'; ?>
<p>
	<?php echo nl2br( $this->get_option( 'description' ) ); ?>
</p>
<div id="paypalv2_express_error"></div>