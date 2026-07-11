<?php
/**
 * Homepage section: Partners / Affiliations logo grid.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

if ( ! smcore_has_post_type( 'sm_partner' ) ) {
	return;
}

$title = school_master_option( 'partners_title', __( 'Our Partners', 'school-master' ) );

$partners = new WP_Query(
	array(
		'post_type'      => 'sm_partner',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	)
);

if ( ! $partners->have_posts() ) {
	return;
}
?>
<section class="home-section partners">
	<div class="container">
		<h2 class="section-title section-title--center"><?php echo esc_html( $title ); ?></h2>

		<div class="partner-grid">
			<?php
			while ( $partners->have_posts() ) :
				$partners->the_post();

				if ( ! has_post_thumbnail() ) {
					continue;
				}

				$url  = smcore_get_meta( 'url' );
				$logo = get_the_post_thumbnail( get_the_ID(), 'medium', array( 'loading' => 'lazy', 'alt' => get_the_title() ) );

				if ( $url ) {
					printf(
						'<a class="partner" href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
						esc_url( $url ),
						$logo // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail returns safe markup.
					);
				} else {
					printf( '<span class="partner">%s</span>', $logo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			endwhile;
			?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
