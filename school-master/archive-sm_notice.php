<?php
/**
 * Notice archive — a table of notices with publish time and file actions.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sm_columns = array(
	'topic'   => __( 'Notice Topic Information', 'school-master' ),
	'date'    => __( 'Published Time / Date', 'school-master' ),
	'actions' => __( 'File Actions', 'school-master' ),
);

$sm_calendar_icon = '<svg class="notice-row__icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M7 2v2H5a2 2 0 00-2 2v13a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-2V2h-2v2H9V2H7zm12 8v9H5v-9h14z"/></svg>';
?>
<main id="main" class="site-main container">
	<div class="content-area content-area--full">
		<header class="page-header">
			<h1 class="page-title"><?php post_type_archive_title(); ?></h1>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="notice-table__wrap">
				<table class="notice-table">
					<thead>
						<tr>
							<?php foreach ( $sm_columns as $sm_key => $sm_label ) : ?>
								<th scope="col" class="notice-table__col notice-table__col--<?php echo esc_attr( $sm_key ); ?>">
									<?php echo esc_html( $sm_label ); ?>
								</th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						while ( have_posts() ) :
							the_post();

							$sm_important = smcore_get_meta( 'is_important' );
							$sm_file      = school_master_attachment_info( smcore_get_meta( 'attachment' ) );
							?>
							<tr class="notice-row <?php echo $sm_important ? 'notice-row--important' : ''; ?>">
								<td class="notice-row__topic" data-label="<?php echo esc_attr( $sm_columns['topic'] ); ?>">
									<?php if ( $sm_file ) : ?>
										<span class="notice-row__type notice-row__type--<?php echo esc_attr( $sm_file['ext'] ? $sm_file['ext'] : 'none' ); ?>">
											<?php echo esc_html( $sm_file['label'] ); ?>
										</span>
									<?php else : ?>
										<span class="notice-row__type notice-row__type--none" aria-hidden="true">&mdash;</span>
									<?php endif; ?>

									<span class="notice-row__title">
										<a class="notice-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										<?php if ( $sm_important ) : ?>
											<span class="notice-badge"><?php esc_html_e( 'Important', 'school-master' ); ?></span>
										<?php endif; ?>
										<?php if ( $sm_file && $sm_file['size'] ) : ?>
											<span class="notice-row__size">(<?php echo esc_html( $sm_file['size'] ); ?>)</span>
										<?php endif; ?>
									</span>
								</td>

								<td class="notice-row__published" data-label="<?php echo esc_attr( $sm_columns['date'] ); ?>">
									<?php echo $sm_calendar_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static, safe SVG. ?>
									<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
										<?php
										printf(
											/* translators: 1: publish date, 2: publish time. */
											esc_html__( '%1$s at %2$s', 'school-master' ),
											esc_html( get_the_date() ),
											esc_html( get_the_time() )
										);
										?>
									</time>
								</td>

								<td class="notice-row__actions" data-label="<?php echo esc_attr( $sm_columns['actions'] ); ?>">
									<?php if ( $sm_file ) : ?>
										<a class="btn btn--primary notice-row__btn" href="<?php echo esc_url( $sm_file['url'] ); ?>" target="_blank" rel="noopener">
											<?php esc_html_e( 'View / Download', 'school-master' ); ?>
											<span class="screen-reader-text"><?php the_title(); ?></span>
										</a>
									<?php else : ?>
										<a class="btn btn--outline notice-row__btn" href="<?php the_permalink(); ?>">
											<?php esc_html_e( 'Read Notice', 'school-master' ); ?>
											<span class="screen-reader-text"><?php the_title(); ?></span>
										</a>
									<?php endif; ?>
								</td>
							</tr>
							<?php
						endwhile;
						?>
					</tbody>
				</table>
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
