<?php
/**
 * Faculty content — card in the archive, profile when singular.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$designation   = smcore_get_meta( 'designation' );
$qualification = smcore_get_meta( 'qualification' );
$email         = smcore_get_meta( 'email' );
$phone         = smcore_get_meta( 'phone' );
$facebook      = smcore_get_meta( 'facebook' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card faculty-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="card__media faculty-card__media">
			<?php the_post_thumbnail( 'school-master-faculty', array( 'loading' => 'lazy' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="card__body faculty-card__body">
		<?php
		if ( is_singular() ) {
			the_title( '<h1 class="entry-title faculty-card__name">', '</h1>' );
		} else {
			the_title( '<h2 class="card__title faculty-card__name"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
		}
		?>

		<?php if ( $designation ) : ?>
			<p class="faculty-card__designation"><?php echo esc_html( $designation ); ?></p>
		<?php endif; ?>

		<?php if ( $qualification ) : ?>
			<p class="faculty-card__qualification"><?php echo esc_html( $qualification ); ?></p>
		<?php endif; ?>

		<ul class="faculty-card__contact">
			<?php if ( $email ) : ?>
				<li><a href="<?php echo esc_attr( 'mailto:' . antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<li><a href="<?php echo esc_attr( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
			<?php endif; ?>
			<?php if ( $facebook ) : ?>
				<li><a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Facebook', 'school-master' ); ?></a></li>
			<?php endif; ?>
		</ul>

		<?php if ( is_singular() ) : ?>
			<div class="entry-content faculty-card__bio"><?php the_content(); ?></div>
		<?php endif; ?>
	</div>
</article>
