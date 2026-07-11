<?php
/**
 * Companion-plugin dependency notice.
 *
 * The homepage sections (notices, courses, faculty, events, gallery…) rely on
 * the custom post types registered by the "School Master Core" plugin. When a
 * buyer activates only the theme, this shows a dismissible admin notice with a
 * one-click install/activate action, so the empty homepage is explained.
 *
 * Intentionally lightweight — no bundled TGMPA library. The plugin is expected
 * on the WordPress.org repo (slug: school-master-core); if a buyer installs it
 * manually the notice simply disappears once it's active.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the companion plugin is active.
 *
 * We check for the marker constant the plugin defines, which avoids hard-coding
 * the plugin folder/file name (a buyer may rename the ZIP on upload).
 *
 * @return bool
 */
function school_master_core_active() {
	return defined( 'SMCORE_VERSION' );
}

/**
 * Show the admin notice when the plugin is missing.
 *
 * @return void
 */
function school_master_plugin_notice() {
	// Only bug users who can actually do something about it.
	if ( school_master_core_active() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	// Respect a per-user dismissal.
	if ( get_user_meta( get_current_user_id(), 'school_master_dismiss_plugin_notice', true ) ) {
		return;
	}

	$slug   = 'school-master-core';
	$file   = 'school-master-core/school-master-core.php';
	$action = school_master_plugin_action( $slug, $file );

	$dismiss_url = wp_nonce_url(
		add_query_arg( 'school_master_dismiss_plugin_notice', '1' ),
		'school_master_dismiss_plugin_notice'
	);
	?>
	<div class="notice notice-warning is-dismissible school-master-plugin-notice">
		<p>
			<strong><?php esc_html_e( 'School Master', 'school-master' ); ?>:</strong>
			<?php esc_html_e( 'This theme needs the free “School Master Core” plugin for its courses, notices, faculty, events and gallery. Without it the homepage will look empty.', 'school-master' ); ?>
		</p>
		<p>
			<?php if ( $action ) : ?>
				<a href="<?php echo esc_url( $action['url'] ); ?>" class="button button-primary"><?php echo esc_html( $action['label'] ); ?></a>
			<?php endif; ?>
			<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button-link" style="margin-left:8px;"><?php esc_html_e( 'Dismiss', 'school-master' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'school_master_plugin_notice' );

/**
 * Build the correct install/activate action for the notice button.
 *
 * Returns an install link if the plugin isn't present, an activate link if it
 * is installed but inactive, or null if we can't offer a safe action.
 *
 * @param string $slug Plugin slug (folder name on WordPress.org).
 * @param string $file Plugin main file, relative to the plugins dir.
 * @return array{url:string,label:string}|null
 */
function school_master_plugin_action( $slug, $file ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$installed = array_keys( get_plugins() );

	// Installed but not active → offer activation.
	if ( in_array( $file, $installed, true ) ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return null;
		}

		return array(
			'url'   => wp_nonce_url(
				self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ),
				'activate-plugin_' . $file
			),
			'label' => __( 'Activate School Master Core', 'school-master' ),
		);
	}

	// Not installed → offer install from the repo.
	return array(
		'url'   => wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=' . rawurlencode( $slug ) ),
			'install-plugin_' . $slug
		),
		'label' => __( 'Install School Master Core', 'school-master' ),
	);
}

/**
 * Persist the notice dismissal for the current user.
 *
 * @return void
 */
function school_master_dismiss_plugin_notice() {
	if ( empty( $_GET['school_master_dismiss_plugin_notice'] ) ) {
		return;
	}

	check_admin_referer( 'school_master_dismiss_plugin_notice' );

	update_user_meta( get_current_user_id(), 'school_master_dismiss_plugin_notice', 1 );

	// Redirect back without the query args so a refresh doesn't re-trigger.
	wp_safe_redirect( remove_query_arg( array( 'school_master_dismiss_plugin_notice', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'school_master_dismiss_plugin_notice' );
