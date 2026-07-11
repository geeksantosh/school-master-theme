<?php
/**
 * Single notice — content plus optional attachment download.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$important  = smcore_get_meta( 'is_important' );
	$attachment = smcore_get_meta( 'attachment' );
	?>
	<main id="main" class="site-main container">
		<div class="content-area content-area--full">
			<article <?php post_class( 'notice-single' ); ?>>
				<header class="page-header">
					<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
					<h1 class="entry-title page-title">
						<?php the_title(); ?>
						<?php if ( $important ) : ?>
							<span class="notice-badge"><?php esc_html_e( 'Important', 'school-master' ); ?></span>
						<?php endif; ?>
					</h1>
				</header>

				<div class="entry-content">
					<?php the_content(); ?>
				</div>

				<?php if ( $attachment ) : ?>
					<p class="notice-attachment">
						<a class="btn btn--secondary" href="<?php echo esc_url( $attachment ); ?>" download>
							<?php esc_html_e( 'Download attachment', 'school-master' ); ?>
						</a>
					</p>
				<?php endif; ?>
			</article>
		</div>
	</main>
	<?php
endwhile;

get_footer();
