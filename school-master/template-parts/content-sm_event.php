<?php
/**
 * Event content — card in the archive, full details when singular.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$start    = smcore_get_meta( 'start_date' );
$end      = smcore_get_meta( 'end_date' );
$location = smcore_get_meta( 'location' );

// Pre-format the date badge from the start date (falls back to publish date).
$badge_ts = $start ? strtotime( $start ) : get_post_time( 'U', true );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card event-card' ); ?>>
	<?php if ( $badge_ts ) : ?>
		<div class="event-card__date" aria-hidden="true">
			<span class="event-card__day"><?php echo esc_html( wp_date( 'd', $badge_ts ) ); ?></span>
			<span class="event-card__month"><?php echo esc_html( wp_date( 'M', $badge_ts ) ); ?></span>
		</div>
	<?php endif; ?>

	<div class="card__body">
		<?php
		if ( is_singular() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			the_title( '<h2 class="card__title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
		}
		?>

		<ul class="event-meta">
			<?php if ( $start ) : ?>
				<li class="event-meta__when">
					<?php
					echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $start ) ) );
					if ( $end && $end !== $start ) {
						echo ' &ndash; ' . esc_html( wp_date( get_option( 'date_format' ), strtotime( $end ) ) );
					}
					?>
				</li>
			<?php endif; ?>
			<?php if ( $location ) : ?>
				<li class="event-meta__where"><?php echo esc_html( $location ); ?></li>
			<?php endif; ?>
		</ul>

		<div class="entry-content">
			<?php
			if ( is_singular() ) {
				the_content();
			} else {
				echo '<p>' . esc_html( wp_trim_words( get_the_excerpt(), 20 ) ) . '</p>';
				printf( '<a class="card__link" href="%s">%s &rarr;</a>', esc_url( get_permalink() ), esc_html__( 'Details', 'school-master' ) );
			}
			?>
		</div>
	</div>
</article>
