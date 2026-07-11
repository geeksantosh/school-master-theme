<?php
/**
 * One-click demo content importer.
 *
 * Seeds a fresh install with representative content and homepage settings so a
 * buyer sees the finished layout immediately, then edits from there. Content
 * types come from the companion plugin, so import is only offered when it is
 * active. Everything created is tracked in an option so it can be removed again
 * cleanly, without touching content the school added itself.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Name of the option that tracks everything the importer created.
 *
 * @return string
 */
function school_master_demo_option() {
	return 'school_master_demo_data';
}

/**
 * Register the Demo Content admin page under Appearance.
 *
 * @return void
 */
function school_master_demo_menu() {
	add_theme_page(
		__( 'Demo Content', 'school-master' ),
		__( 'Demo Content', 'school-master' ),
		'manage_options',
		'school-master-demo',
		'school_master_demo_page'
	);
}
add_action( 'admin_menu', 'school_master_demo_menu' );

/**
 * Render the Demo Content admin page.
 *
 * @return void
 */
function school_master_demo_page() {
	$imported    = get_option( school_master_demo_option() );
	$has_plugin  = defined( 'SMCORE_VERSION' );
	$notice      = isset( $_GET['sm_demo'] ) ? sanitize_key( wp_unslash( $_GET['sm_demo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'School Master — Demo Content', 'school-master' ); ?></h1>

		<?php if ( 'imported' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Demo content imported. Visit your homepage to see it.', 'school-master' ); ?></p></div>
		<?php elseif ( 'removed' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Demo content removed.', 'school-master' ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! $has_plugin ) : ?>
			<div class="notice notice-warning">
				<p><?php esc_html_e( 'Install and activate the "School Master Core" plugin first — the demo content needs the courses, notices and faculty it provides.', 'school-master' ); ?></p>
			</div>
			<?php return; ?>
		<?php endif; ?>

		<p style="max-width:640px">
			<?php esc_html_e( 'This creates sample courses, notices, faculty, events and downloads, fills in the homepage sections, and builds a starter navigation menu. It is meant as a starting point — edit or delete anything afterwards.', 'school-master' ); ?>
		</p>

		<?php if ( $imported ) : ?>
			<p><strong><?php esc_html_e( 'Demo content is currently installed.', 'school-master' ); ?></strong></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Remove all demo content this importer created? Content you added yourself is kept.', 'school-master' ) ); ?>');">
				<input type="hidden" name="action" value="school_master_demo" />
				<input type="hidden" name="demo_action" value="remove" />
				<?php wp_nonce_field( 'school_master_demo' ); ?>
				<?php submit_button( __( 'Remove Demo Content', 'school-master' ), 'delete' ); ?>
			</form>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="school_master_demo" />
				<input type="hidden" name="demo_action" value="import" />
				<?php wp_nonce_field( 'school_master_demo' ); ?>
				<?php submit_button( __( 'Import Demo Content', 'school-master' ), 'primary' ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Handle the import/remove form submission.
 *
 * @return void
 */
function school_master_demo_handle() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'school-master' ) );
	}

	check_admin_referer( 'school_master_demo' );

	$do = isset( $_POST['demo_action'] ) ? sanitize_key( wp_unslash( $_POST['demo_action'] ) ) : '';

	if ( 'import' === $do && defined( 'SMCORE_VERSION' ) ) {
		school_master_demo_import();
		$result = 'imported';
	} elseif ( 'remove' === $do ) {
		school_master_demo_remove();
		$result = 'removed';
	} else {
		$result = '';
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'    => 'school-master-demo',
				'sm_demo' => $result,
			),
			admin_url( 'themes.php' )
		)
	);
	exit;
}
add_action( 'admin_post_school_master_demo', 'school_master_demo_handle' );

/**
 * Insert a demo post and record its ID for later cleanup.
 *
 * @param string $type    Post type.
 * @param string $title   Title.
 * @param string $content Body content.
 * @param array  $meta    Map of field name (no `_sm_`) => value.
 * @param array  $data    Tracking array (by reference).
 * @param string $bucket  Which tracking bucket to file the ID under.
 * @return int Inserted post ID, or 0 on failure.
 */
function school_master_demo_insert( $type, $title, $content, array $meta, array &$data, $bucket = 'posts' ) {
	$id = wp_insert_post(
		array(
			'post_type'    => $type,
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
		),
		true
	);

	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $id, '_sm_' . $key, $value );
	}

	$data[ $bucket ][] = $id;

	return $id;
}

