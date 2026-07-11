<?php
/**
 * Homepage section: Testimonials.
 *
 * Shows what students, alumni and parents say. Each card pulls the quote
 * from the Testimonial's content, the name from its title, the role from
 * the "Author Role" field, and an optional photo from the featured image.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

if ( ! smcore_has_post_type( 'sm_testimonial' ) ) {
	return;
}

$title = school_master_option( 'testimonials_title', __( 'What People Say', 'school-master' ) );
$count = (int) school_master_option( 'testimonials_count', 3 );

$testimonials = new WP_Query(
	array(
		'post_type'      => 'sm_testimonial',
		'posts_per_page' => $count,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);

if ( ! $testimonials->have_posts() ) {
	return;
}
?>
<section class="home-section testimonials">
	<div class="container">
		<h2 class="section-title section-title--center"><?php echo esc_html( $title ); ?></h2>

		<div class="card-grid card-grid--testimonials">
			<?php
			while ( $testimonials->have_posts() ) :
				$testimonials->the_post();
				$role = smcore_get_meta( 'author_role' );
				?>
				<article class="card testimonial">
					<blockquote class="testimonial__quote"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></blockquote>

					<div class="testimonial__author">
						<?php if ( has_post_thumbnail() ) : ?>
							<span class="testimonial__avatar">
								<?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
							</span>
						<?php endif; ?>
						<span class="testimonial__meta">
							<span class="testimonial__name"><?php the_title(); ?></span>
							<?php if ( $role ) : ?>
								<span class="testimonial__role"><?php echo esc_html( $role ); ?></span>
							<?php endif; ?>
						</span>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
