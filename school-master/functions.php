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

add_action( 'after_setup_theme', function() {
	// Set up course featured images from SVG files
	if ( get_option( 'school_master_course_images_setup' ) ) {
		return; // Already done
	}

	$courses = array(
		142 => '142-mathematics-fundamentals.svg',
		143 => '143-english-literature.svg',
		144 => '144-science-biology.svg',
		145 => '145-history---social-studies.svg',
		146 => '146-chemistry.svg',
		147 => '147-physics.svg',
		148 => '148-computer-science---programming.svg',
		149 => '149-art---design.svg',
		150 => '150-physical-education---sports.svg',
	);

	$upload_dir = wp_upload_dir();
	$uploads_path = $upload_dir['basedir'] . '/2026/07';

	foreach ( $courses as $course_id => $filename ) {
		$file_path = $uploads_path . '/' . $filename;

		if ( ! file_exists( $file_path ) ) {
			continue;
		}

		// Check if attachment already exists
		$existing_attachment = get_post_thumbnail_id( $course_id );
		if ( $existing_attachment ) {
			continue;
		}

		// Create attachment
		$attachment = array(
			'post_mime_type' => 'image/svg+xml',
			'post_title'     => "Course Featured Image",
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_parent'    => $course_id,
		);

		$file_url = $upload_dir['url'] . '/2026/07/' . $filename;
		$attach_id = wp_insert_attachment( $attachment, $file_path, $course_id );

		if ( ! is_wp_error( $attach_id ) ) {
			set_post_thumbnail( $course_id, $attach_id );
		}
	}

	update_option( 'school_master_course_images_setup', 1 );
} );
