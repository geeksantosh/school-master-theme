<?php
/**
 * The homepage template.
 *
 * Loads each modular section in order. Every section is individually
 * toggleable from Customizer → Homepage Sections, and each degrades
 * gracefully if its content (or the companion plugin) is missing.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Section id => Customizer toggle key.
 * Order here is the visual order on the page.
 */
$sections = array(
	'hero'         => 'hero',
	'notices'      => 'notices',
	'welcome'      => 'welcome',
	'courses'      => 'courses',
	'whyus'        => 'whyus',
	'stats'        => 'stats',
	'news'         => 'news',
	'testimonials' => 'testimonials',
	'partners'     => 'partners',
	'gallery'      => 'gallery',
	'cta'          => 'cta',
);

foreach ( $sections as $slug => $toggle ) {
	if ( school_master_section_enabled( $toggle ) ) {
		get_template_part( 'template-parts/home/section', $slug );
	}
}

get_footer();
