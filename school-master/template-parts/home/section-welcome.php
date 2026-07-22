<?php
/**
 * Homepage section: Welcome / About.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$title = school_master_option( 'welcome_title', __( 'Welcome to Our Institution', 'school-master' ) );
$text  = school_master_option( 'welcome_text' );
$image = school_master_option( 'welcome_image' );

if ( ! $title && ! $text ) {
	return;
}
?>
<section class="home-section welcome">
	<div class="container welcome__inner">
		<?php if ( $image ) : ?>
			<div class="welcome__media">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			</div>
		<?php endif; ?>
		<div class="welcome__content">
			<?php if ( $title ) : ?>
				<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<div class="welcome__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
