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
		// Build this item in isolation so the toggle lands on its own <a>,
		// not on the first anchor of the accumulated menu output.
		$item_html = '';
		parent::start_el( $item_html, $item, $depth, $args, $id );

		if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			// Insert an accessible dropdown toggle right after the item link.
			$toggle = sprintf(
				'<button class="dropdown-toggle" aria-expanded="false"><span class="screen-reader-text">%s</span><span class="caret" aria-hidden="true"></span></button>',
				esc_html__( 'Open submenu', 'school-master' )
			);

			$item_html = preg_replace( '/(<a[^>]*>.*?<\/a>)/', '$1' . $toggle, $item_html, 1 );
		}

		$output .= $item_html;
	}
}
