<?php
/**
 * Gallery Images Meta Box
 *
 * Simple interface for users to select multiple gallery images.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register gallery images meta box.
 */
function school_master_gallery_register_meta_box() {
	add_meta_box(
		'school_master_gallery_images',
		__( 'Gallery Images', 'school-master' ),
		'school_master_gallery_images_callback',
		'sm_gallery',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'school_master_gallery_register_meta_box' );

/**
 * Render the gallery images meta box.
 */
function school_master_gallery_images_callback( $post ) {
	wp_nonce_field( 'school_master_gallery_images_nonce', 'school_master_gallery_images_nonce' );

	$gallery_ids = get_post_meta( $post->ID, '_school_master_gallery_images', true );
	$gallery_ids = $gallery_ids ? explode( ',', $gallery_ids ) : array();

	wp_enqueue_media();
	wp_enqueue_script( 'school-master-gallery-meta', get_template_directory_uri() . '/inc/gallery-meta-box.js', array( 'jquery', 'media-views', 'media-models' ), '1.0', true );
	wp_enqueue_style( 'school-master-gallery-meta', get_template_directory_uri() . '/inc/gallery-meta-box.css' );

	wp_localize_script( 'school-master-gallery-meta', 'schoolMasterGalleryL10n', array(
		'addImages'    => __( 'Add Images', 'school-master' ),
		'removeImage'  => __( 'Remove', 'school-master' ),
		'noImages'     => __( 'No images selected yet. Click "Add Images" to get started.', 'school-master' ),
		'galleryIds'   => $gallery_ids,
	) );

	?>
	<div id="school-master-gallery-container" class="school-master-gallery-container">
		<div id="gallery-images-list" class="gallery-images-list">
			<?php if ( ! empty( $gallery_ids ) ) : ?>
				<?php foreach ( $gallery_ids as $attachment_id ) : ?>
					<?php $image = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); ?>
					<?php if ( $image ) : ?>
						<div class="gallery-image-item" data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
							<img src="<?php echo esc_url( $image ); ?>" alt="">
							<button type="button" class="remove-image" aria-label="<?php esc_attr_e( 'Remove image', 'school-master' ); ?>">&times;</button>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="no-images-message"><?php esc_html_e( 'No images selected yet. Click "Add Images" to get started.', 'school-master' ); ?></p>
			<?php endif; ?>
		</div>

		<button type="button" id="add-gallery-images" class="button button-primary" style="margin-top: 1rem;">
			<?php esc_html_e( 'Add Images', 'school-master' ); ?>
		</button>

		<input type="hidden" id="gallery-images-input" name="school_master_gallery_images" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>">
	</div>
	<?php
}

/**
 * Save gallery images meta.
 */
function school_master_gallery_save_images( $post_id, $post ) {
	if ( $post->post_type !== 'sm_gallery' ) {
		return;
	}

	// Check nonce
	if ( ! isset( $_POST['school_master_gallery_images_nonce'] ) ||
		! wp_verify_nonce( $_POST['school_master_gallery_images_nonce'], 'school_master_gallery_images_nonce' ) ) {
		return;
	}

	// Check capabilities
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Sanitize and save
	if ( isset( $_POST['school_master_gallery_images'] ) ) {
		$gallery_ids = sanitize_text_field( $_POST['school_master_gallery_images'] );
		update_post_meta( $post_id, '_school_master_gallery_images', $gallery_ids );
	} else {
		delete_post_meta( $post_id, '_school_master_gallery_images' );
	}
}
add_action( 'save_post', 'school_master_gallery_save_images', 10, 2 );
