<?php
/**
 * Homepage section: Courses / Programs.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

if ( ! smcore_has_post_type( 'sm_course' ) ) {
	return;
}

$title = school_master_option( 'courses_title', __( 'Our Programs', 'school-master' ) );
$count = (int) school_master_option( 'courses_count', 6 );

$courses = new WP_Query(
	array(
		'post_type'      => 'sm_course',
		'posts_per_page' => $count,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	)
);

if ( ! $courses->have_posts() ) {
	return;
}
?>
<section class="home-section courses">
	<div class="container">
		<div class="section-head">
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<a class="section-more" href="<?php echo esc_url( get_post_type_archive_link( 'sm_course' ) ); ?>"><?php esc_html_e( 'All programs', 'school-master' ); ?></a>
		</div>

		<div class="card-grid card-grid--courses">
			<?php
			while ( $courses->have_posts() ) :
				$courses->the_post();
				$duration    = smcore_get_meta( 'duration' );
				$seats       = smcore_get_meta( 'seats' );
				$eligibility = smcore_get_meta( 'eligibility' );
				?>
				<article class="card course-card">
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="card__media" href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'school-master-card', array( 'loading' => 'lazy' ) ); ?>
						</a>
					<?php else : ?>
						<a class="card__media card__media--placeholder" href="<?php the_permalink(); ?>">
							<div class="card__media-placeholder">📚</div>
						</a>
					<?php endif; ?>
					<div class="card__body">
						<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

						<ul class="course-meta">
							<?php if ( $duration ) : ?>
								<li><span class="course-meta__label"><?php esc_html_e( 'Duration', 'school-master' ); ?>:</span> <?php echo esc_html( $duration ); ?></li>
							<?php endif; ?>
							<?php if ( $seats ) : ?>
								<li><span class="course-meta__label"><?php esc_html_e( 'Seats', 'school-master' ); ?>:</span> <?php echo esc_html( $seats ); ?></li>
							<?php endif; ?>
							<?php if ( $eligibility ) : ?>
								<li><span class="course-meta__label"><?php esc_html_e( 'Eligibility', 'school-master' ); ?>:</span> <?php echo esc_html( wp_trim_words( $eligibility, 10 ) ); ?></li>
							<?php endif; ?>
						</ul>

						<a class="card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Learn more', 'school-master' ); ?> &rarr;</a>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
