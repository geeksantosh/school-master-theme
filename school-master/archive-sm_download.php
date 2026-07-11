<?php
/**
 * Downloads archive — a list of files with download buttons.
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
			<ul class="download-list">
				<?php
				while ( have_posts() ) :
					the_post();
					$file = smcore_get_meta( 'file' );
					?>
					<li class="download-item">
						<span class="download-item__title"><?php the_title(); ?></span>
						<?php if ( $file ) : ?>
							<a class="btn btn--primary download-item__btn" href="<?php echo esc_url( $file ); ?>" download>
								<?php esc_html_e( 'Download', 'school-master' ); ?>
							</a>
						<?php endif; ?>
					</li>
					<?php
				endwhile;
				?>
			</ul>

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
