<?php
/**
 * Homepage section: Why Choose Us.
 *
 * Pulls its items from a "Why Us" menu-order-ordered list stored as
 * simple posts is overkill; instead we read repeatable feature widgets
 * from the sidebar `whyus` if present, otherwise show nothing. To keep
 * things dependency-free and editable, items come from Customizer-defined
 * feature blocks (title + text) — see the loop below.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$title = school_master_option( 'whyus_title', __( 'Why Choose Us', 'school-master' ) );

/**
 * Feature items. Filterable so a child theme or the demo importer can
 * supply the eight benefit statements seen on the reference site.
 *
 * @var array<int,array{title:string,text:string,icon:string}> $features
 */
$features = apply_filters( 'school_master_whyus_features', array() );

if ( empty( $features ) ) {
	return;
}
?>
<section class="home-section whyus">
	<div class="container">
		<h2 class="section-title section-title--center"><?php echo esc_html( $title ); ?></h2>

		<div class="card-grid card-grid--features">
			<?php foreach ( $features as $feature ) : ?>
				<div class="feature">
					<?php if ( ! empty( $feature['icon'] ) ) : ?>
						<span class="feature__icon dashicons <?php echo esc_attr( $feature['icon'] ); ?>" aria-hidden="true"></span>
					<?php endif; ?>
					<h3 class="feature__title"><?php echo esc_html( $feature['title'] ); ?></h3>
					<?php if ( ! empty( $feature['text'] ) ) : ?>
						<p class="feature__text"><?php echo esc_html( $feature['text'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