/**
 * Create (or reuse) a term and record newly created ones for cleanup.
 *
 * @param string $name Term name.
 * @param string $tax  Taxonomy.
 * @param array  $data Tracking array (by reference).
 * @return int Term ID, or 0 on failure.
 */
function school_master_demo_term( $name, $tax, array &$data ) {
	$existing = term_exists( $name, $tax );

	if ( $existing ) {
		return (int) $existing['term_id'];
	}

	$new = wp_insert_term( $name, $tax );

	if ( is_wp_error( $new ) ) {
		return 0;
	}

	$data['terms'][] = array(
		'id'  => (int) $new['term_id'],
		'tax' => $tax,
	);

	return (int) $new['term_id'];
}

/**
 * Seed demo content, homepage settings and a starter menu.
 *
 * @return void
 */
function school_master_demo_import() {
	// Don't double-import.
	if ( get_option( school_master_demo_option() ) ) {
		return;
	}

	$data = array(
		'posts' => array(),
		'pages' => array(),
		'terms' => array(),
		'menu'  => 0,
	);

	school_master_demo_seed_courses( $data );
	school_master_demo_seed_notices( $data );
	school_master_demo_seed_faculty( $data );
	school_master_demo_seed_events( $data );
	school_master_demo_seed_downloads( $data );
	school_master_demo_seed_settings();
	school_master_demo_seed_pages( $data );
	school_master_demo_seed_menu( $data );

	update_option( school_master_demo_option(), $data );
}

/**
 * Seed sample courses under a couple of categories.
 *
 * @param array $data Tracking array (by reference).
 * @return void
 */
function school_master_demo_seed_courses( array &$data ) {
	$eng = school_master_demo_term( __( 'Engineering', 'school-master' ), 'sm_course_cat', $data );
	$it  = school_master_demo_term( __( 'Information Technology', 'school-master' ), 'sm_course_cat', $data );

	$courses = array(
		array(
			'title' => __( 'Diploma in Civil Engineering', 'school-master' ),
			'cat'   => $eng,
			'meta'  => array( 'duration' => '3 years', 'seats' => 48, 'fee' => 'Rs. 45,000 / year', 'eligibility' => 'SEE passed with Science and Mathematics.' ),
		),
		array(
			'title' => __( 'Diploma in Electrical Engineering', 'school-master' ),
			'cat'   => $eng,
			'meta'  => array( 'duration' => '3 years', 'seats' => 48, 'fee' => 'Rs. 45,000 / year', 'eligibility' => 'SEE passed with Science and Mathematics.' ),
		),
		array(
			'title' => __( 'Diploma in Computer Engineering', 'school-master' ),
			'cat'   => $it,
			'meta'  => array( 'duration' => '3 years', 'seats' => 48, 'fee' => 'Rs. 48,000 / year', 'eligibility' => 'SEE passed with Science and Mathematics.' ),
		),
		array(
			'title' => __( 'Certificate in IT Support', 'school-master' ),
			'cat'   => $it,
			'meta'  => array( 'duration' => '18 months', 'seats' => 40, 'fee' => 'Rs. 30,000 / year', 'eligibility' => 'SEE appeared.' ),
		),
	);

	$body = __( 'A skills-focused program combining classroom theory with extensive practical, lab and on-the-job training. Graduates leave ready for employment or further study.', 'school-master' );

	foreach ( $courses as $course ) {
		$id = school_master_demo_insert( 'sm_course', $course['title'], $body, $course['meta'], $data );

		if ( $id && $course['cat'] ) {
			wp_set_object_terms( $id, array( $course['cat'] ), 'sm_course_cat' );
		}
	}
}

/**
 * Seed sample notices, including one important pinned notice.
 *
 * @param array $data Tracking array (by reference).
 * @return void
 */
function school_master_demo_seed_notices( array &$data ) {
	$notices = array(
		array( __( 'Admissions Open for the New Academic Session', 'school-master' ), '1' ),
		array( __( 'Scholarship Applications Now Being Accepted', 'school-master' ), '' ),
		array( __( 'Notice Regarding First-Term Examination Routine', 'school-master' ), '' ),
		array( __( 'Annual Sports Week Schedule Announced', 'school-master' ), '' ),
	);

	$body = __( 'Please read this notice carefully. For further details, contact the administration office during working hours.', 'school-master' );

	foreach ( $notices as $notice ) {
		$meta = $notice[1] ? array( 'is_important' => '1' ) : array();
		school_master_demo_insert( 'sm_notice', $notice[0], $body, $meta, $data );
	}
}

