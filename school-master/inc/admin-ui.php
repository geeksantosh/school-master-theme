<?php
/**
 * Admin brand layer and dashboard shortcuts.
 *
 * Two jobs:
 *
 * 1. Load `assets/css/admin.css`, which recolours the admin chrome in the
 *    school palette. Cosmetic only — see that file for why.
 * 2. Replace the stock dashboard widgets a school never reads with a grid of
 *    task shortcuts.
 *
 * The shortcuts live in a dashboard widget rather than the welcome panel
 * because core gates the welcome panel on `edit_theme_options`
 * (wp-admin/index.php), which Editors do not have — the exact people these
 * shortcuts exist for would never see it.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load the admin stylesheet on every admin screen.
 *
 * @return void
 */
function school_master_admin_assets() {
	wp_enqueue_style(
		'school-master-admin',
		SCHOOL_MASTER_URI . '/assets/css/admin.css',
		array( 'dashicons' ),
		school_master_asset_version( '/assets/css/admin.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'school_master_admin_assets' );

/**
 * Build the shortcut list for the current user.
 *
 * Every entry is checked twice: the post type must actually be registered
 * (the companion plugin may be inactive) and the user must hold the
 * capability for it, so nobody is shown a tile that 403s when clicked.
 *
 * @return array[] List of shortcuts with label, desc, icon and url keys.
 */
function school_master_dashboard_shortcuts() {
	$shortcuts = array();

	$types = array(
		'sm_notice'  => array(
			'label' => __( 'Add Notice', 'school-master' ),
			'desc'  => __( 'Publish an announcement', 'school-master' ),
			'icon'  => 'dashicons-megaphone',
			'new'   => true,
		),
		'sm_event'   => array(
			'label' => __( 'Add Event', 'school-master' ),
			'desc'  => __( 'Put a date on the calendar', 'school-master' ),
			'icon'  => 'dashicons-calendar-alt',
			'new'   => true,
		),
		'sm_gallery' => array(
			'label' => __( 'Gallery', 'school-master' ),
			'desc'  => __( 'Manage photo albums', 'school-master' ),
			'icon'  => 'dashicons-format-gallery',
			'new'   => false,
		),
		'sm_course'  => array(
			'label' => __( 'Courses', 'school-master' ),
			'desc'  => __( 'Edit programs on offer', 'school-master' ),
			'icon'  => 'dashicons-welcome-learn-more',
			'new'   => false,
		),
		'sm_faculty' => array(
			'label' => __( 'Faculty', 'school-master' ),
			'desc'  => __( 'Update staff profiles', 'school-master' ),
			'icon'  => 'dashicons-groups',
			'new'   => false,
		),
	);

	foreach ( $types as $type => $item ) {
		$object = get_post_type_object( $type );

		if ( ! $object ) {
			continue;
		}

		$capability = $item['new'] ? $object->cap->create_posts : $object->cap->edit_posts;

		if ( ! current_user_can( $capability ) ) {
			continue;
		}

		$shortcuts[] = array(
			'label' => $item['label'],
			'desc'  => $item['desc'],
			'icon'  => $item['icon'],
			'url'   => $item['new']
				? admin_url( 'post-new.php?post_type=' . $type )
				: admin_url( 'edit.php?post_type=' . $type ),
		);
	}

	if ( function_exists( 'school_master_video_capability' ) && current_user_can( school_master_video_capability() ) ) {
		$shortcuts[] = array(
			'label' => __( 'Campus Video', 'school-master' ),
			'desc'  => __( 'Swap the homepage video', 'school-master' ),
			'icon'  => 'dashicons-video-alt3',
			'url'   => admin_url( 'admin.php?page=school-master-video' ),
		);
	}

	$shortcuts[] = array(
		'label' => __( 'View Website', 'school-master' ),
		'desc'  => __( 'See the live site', 'school-master' ),
		'icon'  => 'dashicons-external',
		'url'   => home_url( '/' ),
	);

	/**
	 * Filter the dashboard shortcut tiles.
	 *
	 * @param array[] $shortcuts List of shortcuts.
	 */
	return apply_filters( 'school_master_dashboard_shortcuts', $shortcuts );
}

/**
 * Render the shortcuts widget.
 *
 * @return void
 */
function school_master_dashboard_panel() {
	$shortcuts = school_master_dashboard_shortcuts();

	if ( empty( $shortcuts ) ) {
		return;
	}

	$user = wp_get_current_user();
	$name = $user->first_name ? $user->first_name : $user->display_name;
	?>
	<p class="sm-shortcuts__intro">
		<?php
		printf(
			/* translators: %s: current user's name. */
			esc_html__( 'Welcome back, %s. What would you like to do today?', 'school-master' ),
			esc_html( $name )
		);
		?>
	</p>

	<div class="sm-shortcuts__grid">
		<?php foreach ( $shortcuts as $shortcut ) : ?>
			<a class="sm-shortcut" href="<?php echo esc_url( $shortcut['url'] ); ?>">
				<span class="dashicons <?php echo esc_attr( $shortcut['icon'] ); ?>" aria-hidden="true"></span>
				<span class="sm-shortcut__label"><?php echo esc_html( $shortcut['label'] ); ?></span>
				<span class="sm-shortcut__desc"><?php echo esc_html( $shortcut['desc'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Swap the stock dashboard widgets for the shortcuts panel.
 *
 * "At a Glance" and Site Health are left alone: they carry real information.
 * The three removed here are the ones that only ever describe WordPress
 * itself, which is noise on a school site.
 *
 * @return void
 */
function school_master_dashboard_setup() {
	$remove = apply_filters(
		'school_master_remove_dashboard_widgets',
		array(
			'dashboard_primary'     => 'side',   // WordPress Events and News.
			'dashboard_quick_press' => 'side',   // Quick Draft.
			'dashboard_activity'    => 'normal', // Activity.
		)
	);

	foreach ( $remove as $widget => $context ) {
		remove_meta_box( $widget, 'dashboard', $context );
	}

	wp_add_dashboard_widget(
		'school_master_shortcuts',
		__( 'Quick Actions', 'school-master' ),
		'school_master_dashboard_panel',
		null,
		null,
		'normal',
		'high'
	);
}
add_action( 'wp_dashboard_setup', 'school_master_dashboard_setup' );
