<?php
/**
 * Archive template — used for CPT archives, categories, tags, dates.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main container">
	<div class="content-area content-area--full">
		<header class="page-header">
			<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="archive-description">', '</div>' );
			?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="card-grid card-grid--archive">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
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
