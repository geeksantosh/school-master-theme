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
}
