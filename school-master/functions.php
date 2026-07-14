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
	// Set up course featured images
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
	$setup_needed = false;

	// Check if any course is missing metadata - if so, re-setup all
	foreach ( $courses as $course_id => $filename ) {
		$existing_attachment = get_post_thumbnail_id( $course_id );
		if ( $existing_attachment ) {
			$metadata = wp_get_attachment_metadata( $existing_attachment );
			// If attachment exists but has no metadata, we need to fix it
			if ( empty( $metadata ) || ! isset( $metadata['width'] ) ) {
				$setup_needed = true;
				break;
			}
		}
	}

	// Only skip setup if all courses have proper thumbnails with metadata
	if ( ! $setup_needed ) {
		$all_setup = true;
		foreach ( $courses as $course_id => $filename ) {
			if ( ! get_post_thumbnail_id( $course_id ) ) {
				$all_setup = false;
				break;
			}
		}
		if ( $all_setup ) {
			return;
		}
	}

	foreach ( $courses as $course_id => $filename ) {
		$file_path = $uploads_path . '/' . $filename;

		if ( ! file_exists( $file_path ) ) {
			continue;
		}

		// Check if attachment already exists
		$existing_attachment = get_post_thumbnail_id( $course_id );
		if ( $existing_attachment ) {
			// Update metadata if missing
			$metadata = wp_get_attachment_metadata( $existing_attachment );
			if ( empty( $metadata ) || ! isset( $metadata['width'] ) ) {
				$metadata = array(
					'width'  => 1200,
					'height' => 800,
					'file'   => $filename,
				);
				wp_update_attachment_metadata( $existing_attachment, $metadata );
			}
			continue;
		}

		// For SVG files, create minimal attachment without thumbnail processing
		$attachment = array(
			'post_mime_type' => 'image/svg+xml',
			'post_title'     => "Course Featured Image",
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_parent'    => $course_id,
		);

		$attach_id = wp_insert_attachment( $attachment, $file_path, $course_id );

		if ( ! is_wp_error( $attach_id ) ) {
			// For SVG, manually set the media details since WordPress can't generate thumbnails
			$metadata = array(
				'width'  => 1200,
				'height' => 800,
				'file'   => $filename,
			);
			wp_update_attachment_metadata( $attach_id, $metadata );
			set_post_thumbnail( $course_id, $attach_id );
		}
	}
} );

// Filter post_thumbnail_html to handle SVG images properly
add_filter( 'post_thumbnail_html', function( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	// If this is already valid HTML, return it
	if ( ! empty( $html ) ) {
		return $html;
	}

	// If the attachment is an SVG, generate HTML for it directly
	$attachment = get_post( $post_thumbnail_id );
	if ( $attachment && 'image/svg+xml' === $attachment->post_mime_type ) {
		$src = wp_get_attachment_url( $post_thumbnail_id );
		$alt = get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true );
		$title = $attachment->post_title;

		if ( is_array( $attr ) ) {
			$attr = array_merge(
				array( 'alt' => $alt ? $alt : $title ),
				$attr
			);
			$attr_html = implode( ' ', array_map(
				function( $k, $v ) {
					return $k . '="' . esc_attr( $v ) . '"';
				},
				array_keys( $attr ),
				$attr
			) );
		} else {
			$attr_html = 'alt="' . esc_attr( $alt ? $alt : $title ) . '"';
		}

		return '<img src="' . esc_url( $src ) . '" ' . $attr_html . ' loading="lazy" />';
	}

	return $html;
}, 10, 5 );
