<?php
/**
 * Single post template.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main container">
	<div class="content-area">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php
get_footer();
