<?php
/**
 * Homepage section: Campus video.
 *
 * Separate from the hero's video mode, which swaps out the hero image. This
 * lets a school keep its best photo up top and still feature a video tour.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$embed = school_master_youtube_embed_url( school_master_option( 'video_url' ) );

if ( ! $embed ) {
	return;
}

$title = school_master_option( 'video_title', __( 'Campus Life', 'school-master' ) );
$text  = school_master_option( 'video_text' );
?>
<section class="home-section video-section">
	<div class="container">
		<?php if ( $title ) : ?>
			<h2 class="section-title section-title--center"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="video-section__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<div class="video-section__player">
			<div class="video-responsive">
				<iframe src="<?php echo esc_url( $embed ); ?>" title="<?php echo esc_attr( $title ? $title : __( 'Campus video', 'school-master' ) ); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
			</div>
		</div>
	</div>
</section>
