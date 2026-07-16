<?php
/**
 * Course card for the course archive.
 *
 * Single courses use single-sm_course.php, so this partial only needs to
 * cover the archive/loop card.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$duration = smcore_get_meta( 'duration' );
$seats    = smcore_get_meta( 'seats' );
$fee      = smcore_get_meta( 'fee' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card course-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="card__media" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'school-master-card', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php else : ?>
		<a class="card__media card__media--placeholder" href="<?php the_permalink(); ?>">
			<div class="card__media-placeholder">📚</div>
		</a>
	<?php endif; ?>

	<div class="card__body">
		<?php the_title( '<h2 class="card__title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>

		<?php if ( $duration || $seats || $fee ) : ?>
			<ul class="course-meta">
				<?php if ( $duration ) : ?>
					<li><span class="course-meta__label"><?php esc_html_e( 'Duration:', 'school-master' ); ?></span> <?php echo esc_html( $duration ); ?></li>
				<?php endif; ?>
				<?php if ( $seats ) : ?>
					<li><span class="course-meta__label"><?php esc_html_e( 'Seats:', 'school-master' ); ?></span> <?php echo esc_html( $seats ); ?></li>
				<?php endif; ?>
				<?php if ( $fee ) : ?>
					<li><span class="course-meta__label"><?php esc_html_e( 'Fee:', 'school-master' ); ?></span> <?php echo esc_html( $fee ); ?></li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

		<a class="card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View course', 'school-master' ); ?> &rarr;</a>
		<?php school_master_course_actions( 'btn--sm' ); ?>
	</div>
</article>
