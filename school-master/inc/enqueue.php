<?php
/**
 * Front-end asset loading.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

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
		SCHOOL_MASTER_VERSION
	);

	// The style.css header file (screen-reader + alignment helpers).
	wp_enqueue_style(
		'school-master-base',
		get_stylesheet_uri(),
		array( 'school-master' ),
		SCHOOL_MASTER_VERSION
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
		SCHOOL_MASTER_VERSION,
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
