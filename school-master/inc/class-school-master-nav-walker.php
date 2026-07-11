<?php
/**
 * Lightweight nav walker adding a caret to items with children (for dropdowns).
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class School_Master_Nav_Walker.
 */
class School_Master_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Start the element output, appending a toggle for parent items.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		parent::start_el( $output, $item, $depth, $args, $id );

		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );

		if ( $has_children && 0 === $depth ) {
			// Insert an accessible dropdown toggle right after the opening <a>.
			$toggle = sprintf(
				'<button class="dropdown-toggle" aria-expanded="false"><span class="screen-reader-text">%s</span><span class="caret" aria-hidden="true"></span></button>',
				esc_html__( 'Open submenu', 'school-master' )
			);

			$output = preg_replace( '/(<a[^>]*>.*?<\/a>)/', '$1' . $toggle, $output, 1 );
		}
	}
}
