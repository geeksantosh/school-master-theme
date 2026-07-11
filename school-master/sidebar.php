<?php
/**
 * The blog sidebar.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'school-master' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
