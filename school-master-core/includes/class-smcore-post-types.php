<?php
/**
 * Registers all custom post types.
 *
 * @package SchoolMasterCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class SMCore_Post_Types.
 */
class SMCore_Post_Types {

	/**
	 * Singleton instance.
	 *
	 * @var SMCore_Post_Types|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return SMCore_Post_Types
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
	 * Register every post type.
	 *
	 * @return void
	 */
	public function register() {
		$this->register_type(
			'sm_notice',
			__( 'Notice', 'school-master-core' ),
			__( 'Notices', 'school-master-core' ),
			'notices',
			'dashicons-megaphone',
			array( 'title', 'editor', 'thumbnail', 'excerpt' )
		);

		$this->register_type(
			'sm_course',
			__( 'Course', 'school-master-core' ),
			__( 'Courses', 'school-master-core' ),
			'courses',
			'dashicons-welcome-learn-more',
			array( 'title', 'editor', 'thumbnail', 'excerpt' )
		);

		$this->register_type(
			'sm_faculty',
			__( 'Faculty', 'school-master-core' ),
			__( 'Faculty', 'school-master-core' ),
			'faculty',
			'dashicons-groups',
			array( 'title', 'editor', 'thumbnail' )
		);

		$this->register_type(
			'sm_event',
			__( 'Event', 'school-master-core' ),
			__( 'Events', 'school-master-core' ),
			'events',
			'dashicons-calendar-alt',
			array( 'title', 'editor', 'thumbnail', 'excerpt' )
		);

		$this->register_type(
			'sm_gallery',
			__( 'Gallery Album', 'school-master-core' ),
			__( 'Gallery', 'school-master-core' ),
			'gallery',
			'dashicons-format-gallery',
			array( 'title', 'editor', 'thumbnail' )
		);

		$this->register_type(
			'sm_download',
			__( 'Download', 'school-master-core' ),
			__( 'Downloads', 'school-master-core' ),
			'downloads',
			'dashicons-download',
			array( 'title', 'editor' )
		);

		$this->register_type(
			'sm_testimonial',
			__( 'Testimonial', 'school-master-core' ),
			__( 'Testimonials', 'school-master-core' ),
			'testimonials',
			'dashicons-format-quote',
			array( 'title', 'editor', 'thumbnail' ),
			false // Not publicly queryable on its own archive.
		);

		$this->register_type(
			'sm_partner',
			__( 'Partner', 'school-master-core' ),
			__( 'Partners', 'school-master-core' ),
			'partners',
			'dashicons-networking',
			array( 'title', 'thumbnail' ),
			false
		);
	}

	/**
	 * Helper that registers a single post type with sensible defaults.
	 *
	 * @param string   $key       Post type key.
	 * @param string   $singular  Singular label.
	 * @param string   $plural    Plural label.
	 * @param string   $slug      Rewrite slug.
	 * @param string   $icon      Dashicon.
	 * @param string[] $supports  Supported features.
	 * @param bool     $has_archive Whether to give it an archive. Default true.
	 * @return void
	 */
	private function register_type( $key, $singular, $plural, $slug, $icon, $supports, $has_archive = true ) {
		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'menu_name'             => $plural,
			'add_new'               => __( 'Add New', 'school-master-core' ),
			/* translators: %s: singular post type name. */
			'add_new_item'          => sprintf( __( 'Add New %s', 'school-master-core' ), $singular ),
			/* translators: %s: singular post type name. */
			'edit_item'             => sprintf( __( 'Edit %s', 'school-master-core' ), $singular ),
			/* translators: %s: singular post type name. */
			'new_item'              => sprintf( __( 'New %s', 'school-master-core' ), $singular ),
			/* translators: %s: singular post type name. */
			'view_item'             => sprintf( __( 'View %s', 'school-master-core' ), $singular ),
			/* translators: %s: plural post type name. */
			'search_items'          => sprintf( __( 'Search %s', 'school-master-core' ), $plural ),
			/* translators: %s: plural post type name (lowercase). */
			'not_found'             => sprintf( __( 'No %s found', 'school-master-core' ), strtolower( $plural ) ),
			'all_items'             => $plural,
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true, // Enable block editor + REST API.
			'has_archive'        => $has_archive,
			'publicly_queryable' => true,
			'rewrite'            => array(
				'slug'       => $slug,
				'with_front' => false,
			),
			'menu_icon'          => $icon,
			'supports'           => $supports,
			'hierarchical'       => false,
		);

		register_post_type( $key, $args );
	}
}
