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
 * Build the dynamic CSS custom properties from Customizer settings.
 *
 * Every color/spacing token a school can change flows through here, so the
 * whole theme re-skins from a handful of Customizer controls.
 *
 * @return string
 */
function school_master_dynamic_css() {
	$primary   = get_theme_mod( 'school_master_primary_color', '#0b5394' );
	$secondary = get_theme_mod( 'school_master_secondary_color', '#e8a33d' );
	$dark      = get_theme_mod( 'school_master_dark_color', '#0b2545' );

	$css = ':root{';
	$css .= '--sm-primary:' . sanitize_hex_color( $primary ) . ';';
	$css .= '--sm-secondary:' . sanitize_hex_color( $secondary ) . ';';
	$css .= '--sm-dark:' . sanitize_hex_color( $dark ) . ';';
	$css .= '}';

	return $css;
}
