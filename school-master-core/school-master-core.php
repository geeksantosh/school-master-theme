<?php
/**
 * Plugin Name:       School Master Core
 * Plugin URI:        https://github.com/geeksantosh/school-master-theme
 * Description:       Companion plugin for the School Master theme. Registers all content types (Notices, Courses, Faculty, Events, Gallery, Downloads, Testimonials, Partners), their taxonomies and meta. Keeping this data in a plugin means a school never loses content when switching or updating themes.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Santosh Adhikari
 * Author URI:        https://github.com/geeksantosh
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       school-master-core
 * Domain Path:       /languages
 *
 * @package SchoolMasterCore
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Plugin constants.
 */
define( 'SMCORE_VERSION', '1.0.0' );
define( 'SMCORE_FILE', __FILE__ );
define( 'SMCORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SMCORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Core includes.
 */
require_once SMCORE_PATH . 'includes/helpers.php';
require_once SMCORE_PATH . 'includes/class-smcore-post-types.php';
require_once SMCORE_PATH . 'includes/class-smcore-taxonomies.php';
require_once SMCORE_PATH . 'includes/class-smcore-meta-boxes.php';

/**
 * Boot the plugin.
 *
 * @return void
 */
function smcore_init() {
	SMCore_Post_Types::instance();
	SMCore_Taxonomies::instance();
	SMCore_Meta_Boxes::instance();

	load_plugin_textdomain( 'school-master-core', false, dirname( plugin_basename( SMCORE_FILE ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'smcore_init' );

/**
 * Flush rewrite rules on activation so CPT permalinks work immediately.
 *
 * We register the post types/taxonomies first, then flush.
 *
 * @return void
 */
function smcore_activate() {
	SMCore_Post_Types::instance()->register();
	SMCore_Taxonomies::instance()->register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'smcore_activate' );

/**
 * Clean up rewrite rules on deactivation.
 *
 * @return void
 */
function smcore_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'smcore_deactivate' );