/**
 * Seed sample faculty members.
 *
 * @param array $data Tracking array (by reference).
 * @return void
 */
function school_master_demo_seed_faculty( array &$data ) {
	$faculty = array(
		array( __( 'Ramesh Sharma', 'school-master' ), array( 'designation' => 'Principal', 'qualification' => 'M.Sc. Engineering', 'email' => 'principal@example.edu.np' ) ),
		array( __( 'Sita Gurung', 'school-master' ), array( 'designation' => 'Head of Department', 'qualification' => 'M.E. Computer', 'email' => 'hod.it@example.edu.np' ) ),
		array( __( 'Bikash Thapa', 'school-master' ), array( 'designation' => 'Senior Lecturer', 'qualification' => 'B.E. Civil', 'email' => 'bikash@example.edu.np' ) ),
		array( __( 'Anita Rai', 'school-master' ), array( 'designation' => 'Lecturer', 'qualification' => 'B.E. Electrical', 'email' => 'anita@example.edu.np' ) ),
	);

	$body = __( 'A dedicated educator committed to hands-on, student-centred learning and to preparing graduates for successful careers.', 'school-master' );

	foreach ( $faculty as $member ) {
		school_master_demo_insert( 'sm_faculty', $member[0], $body, $member[1], $data );
	}
}

/**
 * Seed a couple of upcoming events.
 *
 * @param array $data Tracking array (by reference).
 * @return void
 */
function school_master_demo_seed_events( array &$data ) {
	$base = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

	$events = array(
		array(
			'title' => __( 'Open House & Campus Tour', 'school-master' ),
			'meta'  => array(
				'start_date' => gmdate( 'Y-m-d', $base + ( 14 * DAY_IN_SECONDS ) ),
				'location'   => __( 'Main Campus', 'school-master' ),
			),
		),
		array(
			'title' => __( 'Annual Technical Exhibition', 'school-master' ),
			'meta'  => array(
				'start_date' => gmdate( 'Y-m-d', $base + ( 30 * DAY_IN_SECONDS ) ),
				'end_date'   => gmdate( 'Y-m-d', $base + ( 31 * DAY_IN_SECONDS ) ),
				'location'   => __( 'College Auditorium', 'school-master' ),
			),
		),
	);

	$body = __( 'Join us for this event. Students, parents and the community are all welcome to attend.', 'school-master' );

	foreach ( $events as $event ) {
		school_master_demo_insert( 'sm_event', $event['title'], $body, $event['meta'], $data );
	}
}

/**
 * Seed a couple of download entries.
 *
 * @param array $data Tracking array (by reference).
 * @return void
 */
function school_master_demo_seed_downloads( array &$data ) {
	$downloads = array(
		__( 'Admission Form', 'school-master' ),
		__( 'Academic Calendar', 'school-master' ),
		__( 'Fee Structure', 'school-master' ),
	);

	$body = __( 'Attach the file for this download from the edit screen, then it becomes available on the Downloads page.', 'school-master' );

	foreach ( $downloads as $title ) {
		school_master_demo_insert( 'sm_download', $title, $body, array(), $data );
	}
}

/**
 * Fill in homepage Customizer settings that are empty by default.
 *
 * Only sets values that ship blank (hero button target, stats counters,
 * welcome text). Existing values a school already set are left untouched.
 *
 * @return void
 */
