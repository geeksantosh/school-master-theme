<?php
/**
 * Single course template, surfacing the course meta fields.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$duration    = smcore_get_meta( 'duration' );
	$seats       = smcore_get_meta( 'seats' );
	$fee         = smcore_get_meta( 'fee' );
	$eligibility = smcore_get_meta( 'eligibility' );
	?>
	<main id="main" class="site-main container">
		<div class="content-area content-area--full">
			<article <?php post_class( 'course-single' ); ?>>
				<header class="page-header">
					<h1 class="entry-title page-title"><?php the_title(); ?></h1>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="course-single__media"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>

				<?php if ( $duration || $seats || $fee || $eligibility ) : ?>
					<div class="course-facts">
						<?php if ( $duration ) : ?>
							<div class="course-fact"><span class="course-fact__label"><?php esc_html_e( 'Duration', 'school-master' ); ?></span><span class="course-fact__value"><?php echo esc_html( $duration ); ?></span></div>
						<?php endif; ?>
						<?php if ( $seats ) : ?>
							<div class="course-fact"><span class="course-fact__label"><?php esc_html_e( 'Seats', 'school-master' ); ?></span><span class="course-fact__value"><?php echo esc_html( $seats ); ?></span></div>
						<?php endif; ?>
						<?php if ( $fee ) : ?>
							<div class="course-fact"><span class="course-fact__label"><?php esc_html_e( 'Fee', 'school-master' ); ?></span><span class="course-fact__value"><?php echo esc_html( $fee ); ?></span></div>
						<?php endif; ?>
						<?php if ( $eligibility ) : ?>
							<div class="course-fact course-fact--wide"><span class="course-fact__label"><?php esc_html_e( 'Eligibility', 'school-master' ); ?></span><span class="course-fact__value"><?php echo esc_html( $eligibility ); ?></span></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		</div>
	</main>
	<?php
endwhile;

get_footer();
