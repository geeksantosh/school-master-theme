<?php
/**
 * Default content template for posts in a loop.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card post-card' ); ?>>
	<?php if ( has_post_thumbnail() && ! is_singular() ) : ?>
		<a class="card__media" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'school-master-card', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>

	<div class="card__body">
		<header class="entry-header">
			<?php
			if ( is_singular() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( '<h2 class="entry-title card__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}
			?>
			<div class="entry-meta"><?php school_master_posted_on(); ?></div>
		</header>

		<div class="entry-content">
			<?php
			if ( is_singular() ) {
				the_content();
				wp_link_pages();
			} else {
				echo '<p>' . esc_html( wp_trim_words( get_the_excerpt(), 24 ) ) . '</p>';
				printf( '<a class="card__link" href="%s">%s &rarr;</a>', esc_url( get_permalink() ), esc_html__( 'Read more', 'school-master' ) );
			}
			?>
		</div>
	</div>
</article>
