<?php
/**
 * AJAX handlers for gallery meta box.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handler to get gallery images HTML.
 */
function school_master_get_gallery_images() {
	check_ajax_referer( 'school_master_gallery_images_nonce', 'nonce' );

	if ( ! isset( $_POST['image_ids'] ) ) {
		wp_send_json_error();
	}

	$image_ids = array_map( 'intval', explode( ',', sanitize_text_field( $_POST['image_ids'] ) ) );
	$image_ids = array_filter( $image_ids );

	if ( empty( $image_ids ) ) {
		wp_send_json_success( array(
			'html' => '<p class="no-images-message">' . esc_html__( 'No images selected yet. Click "Add Images" to get started.', 'school-master' ) . '</p>',
		) );
	}

	$html = '';
	foreach ( $image_ids as $attachment_id ) {
		$image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
		if ( $image_url ) {
			$html .= sprintf(
				'<div class="gallery-image-item" data-attachment-id="%d"><img src="%s" alt=""><button type="button" class="remove-image" aria-label="%s">&times;</button></div>',
				$attachment_id,
				esc_url( $image_url ),
				esc_attr__( 'Remove image', 'school-master' )
			);
		}
	}

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_school_master_get_gallery_images', 'school_master_get_gallery_images' );
