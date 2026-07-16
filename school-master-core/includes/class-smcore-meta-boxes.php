<?php
/**
 * Registers meta boxes and custom fields for the content types.
 *
 * A small, dependency-free meta framework: each post type declares its
 * fields as a config array, and this class handles rendering, nonce
 * verification, sanitization and saving. No ACF required, so buyers
 * don't inherit a third-party dependency.
 *
 * @package SchoolMasterCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class SMCore_Meta_Boxes.
 */
class SMCore_Meta_Boxes {

	/**
	 * Singleton instance.
	 *
	 * @var SMCore_Meta_Boxes|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return SMCore_Meta_Boxes
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook into the admin.
	 */
	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media' ) );
	}

	/**
	 * Field definitions per post type.
	 *
	 * Field types: text, number, email, url, textarea, checkbox, date, file.
	 *
	 * @return array<string,array<string,array>>
	 */
	public function fields() {
		return array(
			'sm_course'      => array(
				'duration'    => array(
					'label' => __( 'Duration', 'school-master-core' ),
					'type'  => 'text',
					'desc'  => __( 'e.g. 18 months, 3 years', 'school-master-core' ),
				),
				'seats'       => array(
					'label' => __( 'Total Seats', 'school-master-core' ),
					'type'  => 'number',
				),
				'eligibility' => array(
					'label' => __( 'Eligibility', 'school-master-core' ),
					'type'  => 'textarea',
				),
				'fee'         => array(
					'label' => __( 'Fee', 'school-master-core' ),
					'type'  => 'text',
				),
				'apply_url'   => array(
					'label' => __( 'Apply Online URL', 'school-master-core' ),
					'type'  => 'url',
					'desc'  => __( 'Optional. Adds an "Apply Now" button to this course. Link to your admission form or an external portal.', 'school-master-core' ),
				),
				'brochure'    => array(
					'label' => __( 'Brochure (PDF)', 'school-master-core' ),
					'type'  => 'file',
					'desc'  => __( 'Optional. Adds a brochure download button showing the file size.', 'school-master-core' ),
				),
			),
			'sm_faculty'     => array(
				'designation'   => array(
					'label' => __( 'Designation', 'school-master-core' ),
					'type'  => 'text',
					'desc'  => __( 'e.g. Principal, Lecturer', 'school-master-core' ),
				),
				'qualification' => array(
					'label' => __( 'Qualification', 'school-master-core' ),
					'type'  => 'text',
				),
				'email'         => array(
					'label' => __( 'Email', 'school-master-core' ),
					'type'  => 'email',
				),
				'phone'         => array(
					'label' => __( 'Phone', 'school-master-core' ),
					'type'  => 'text',
				),
				'facebook'      => array(
					'label' => __( 'Facebook URL', 'school-master-core' ),
					'type'  => 'url',
				),
			),
			'sm_notice'      => array(
				'is_important'  => array(
					'label' => __( 'Mark as important', 'school-master-core' ),
					'type'  => 'checkbox',
					'desc'  => __( 'Highlights the notice and pins it to the top.', 'school-master-core' ),
				),
				'popup_expiry'  => array(
					'label' => __( 'Popup until', 'school-master-core' ),
					'type'  => 'date',
					'desc'  => __( 'Optional. The first-visit popup stops showing this notice after this date. Leave blank to keep showing it.', 'school-master-core' ),
				),
				'attachment'    => array(
					'label' => __( 'Attachment (PDF/Doc)', 'school-master-core' ),
					'type'  => 'file',
				),
			),
			'sm_event'       => array(
				'start_date' => array(
					'label' => __( 'Start Date', 'school-master-core' ),
					'type'  => 'date',
				),
				'end_date'   => array(
					'label' => __( 'End Date', 'school-master-core' ),
					'type'  => 'date',
				),
				'location'   => array(
					'label' => __( 'Location', 'school-master-core' ),
					'type'  => 'text',
				),
			),
			'sm_download'    => array(
				'file' => array(
					'label' => __( 'File', 'school-master-core' ),
					'type'  => 'file',
				),
			),
			'sm_testimonial' => array(
				'author_role' => array(
					'label' => __( 'Author Role', 'school-master-core' ),
					'type'  => 'text',
					'desc'  => __( 'e.g. Alumni 2020, Parent', 'school-master-core' ),
				),
			),
			'sm_partner'     => array(
				'url' => array(
					'label' => __( 'Website URL', 'school-master-core' ),
					'type'  => 'url',
				),
			),
		);
	}

	/**
	 * Register a meta box for each post type that declares fields.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		foreach ( array_keys( $this->fields() ) as $post_type ) {
			add_meta_box(
				'smcore_details',
				__( 'Details', 'school-master-core' ),
				array( $this, 'render' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Render the meta box.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public function render( $post ) {
		$fields = $this->fields();

		if ( empty( $fields[ $post->post_type ] ) ) {
			return;
		}

		wp_nonce_field( 'smcore_save_meta', 'smcore_meta_nonce' );

		echo '<table class="form-table"><tbody>';

		foreach ( $fields[ $post->post_type ] as $name => $field ) {
			$value = get_post_meta( $post->ID, '_sm_' . $name, true );
			$id    = 'sm_' . $name;

			echo '<tr>';
			printf( '<th scope="row"><label for="%s">%s</label></th>', esc_attr( $id ), esc_html( $field['label'] ) );
			echo '<td>';

			$this->render_field( $name, $field, $value );

			if ( ! empty( $field['desc'] ) ) {
				printf( '<p class="description">%s</p>', esc_html( $field['desc'] ) );
			}

			echo '</td></tr>';
		}

		echo '</tbody></table>';
	}

	/**
	 * Render a single field control.
	 *
	 * @param string $name  Field name.
	 * @param array  $field Field config.
	 * @param mixed  $value Current value.
	 * @return void
	 */
	private function render_field( $name, $field, $value ) {
		$id   = 'sm_' . $name;
		$attr = sprintf( 'id="%1$s" name="%1$s"', esc_attr( $id ) );

		switch ( $field['type'] ) {
			case 'textarea':
				printf( '<textarea %s rows="4" class="large-text">%s</textarea>', $attr, esc_textarea( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'checkbox':
				printf( '<label><input type="checkbox" %s value="1" %s /> %s</label>', $attr, checked( $value, '1', false ), esc_html__( 'Yes', 'school-master-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'file':
				printf( '<input type="url" %s value="%s" class="regular-text smcore-file-url" />', $attr, esc_attr( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				printf( ' <button type="button" class="button smcore-upload">%s</button>', esc_html__( 'Select File', 'school-master-core' ) );
				break;

			case 'number':
				printf( '<input type="number" %s value="%s" class="small-text" />', $attr, esc_attr( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'date':
				printf( '<input type="date" %s value="%s" />', $attr, esc_attr( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'email':
			case 'url':
			case 'text':
			default:
				printf( '<input type="%s" %s value="%s" class="regular-text" />', esc_attr( $field['type'] ), $attr, esc_attr( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
		}
	}

	/**
	 * Save meta on `save_post`.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function save( $post_id, $post ) {
		// Bail on autosave, revisions, and missing/invalid nonce.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['smcore_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['smcore_meta_nonce'] ), 'smcore_save_meta' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = $this->fields();

		if ( empty( $fields[ $post->post_type ] ) ) {
			return;
		}

		foreach ( $fields[ $post->post_type ] as $name => $field ) {
			$key = 'sm_' . $name;

			if ( 'checkbox' === $field['type'] ) {
				$value = isset( $_POST[ $key ] ) ? '1' : '';
			} else {
				$value = isset( $_POST[ $key ] ) ? $this->sanitize( wp_unslash( $_POST[ $key ] ), $field['type'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}

			if ( '' === $value ) {
				delete_post_meta( $post_id, '_sm_' . $name );
			} else {
				update_post_meta( $post_id, '_sm_' . $name, $value );
			}
		}
	}

	/**
	 * Sanitize a value based on its field type.
	 *
	 * @param mixed  $value Raw value.
	 * @param string $type  Field type.
	 * @return mixed
	 */
	private function sanitize( $value, $type ) {
		switch ( $type ) {
			case 'textarea':
				return sanitize_textarea_field( $value );
			case 'email':
				return sanitize_email( $value );
			case 'url':
			case 'file':
				return esc_url_raw( $value );
			case 'number':
				return is_numeric( $value ) ? $value + 0 : '';
			case 'date':
				return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Load the media uploader on our edit screens for file fields.
	 *
	 * @param string $hook Current admin page.
	 * @return void
	 */
	public function enqueue_media( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || ! array_key_exists( $screen->post_type, $this->fields() ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'smcore-admin',
			SMCORE_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			SMCORE_VERSION,
			true
		);
	}
}