function school_master_demo_seed_settings() {
	$courses_url = get_post_type_archive_link( 'sm_course' );
	$notices_url = get_post_type_archive_link( 'sm_notice' );

	$defaults = array(
		'hero_btn_url'     => $courses_url ? $courses_url : home_url( '/' ),
		'cta_btn_url'      => home_url( '/contact/' ),
		'welcome_text'     => __( 'We are a technical institution dedicated to producing skilled, employable graduates. Through modern facilities, experienced faculty and a practical, industry-aligned curriculum, we help students turn ambition into a career.', 'school-master' ),
		'stat_1_number'    => '1200+',
		'stat_1_label'     => __( 'Graduates', 'school-master' ),
		'stat_2_number'    => '35',
		'stat_2_label'     => __( 'Expert Faculty', 'school-master' ),
		'stat_3_number'    => '12',
		'stat_3_label'     => __( 'Programs', 'school-master' ),
		'stat_4_number'    => '95%',
		'stat_4_label'     => __( 'Placement Rate', 'school-master' ),
		// Top navigation buttons (far right of the top bar).
		'topbar_btn1_text' => __( 'Apply Now', 'school-master' ),
		'topbar_btn1_url'  => home_url( '/contact/' ),
		'topbar_btn2_text' => __( 'Notices', 'school-master' ),
		'topbar_btn2_url'  => $notices_url ? $notices_url : home_url( '/' ),
	);

	foreach ( $defaults as $key => $value ) {
		if ( '' === (string) get_theme_mod( 'school_master_' . $key, '' ) ) {
			set_theme_mod( 'school_master_' . $key, $value );
		}
	}
}

/**
 * Create Home and Blog pages and set a static front page.
 *
 * @param array $data Tracking array (by reference).
 * @return void
 */
function school_master_demo_seed_pages( array &$data ) {
	// Leave an existing static front page alone.
	if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
		return;
	}

	$home = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_title'  => __( 'Home', 'school-master' ),
			'post_status' => 'publish',
		),
		true
	);

	$blog = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_title'  => __( 'News', 'school-master' ),
			'post_status' => 'publish',
		),
		true
	);

	if ( ! is_wp_error( $home ) && $home ) {
		$data['pages'][] = $home;
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}

	if ( ! is_wp_error( $blog ) && $blog ) {
		$data['pages'][] = $blog;
		update_option( 'page_for_posts', $blog );
	}
}

/**
 * Build a starter primary menu and assign it to the primary location.
 *
 * @param array $data Tracking array (by reference).
 * @return void
 */
function school_master_demo_seed_menu( array &$data ) {
	$menu_name = __( 'Primary Menu', 'school-master' );

	if ( wp_get_nav_menu_object( $menu_name ) ) {
		return;
	}

	$menu_id = wp_create_nav_menu( $menu_name );

	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	$data['menu'] = $menu_id;

	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'   => __( 'Home', 'school-master' ),
			'menu-item-url'     => home_url( '/' ),
			'menu-item-status'  => 'publish',
		)
	);

	$archives = array(
		'sm_course'   => __( 'Courses', 'school-master' ),
		'sm_notice'   => __( 'Notices', 'school-master' ),
		'sm_faculty'  => __( 'Faculty', 'school-master' ),
		'sm_event'    => __( 'Events', 'school-master' ),
		'sm_download' => __( 'Downloads', 'school-master' ),
	);

	foreach ( $archives as $type => $label ) {
		$url = get_post_type_archive_link( $type );

		if ( ! $url ) {
			continue;
		}

		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $label,
				'menu-item-url'    => $url,
				'menu-item-status' => 'publish',
			)
		);
	}

	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Remove everything the importer created, leaving school-added content intact.
 *
 * @return void
 */
function school_master_demo_remove() {
	$data = get_option( school_master_demo_option() );

	if ( ! $data ) {
		return;
	}

	// Delete demo posts and pages.
	foreach ( array( 'posts', 'pages' ) as $bucket ) {
		if ( empty( $data[ $bucket ] ) ) {
			continue;
		}

		foreach ( $data[ $bucket ] as $post_id ) {
			wp_delete_post( (int) $post_id, true );
		}
	}

	// If the front page we created is gone, reset the reading settings.
	if ( ! empty( $data['pages'] ) ) {
		$front = (int) get_option( 'page_on_front' );

		if ( in_array( $front, array_map( 'intval', $data['pages'] ), true ) ) {
			update_option( 'show_on_front', 'posts' );
			update_option( 'page_on_front', 0 );
			update_option( 'page_for_posts', 0 );
		}
	}

	// Delete terms we created.
	if ( ! empty( $data['terms'] ) ) {
		foreach ( $data['terms'] as $term ) {
			wp_delete_term( (int) $term['id'], $term['tax'] );
		}
	}

	// Delete the starter menu.
	if ( ! empty( $data['menu'] ) ) {
		wp_delete_nav_menu( (int) $data['menu'] );
	}

	delete_option( school_master_demo_option() );
}
