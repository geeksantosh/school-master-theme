<?php
/**
 * Homepage section: Notice Board.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

// Needs the companion plugin's notice post type.
if ( ! smcore_has_post_type( 'sm_notice' ) ) {
	return;
}

$title = school_master_option( 'notices_title', __( 'Notice Board', 'school-master' ) );
$count = (int) school_master_option( 'notices_count', 5 );

// Shared query: newest first with important notices floated to the top.
$notices = school_master_notices_query( $count );

if ( ! $notices || ! $notices->have_posts() ) {
	return;
}
?>
<section class="home-section notices">
	<div class="container">
		<div class="section-head">
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<a class="section-more" href="<?php echo esc_url( get_post_type_archive_link( 'sm_notice' ) ); ?>"><?php esc_html_e( 'View all', 'school-master' ); ?></a>
		</div>

		<ul class="notice-list">
			<?php
			while ( $notices->have_posts() ) :
				$notices->the_post();
				$important = smcore_get_meta( 'is_important' );
				?>
				<li class="notice-item<?php echo $important ? ' notice-item--important' : ''; ?>">
					<span class="notice-date">
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</span>
					<a class="notice-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<?php if ( $important ) : ?>
						<span class="notice-badge"><?php esc_html_e( 'New', 'school-master' ); ?></span>
					<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>
</section>
<?php
wp_reset_postdata();
