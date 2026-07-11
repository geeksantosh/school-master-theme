<?php
/**
 * Homepage section: Latest News (standard blog posts).
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$title = school_master_option( 'news_title', __( 'Latest News', 'school-master' ) );

// "All news" can only point somewhere real when a Posts page is set in
// Settings → Reading; with no page, get_permalink(0) would resolve to the
// current global post instead, so hide the link entirely.
$posts_page_id = (int) get_option( 'page_for_posts' );
$all_news_url  = $posts_page_id ? get_permalink( $posts_page_id ) : '';

$news = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'ignore_sticky_posts' => true,
	)
);

if ( ! $news->have_posts() ) {
	return;
}
?>
<section class="home-section news">
	<div class="container">
		<div class="section-head">
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $all_news_url ) : ?>
				<a class="section-more" href="<?php echo esc_url( $all_news_url ); ?>"><?php esc_html_e( 'All news', 'school-master' ); ?></a>
			<?php endif; ?>
		</div>

		<div class="card-grid card-grid--news">
			<?php
			while ( $news->have_posts() ) :
				$news->the_post();
				?>
				<article class="card news-card">
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="card__media" href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'school-master-card', array( 'loading' => 'lazy' ) ); ?>
						</a>
					<?php endif; ?>
					<div class="card__body">
						<span class="card__date"><?php echo esc_html( get_the_date() ); ?></span>
						<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
