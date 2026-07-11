<?php
/**
 * Notice archive — a chronological list rather than a card grid.
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
			<ul class="notice-list notice-list--archive">
				<?php
				while ( have_posts() ) :
					the_post();
					$important = smcore_get_meta( 'is_important' );
					?>
					<li class="notice-item <?php echo $important ? 'notice-item--important' : ''; ?>">
						<span class="notice-date"><?php echo esc_html( get_the_date() ); ?></span>
						<a class="notice-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php if ( $important ) : ?>
							<span class="notice-badge"><?php esc_html_e( 'Important', 'school-master' ); ?></span>
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
