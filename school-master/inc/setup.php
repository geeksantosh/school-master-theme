<?php
/**
 * Theme setup: supports, menus, sidebars.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme feature support.
 *
 * @return void
 */
function school_master_setup() {
	load_theme_textdomain( 'school-master', SCHOOL_MASTER_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Image sizes tuned for the homepage sections.
	add_image_size( 'school-master-card', 400, 280, true );
	add_image_size( 'school-master-faculty', 300, 300, true );
	add_image_size( 'school-master-hero', 1600, 700, true );

	register_nav_menus(
		array(
			'primary'   => __( 'Primary Menu', 'school-master' ),
			'footer'    => __( 'Footer Quick Links', 'school-master' ),
			'topbar'    => __( 'Top Bar Menu', 'school-master' ),
		)
	);
}
add_action( 'after_setup_theme', 'school_master_setup' );

/**
 * Set the content width.
 *
 * @return void
 */
function school_master_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'school_master_content_width', 1140 );
}
add_action( 'after_setup_theme', 'school_master_content_width', 0 );

/**
 * Register widget areas.
 *
 * @return void
 */
function school_master_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'school-master' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Widgets shown alongside blog posts and archives.', 'school-master' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( __( 'Footer Column %d', 'school-master' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Footer widget area.', 'school-master' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);
	}
}
add_action( 'widgets_init', 'school_master_widgets_init' );
