<?php
/**
 * Gallery archive — a grid of featured images linking to each gallery item.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main container">
	<div class="content-area content-area--full">
		<header class="page-header">
			<h1 class="page-title"><?php post_type_archive_title(); ?></h1>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="gallery-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					if ( ! has_post_thumbnail() ) {
						continue;
					}
					?>
					<a class="gallery-grid__item" href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'school-master-card', array( 'loading' => 'lazy' ) ); ?>
						<span class="gallery-grid__caption"><?php the_title(); ?></span>
					</a>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'prev_text' => __( 'Previous', 'school-master' ),
					'next_text' => __( 'Next', 'school-master' ),
				)
			);
			?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
