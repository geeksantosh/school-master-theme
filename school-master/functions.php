<?php
/**
 * School Master theme bootstrap.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme constants.
 */
define( 'SCHOOL_MASTER_VERSION', '1.0.0' );
define( 'SCHOOL_MASTER_DIR', get_template_directory() );
define( 'SCHOOL_MASTER_URI', get_template_directory_uri() );

/**
 * Load theme modules.
 */
require_once SCHOOL_MASTER_DIR . '/inc/setup.php';
require_once SCHOOL_MASTER_DIR . '/inc/enqueue.php';
require_once SCHOOL_MASTER_DIR . '/inc/template-tags.php';
require_once SCHOOL_MASTER_DIR . '/inc/customizer.php';
require_once SCHOOL_MASTER_DIR . '/inc/class-school-master-nav-walker.php';

if ( is_admin() ) {
	require_once SCHOOL_MASTER_DIR . '/inc/plugin-notice.php';
	require_once SCHOOL_MASTER_DIR . '/inc/demo-import.php';
	require_once SCHOOL_MASTER_DIR . '/inc/gallery-meta-box.php';
	require_once SCHOOL_MASTER_DIR . '/inc/gallery-ajax.php';
}

add_action( 'after_setup_theme', function() {
	$current = get_option( 'school_master_testimonials_count' );
	if ( $current && (int) $current < 10 ) {
		update_option( 'school_master_testimonials_count', 100 );
	}
} );

add_action( 'after_setup_theme', function() {
	if ( ! get_option( 'school_master_testimonials_autoscroll' ) ) {
		update_option( 'school_master_testimonials_autoscroll', 1 );
	}
} );
