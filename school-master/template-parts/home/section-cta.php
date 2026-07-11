<?php
/**
 * Homepage section: Call to action band.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$title    = school_master_option( 'cta_title' );
$btn_text = school_master_option( 'cta_btn_text' );
$btn_url  = school_master_option( 'cta_btn_url' );

if ( ! $title ) {
	return;
}
?>
<section class="home-section cta">
	<div class="container cta__inner">
		<h2 class="cta__title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $btn_text && $btn_url ) : ?>
			<a class="btn btn--secondary cta__btn" href="<?php echo esc_url( $btn_url ); ?>"><?php echo esc_html( $btn_text ); ?></a>
		<?php endif; ?>
	</div>
</section>
