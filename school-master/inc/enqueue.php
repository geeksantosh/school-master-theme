<?php
/**
 * Front-end asset loading.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a theme asset.
 *
 * SCHOOL_MASTER_VERSION alone is a constant, so an edited file keeps its old
 * ?ver= and browsers — Safari especially — serve the cached copy indefinitely.
 * The file's mtime changes whenever the file does, which is the whole point.
 *
 * @param string $relative_path Path below the theme root, leading slash.
 * @return string
 */
function school_master_asset_version( $relative_path ) {
	$file = SCHOOL_MASTER_DIR . $relative_path;
	$time = file_exists( $file ) ? filemtime( $file ) : false;

	return $time ? SCHOOL_MASTER_VERSION . '.' . $time : SCHOOL_MASTER_VERSION;
}

/**
 * Enqueue theme styles and scripts.
 *
 * @return void
 */
function school_master_enqueue() {
	// Brand webfonts: Outfit for headings, Open Sans for body copy.
	wp_enqueue_style(
		'school-master-fonts',
		school_master_fonts_url(),
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google serves its own cache key.
	);

	// Main stylesheet.
	wp_enqueue_style(
		'school-master',
		SCHOOL_MASTER_URI . '/assets/css/theme.css',
		array(),
		school_master_asset_version( '/assets/css/theme.css' )
	);

	// The style.css header file (screen-reader + alignment helpers).
	wp_enqueue_style(
		'school-master-base',
		get_stylesheet_uri(),
		array( 'school-master' ),
		school_master_asset_version( '/style.css' )
	);

	// Dashicons power the "Why Choose Us" feature icons on the front end.
	wp_enqueue_style( 'dashicons' );

	// Inline the Customizer-driven CSS variables so brand colors apply everywhere.
	wp_add_inline_style( 'school-master', school_master_dynamic_css() );

	// Main script.
	wp_enqueue_script(
		'school-master',
		SCHOOL_MASTER_URI . '/assets/js/theme.js',
		array(),
		school_master_asset_version( '/assets/js/theme.js' ),
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'school_master_enqueue' );

/**
 * Google Fonts request for the theme's two typefaces.
 *
 * @return string
 */
function school_master_fonts_url() {
	return add_query_arg(
		array(
			'family'  => rawurlencode( 'Outfit:wght@400;500;600;700;800' ) . '&family=' . rawurlencode( 'Open Sans:wght@400;500;600;700' ),
			'display' => 'swap',
		),
		'https://fonts.googleapis.com/css2'
	);
}

/**
 * Open a connection to the font host before the stylesheet is parsed.
 *
 * @param array  $urls          Resource URLs already queued for the handle.
 * @param string $relation_type Link relation being filtered.
 * @return array
 */
function school_master_font_preconnect( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'school-master-fonts', 'enqueued' ) ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'school_master_font_preconnect', 10, 2 );

/**
 * Shift a hex color toward white or black.
 *
 * Lets the palette derive its own hover, gradient and wash tones from the three
 * Customizer colors, so a school that re-brands gets a coherent set rather than
 * one changed color sitting next to hard-coded indigo.
 *
 * @param string $hex     Source color, #rgb or #rrggbb.
 * @param float  $percent -1 (black) to 1 (white).
 * @return string
 */
function school_master_shade( $hex, $percent ) {
	$hex = sanitize_hex_color( $hex );

	if ( ! $hex ) {
		return '#000000';
	}

	$hex = ltrim( $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	$target = $percent > 0 ? 255 : 0;
	$amount = min( 1, abs( (float) $percent ) );
	$out    = '#';

	foreach ( str_split( $hex, 2 ) as $pair ) {
		$channel = hexdec( $pair );
		$out    .= str_pad( dechex( (int) round( $channel + ( $target - $channel ) * $amount ) ), 2, '0', STR_PAD_LEFT );
	}

	return $out;
}

/**
 * Comma-separated R,G,B for a hex color, for use inside rgba().
 *
 * @param string $hex Source color.
 * @return string
 */
function school_master_rgb( $hex ) {
	$hex = ltrim( (string) sanitize_hex_color( $hex ), '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) ) {
		return '0,0,0';
	}

	return implode( ',', array_map( 'hexdec', str_split( $hex, 2 ) ) );
}

/**
 * Build the dynamic CSS custom properties from Customizer settings.
 *
 * Every color/spacing token a school can change flows through here, so the
 * whole theme re-skins from a handful of Customizer controls.
 *
 * @return string
 */
function school_master_dynamic_css() {
	$primary   = sanitize_hex_color( get_theme_mod( 'school_master_primary_color', '#26236c' ) );
	$secondary = sanitize_hex_color( get_theme_mod( 'school_master_secondary_color', '#f25708' ) );
	$dark      = sanitize_hex_color( get_theme_mod( 'school_master_dark_color', '#1b1852' ) );

	$css  = ':root{';
	$css .= '--sm-primary:' . $primary . ';';
	$css .= '--sm-secondary:' . $secondary . ';';
	$css .= '--sm-dark:' . $dark . ';';

	// Derived brand tones: hover states, gradient stops and section washes.
	$css .= '--sm-primary-bright:' . school_master_shade( $primary, .28 ) . ';';
	$css .= '--sm-primary-deep:' . school_master_shade( $primary, -.25 ) . ';';
	$css .= '--sm-primary-wash:' . school_master_shade( $primary, .94 ) . ';';
	$css .= '--sm-primary-tint:' . school_master_shade( $primary, .86 ) . ';';
	$css .= '--sm-secondary-warm:' . school_master_shade( $secondary, .22 ) . ';';
	$css .= '--sm-secondary-wash:' . school_master_shade( $secondary, .9 ) . ';';
	$css .= '--sm-dark-deep:' . school_master_shade( $dark, -.35 ) . ';';

	// RGB triplets so overlays and shadows can tint with the brand.
	$css .= '--sm-primary-rgb:' . school_master_rgb( $primary ) . ';';
	$css .= '--sm-dark-rgb:' . school_master_rgb( $dark ) . ';';
	$css .= '}';

	return $css;
}
