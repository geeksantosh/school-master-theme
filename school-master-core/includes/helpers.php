<?php
/**
 * Shared helper functions for the plugin.
 *
 * @package SchoolMasterCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a piece of post meta for one of our content types.
 *
 * Thin wrapper so templates in the theme never have to know the raw
 * meta key names (which are prefixed with `_sm_`).
 *
 * @param string   $field   Field name without the `_sm_` prefix (e.g. 'duration').
 * @param int|null $post_id Optional. Post ID. Defaults to current post.
 * @param bool     $single  Optional. Return a single value. Default true.
 * @return mixed
 */
function smcore_get_meta( $field, $post_id = null, $single = true ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return get_post_meta( $post_id, '_sm_' . $field, $single );
}

/**
 * Whether the companion plugin is providing a given post type.
 *
 * The theme uses this to decide whether to show a section that depends
 * on plugin content, so it degrades gracefully if the plugin is off.
 *
 * @param string $post_type Post type key (e.g. 'sm_course').
 * @return bool
 */
function smcore_has_post_type( $post_type ) {
	return post_type_exists( $post_type );
}

/**
 * List of the content types this plugin registers.
 *
 * @return array<string,string> Map of post type key => human label.
 */
function smcore_post_types() {
	return array(
		'sm_notice'      => __( 'Notices', 'school-master-core' ),
		'sm_course'      => __( 'Courses', 'school-master-core' ),
		'sm_faculty'     => __( 'Faculty', 'school-master-core' ),
		'sm_event'       => __( 'Events', 'school-master-core' ),
		'sm_gallery'     => __( 'Gallery', 'school-master-core' ),
		'sm_download'    => __( 'Downloads', 'school-master-core' ),
		'sm_testimonial' => __( 'Testimonials', 'school-master-core' ),
		'sm_partner'     => __( 'Partners', 'school-master-core' ),
	);
}
