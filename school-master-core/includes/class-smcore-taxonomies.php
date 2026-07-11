<?php
/**
 * Registers taxonomies for the content types.
 *
 * @package SchoolMasterCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class SMCore_Taxonomies.
 */
class SMCore_Taxonomies {

	/**
	 * Singleton instance.
	 *
	 * @var SMCore_Taxonomies|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return SMCore_Taxonomies
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook registration into `init`.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Register every taxonomy.
	 *
	 * @return void
	 */
	public function register() {
		$this->register_tax( 'sm_notice_cat', 'sm_notice', __( 'Notice Category', 'school-master-core' ), __( 'Notice Categories', 'school-master-core' ), 'notice-category' );
		$this->register_tax( 'sm_course_cat', 'sm_course', __( 'Course Category', 'school-master-core' ), __( 'Course Categories', 'school-master-core' ), 'course-category' );
		$this->register_tax( 'sm_department', 'sm_faculty', __( 'Department', 'school-master-core' ), __( 'Departments', 'school-master-core' ), 'department' );
		$this->register_tax( 'sm_album', 'sm_gallery', __( 'Album', 'school-master-core' ), __( 'Albums', 'school-master-core' ), 'album' );
		$this->register_tax( 'sm_download_cat', 'sm_download', __( 'Download Category', 'school-master-core' ), __( 'Download Categories', 'school-master-core' ), 'download-category' );
	}

	/**
	 * Register a single hierarchical taxonomy.
	 *
	 * @param string $key        Taxonomy key.
	 * @param string $post_type  Post type to attach to.
	 * @param string $singular   Singular label.
	 * @param string $plural     Plural label.
	 * @param string $slug       Rewrite slug.
	 * @return void
	 */
	private function register_tax( $key, $post_type, $singular, $plural, $slug ) {
		$labels = array(
			'name'          => $plural,
			'singular_name' => $singular,
			'menu_name'     => $plural,
			'all_items'     => $plural,
			/* translators: %s: singular taxonomy name. */
			'edit_item'     => sprintf( __( 'Edit %s', 'school-master-core' ), $singular ),
			/* translators: %s: singular taxonomy name. */
			'add_new_item'  => sprintf( __( 'Add New %s', 'school-master-core' ), $singular ),
			/* translators: %s: plural taxonomy name. */
			'search_items'  => sprintf( __( 'Search %s', 'school-master-core' ), $plural ),
		);

		register_taxonomy(
			$key,
			$post_type,
			array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => $slug,
					'with_front' => false,
				),
			)
		);
	}
}
