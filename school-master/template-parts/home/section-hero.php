<?php
/**
 * Homepage section: Hero (background image or YouTube video).
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$type     = school_master_option( 'hero_type', 'image' );
$title    = school_master_option( 'hero_title', __( 'Welcome to Our Campus', 'school-master' ) );
$subtitle = school_master_option( 'hero_subtitle' );
$btn_text = school_master_option( 'hero_btn_text' );
$btn_url  = school_master_option( 'hero_btn_url' );
$image    = school_master_option( 'hero_image' );
$video    = school_master_option( 'hero_video_url' );

$style = ( 'image' === $type && $image ) ? sprintf( ' style="background-image:url(%s)"', esc_url( $image ) ) : '';
?>
<section class="home-section hero hero--<?php echo esc_attr( $type ); ?>"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>>
	<div class="hero__overlay"></div>
	<div class="container hero__inner">
		<div class="hero__content">
			<?php if ( $title ) : ?>
				<h1 class="hero__title"><?php echo esc_html( $title ); ?></h1>
			<?php endif; ?>

			<?php if ( $subtitle ) : ?>
				<p class="hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>

			<?php if ( $btn_text && $btn_url ) : ?>
				<a class="btn btn--primary hero__btn" href="<?php echo esc_url( $btn_url ); ?>"><?php echo esc_html( $btn_text ); ?></a>
			<?php endif; ?>
		</div>

		<?php if ( 'video' === $type && $video ) : ?>
			<?php $embed = school_master_youtube_embed_url( $video ); ?>
			<?php if ( $embed ) : ?>
				<div class="hero__video">
					<div class="video-responsive">
						<iframe src="<?php echo esc_url( $embed ); ?>" title="<?php esc_attr_e( 'Intro video', 'school-master' ); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
