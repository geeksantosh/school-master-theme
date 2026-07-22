<?php
/**
 * Customizer configuration — every per-school setting lives here.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Customizer panels, sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function school_master_customize_register( $wp_customize ) {
	// Live-preview the site title/description.
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	/* -----------------------------------------------------------------
	 * Helper closures to cut down on repetition.
	 * ----------------------------------------------------------------- */
	$add_text = function ( $id, $label, $section, $default = '', $type = 'text' ) use ( $wp_customize ) {
		$wp_customize->add_setting(
			'school_master_' . $id,
			array(
				'default'           => $default,
				'sanitize_callback' => 'url' === $type ? 'esc_url_raw' : ( 'email' === $type ? 'sanitize_email' : 'sanitize_text_field' ),
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'school_master_' . $id,
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => 'url' === $type ? 'url' : ( 'email' === $type ? 'email' : $type ),
			)
		);
	};

	$add_toggle = function ( $id, $label, $section, $default = true ) use ( $wp_customize ) {
		$wp_customize->add_setting(
			'school_master_' . $id,
			array(
				'default'           => $default,
				'sanitize_callback' => 'school_master_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'school_master_' . $id,
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => 'checkbox',
			)
		);
	};

	/* -----------------------------------------------------------------
	 * 1. Top Bar & Contact.
	 * ----------------------------------------------------------------- */
	$wp_customize->add_section(
		'school_master_topbar',
		array(
			'title'    => __( 'Top Bar & Contact', 'school-master' ),
			'priority' => 30,
		)
	);
	$add_toggle( 'topbar_enable', __( 'Show top bar', 'school-master' ), 'school_master_topbar', true );
	$add_text( 'contact_address', __( 'Address', 'school-master' ), 'school_master_topbar' );
	$add_text( 'contact_phone', __( 'Phone', 'school-master' ), 'school_master_topbar' );
	$add_text( 'contact_email', __( 'Email', 'school-master' ), 'school_master_topbar', '', 'email' );

	// Top navigation buttons (e.g. Apply Now / Login). Blank label = hidden.
	// They sit at the far right of the top bar; if the top bar is toggled off
	// but a button is set, the bar still appears so the buttons are not lost.
	for ( $b = 1; $b <= 2; $b++ ) {
		/* translators: %d: button number. */
		$add_text( "topbar_btn{$b}_text", sprintf( __( 'Button %d — Label', 'school-master' ), $b ), 'school_master_topbar' );
		/* translators: %d: button number. */
		$add_text( "topbar_btn{$b}_url", sprintf( __( 'Button %d — URL', 'school-master' ), $b ), 'school_master_topbar', '', 'url' );

		$wp_customize->add_setting(
			"school_master_topbar_btn{$b}_style",
			array(
				'default'           => 1 === $b ? 'solid' : 'outline',
				'sanitize_callback' => 'school_master_sanitize_select',
			)
		);
		$wp_customize->add_control(
			"school_master_topbar_btn{$b}_style",
			array(
				/* translators: %d: button number. */
				'label'   => sprintf( __( 'Button %d — Style', 'school-master' ), $b ),
				'section' => 'school_master_topbar',
				'type'    => 'select',
				'choices' => array(
					'solid'   => __( 'Solid (filled)', 'school-master' ),
					'outline' => __( 'Outline', 'school-master' ),
				),
			)
		);
	}

	/* -----------------------------------------------------------------
	 * 1b. Notice Ticker & Popup (site-wide, driven by the Notices content).
	 * ----------------------------------------------------------------- */
	$wp_customize->add_section(
		'school_master_notice_bar',
		array(
			'title'       => __( 'Notice Ticker & Popup', 'school-master' ),
			'description' => __( 'A scrolling ticker of the latest notices, and an optional popup on first visit. Both read from your Notices, so add notices for them to appear.', 'school-master' ),
			'priority'    => 35,
		)
	);

	// Scrolling ticker.
	$add_toggle( 'ticker_enable', __( 'Show notice ticker', 'school-master' ), 'school_master_notice_bar', true );
	$add_text( 'ticker_label', __( 'Ticker label', 'school-master' ), 'school_master_notice_bar', __( 'Notices', 'school-master' ) );
	school_master_number_control( $wp_customize, 'ticker_count', __( 'Number of notices in ticker', 'school-master' ), 'school_master_notice_bar', 6 );

	$wp_customize->add_setting(
		'school_master_ticker_speed',
		array(
			'default'           => 'normal',
			'sanitize_callback' => 'school_master_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'school_master_ticker_speed',
		array(
			'label'   => __( 'Ticker speed', 'school-master' ),
			'section' => 'school_master_notice_bar',
			'type'    => 'select',
			'choices' => array(
				'slow'   => __( 'Slow', 'school-master' ),
				'normal' => __( 'Normal', 'school-master' ),
				'fast'   => __( 'Fast', 'school-master' ),
			),
		)
	);

	// Popup on first visit.
	$add_toggle( 'popup_enable', __( 'Show notice popup on first visit', 'school-master' ), 'school_master_notice_bar', false );

	$wp_customize->add_setting(
		'school_master_popup_source',
		array(
			'default'           => 'important',
			'sanitize_callback' => 'school_master_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'school_master_popup_source',
		array(
			'label'       => __( 'Popup content', 'school-master' ),
			'description' => __( '"Important notices" pops up each notice marked important (newest first, one at a time) until its optional "Popup until" date passes. "Custom message" uses the fields below.', 'school-master' ),
			'section'     => 'school_master_notice_bar',
			'type'        => 'select',
			'choices'     => array(
				'important' => __( 'Important notices', 'school-master' ),
				'custom'    => __( 'Custom message', 'school-master' ),
			),
		)
	);
	$add_text( 'popup_title', __( 'Popup title (custom)', 'school-master' ), 'school_master_notice_bar', __( 'Important Notice', 'school-master' ) );
	$wp_customize->add_setting(
		'school_master_popup_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'school_master_popup_text',
		array(
			'label'   => __( 'Popup message (custom)', 'school-master' ),
			'section' => 'school_master_notice_bar',
			'type'    => 'textarea',
		)
	);
	$add_text( 'popup_btn_text', __( 'Popup button text', 'school-master' ), 'school_master_notice_bar', __( 'Read more', 'school-master' ) );
	$add_text( 'popup_btn_url', __( 'Popup button URL (custom)', 'school-master' ), 'school_master_notice_bar', '', 'url' );

	/* -----------------------------------------------------------------
	 * 2. Colors.
	 * ----------------------------------------------------------------- */
	$wp_customize->add_section(
		'school_master_colors',
		array(
			'title'    => __( 'Brand Colors', 'school-master' ),
			'priority' => 40,
		)
	);
	$colors = array(
		'primary_color'   => array( __( 'Primary Color', 'school-master' ), '#26236c' ),
		'secondary_color' => array( __( 'Secondary / Accent Color', 'school-master' ), '#f25708' ),
		'dark_color'      => array( __( 'Dark Color (footer, headings)', 'school-master' ), '#1b1852' ),
	);
	foreach ( $colors as $id => $data ) {
		$wp_customize->add_setting(
			'school_master_' . $id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'school_master_' . $id,
				array(
					'label'   => $data[0],
					'section' => 'school_master_colors',
				)
			)
		);
	}

	/* -----------------------------------------------------------------
	 * 3. Social Links.
	 * ----------------------------------------------------------------- */
	$wp_customize->add_section(
		'school_master_social',
		array(
			'title'    => __( 'Social Links', 'school-master' ),
			'priority' => 50,
		)
	);
	foreach ( school_master_social_networks() as $key => $label ) {
		$add_text( 'social_' . $key, $label, 'school_master_social', '', 'url' );
	}

	/* -----------------------------------------------------------------
	 * 4. Homepage Sections (panel).
	 * ----------------------------------------------------------------- */
	$wp_customize->add_panel(
		'school_master_homepage',
		array(
			'title'       => __( 'Homepage Sections', 'school-master' ),
			'description' => __( 'Toggle and configure each homepage section. Sections appear top to bottom in the order listed here.', 'school-master' ),
			'priority'    => 60,
		)
	);

	// 4a. Hero.
	$wp_customize->add_section(
		'school_master_hero',
		array(
			'title' => __( 'Hero', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_hero_enable', __( 'Enable Hero', 'school-master' ), 'school_master_hero', true );

	$wp_customize->add_setting(
		'school_master_hero_type',
		array(
			'default'           => 'image',
			'sanitize_callback' => 'school_master_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'school_master_hero_type',
		array(
			'label'   => __( 'Hero Type', 'school-master' ),
			'section' => 'school_master_hero',
			'type'    => 'select',
			'choices' => array(
				'image' => __( 'Background Image', 'school-master' ),
				'video' => __( 'YouTube Video', 'school-master' ),
			),
		)
	);

	$wp_customize->add_setting(
		'school_master_hero_image',
		array( 'sanitize_callback' => 'esc_url_raw' )
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'school_master_hero_image',
			array(
				'label'   => __( 'Hero Background Image', 'school-master' ),
				'section' => 'school_master_hero',
			)
		)
	);
	$add_text( 'hero_video_url', __( 'YouTube Video URL', 'school-master' ), 'school_master_hero', '', 'url' );
	$add_text( 'hero_title', __( 'Hero Title', 'school-master' ), 'school_master_hero', __( 'Welcome to Our Campus', 'school-master' ) );
	$add_text( 'hero_subtitle', __( 'Hero Subtitle', 'school-master' ), 'school_master_hero', __( 'Enlightenment through Education', 'school-master' ) );
	$add_text( 'hero_btn_text', __( 'Button Text', 'school-master' ), 'school_master_hero', __( 'Explore Courses', 'school-master' ) );
	$add_text( 'hero_btn_url', __( 'Button URL', 'school-master' ), 'school_master_hero', '', 'url' );

	// 4b. Notice Board.
	$wp_customize->add_section(
		'school_master_notices',
		array(
			'title' => __( 'Notice Board', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_notices_enable', __( 'Enable Notice Board', 'school-master' ), 'school_master_notices', true );
	$add_text( 'notices_title', __( 'Section Title', 'school-master' ), 'school_master_notices', __( 'Notice Board', 'school-master' ) );
	school_master_number_control( $wp_customize, 'notices_count', __( 'Number of notices', 'school-master' ), 'school_master_notices', 5 );

	// 4c. Welcome.
	$wp_customize->add_section(
		'school_master_welcome',
		array(
			'title' => __( 'Welcome / About', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_welcome_enable', __( 'Enable Welcome', 'school-master' ), 'school_master_welcome', true );
	$add_text( 'welcome_title', __( 'Title', 'school-master' ), 'school_master_welcome', __( 'Welcome to Our Institution', 'school-master' ) );
	$wp_customize->add_setting(
		'school_master_welcome_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'school_master_welcome_text',
		array(
			'label'   => __( 'Welcome Text', 'school-master' ),
			'section' => 'school_master_welcome',
			'type'    => 'textarea',
		)
	);
	$wp_customize->add_setting(
		'school_master_welcome_image',
		array( 'sanitize_callback' => 'esc_url_raw' )
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'school_master_welcome_image',
			array(
				'label'   => __( 'Welcome Image', 'school-master' ),
				'section' => 'school_master_welcome',
			)
		)
	);

	// 4d. Courses.
	$wp_customize->add_section(
		'school_master_courses',
		array(
			'title' => __( 'Courses', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_courses_enable', __( 'Enable Courses', 'school-master' ), 'school_master_courses', true );
	$add_text( 'courses_title', __( 'Section Title', 'school-master' ), 'school_master_courses', __( 'Our Programs', 'school-master' ) );
	school_master_number_control( $wp_customize, 'courses_count', __( 'Number of courses', 'school-master' ), 'school_master_courses', 6 );

	// 4e. Why Us.
	$wp_customize->add_section(
		'school_master_whyus',
		array(
			'title' => __( 'Why Choose Us', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_whyus_enable', __( 'Enable Why Choose Us', 'school-master' ), 'school_master_whyus', true );
	$add_text( 'whyus_title', __( 'Section Title', 'school-master' ), 'school_master_whyus', __( 'Why Choose Us', 'school-master' ) );

	// 4e.5 Campus Video.
	$wp_customize->add_section(
		'school_master_video',
		array(
			'title' => __( 'Campus Video', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_video_enable', __( 'Enable Campus Video', 'school-master' ), 'school_master_video', true );
	$add_text( 'video_title', __( 'Section Title', 'school-master' ), 'school_master_video', __( 'Campus Life', 'school-master' ) );
	$add_text( 'video_url', __( 'YouTube Video URL', 'school-master' ), 'school_master_video', '', 'url' );
	$wp_customize->add_setting(
		'school_master_video_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'school_master_video_text',
		array(
			'label'       => __( 'Intro Text', 'school-master' ),
			'description' => __( 'Optional line shown above the video.', 'school-master' ),
			'section'     => 'school_master_video',
			'type'        => 'textarea',
		)
	);

	// 4f. Stats / Counters.
	$wp_customize->add_section(
		'school_master_stats',
		array(
			'title' => __( 'Statistics Counters', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_stats_enable', __( 'Enable Statistics', 'school-master' ), 'school_master_stats', true );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: counter number. */
		$add_text( "stat_{$i}_number", sprintf( __( 'Counter %d — Number', 'school-master' ), $i ), 'school_master_stats' );
		/* translators: %d: counter number. */
		$add_text( "stat_{$i}_label", sprintf( __( 'Counter %d — Label', 'school-master' ), $i ), 'school_master_stats' );
	}

	// 4g. News.
	$wp_customize->add_section(
		'school_master_news',
		array(
			'title' => __( 'Latest News', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_news_enable', __( 'Enable Latest News', 'school-master' ), 'school_master_news', true );
	$add_text( 'news_title', __( 'Section Title', 'school-master' ), 'school_master_news', __( 'Latest News', 'school-master' ) );

	// 4h. Testimonials.
	$wp_customize->add_section(
		'school_master_testimonials',
		array(
			'title' => __( 'Testimonials', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_testimonials_enable', __( 'Enable Testimonials', 'school-master' ), 'school_master_testimonials', true );
	$add_text( 'testimonials_title', __( 'Section Title', 'school-master' ), 'school_master_testimonials', __( 'What People Say', 'school-master' ) );
	school_master_number_control( $wp_customize, 'testimonials_count', __( 'Number of testimonials', 'school-master' ), 'school_master_testimonials', 100 );
	$add_toggle( 'testimonials_autoscroll', __( 'Auto-scroll the row when testimonials overflow the screen', 'school-master' ), 'school_master_testimonials', true );

	// 4i. Partners.
	$wp_customize->add_section(
		'school_master_partners',
		array(
			'title' => __( 'Partners', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_partners_enable', __( 'Enable Partners', 'school-master' ), 'school_master_partners', true );
	$add_text( 'partners_title', __( 'Section Title', 'school-master' ), 'school_master_partners', __( 'Our Partners', 'school-master' ) );
	$add_toggle( 'partners_autoscroll', __( 'Auto-scroll the row when logos overflow the screen', 'school-master' ), 'school_master_partners', true );

	// 4i.5 Gallery.
	$wp_customize->add_section(
		'school_master_gallery',
		array(
			'title' => __( 'Gallery', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_gallery_enable', __( 'Enable Gallery', 'school-master' ), 'school_master_gallery', true );
	$add_text( 'gallery_title', __( 'Section Title', 'school-master' ), 'school_master_gallery', __( 'Gallery', 'school-master' ) );
	school_master_number_control( $wp_customize, 'gallery_count', __( 'Number of gallery items', 'school-master' ), 'school_master_gallery', 100 );

	// 4j. Call to action.
	$wp_customize->add_section(
		'school_master_cta',
		array(
			'title' => __( 'Call to Action', 'school-master' ),
			'panel' => 'school_master_homepage',
		)
	);
	$add_toggle( 'section_cta_enable', __( 'Enable Call to Action', 'school-master' ), 'school_master_cta', true );
	$add_text( 'cta_title', __( 'Title', 'school-master' ), 'school_master_cta', __( 'Ready to Apply?', 'school-master' ) );
	$add_text( 'cta_btn_text', __( 'Button Text', 'school-master' ), 'school_master_cta', __( 'Apply Now', 'school-master' ) );
	$add_text( 'cta_btn_url', __( 'Button URL', 'school-master' ), 'school_master_cta', '', 'url' );

	/* -----------------------------------------------------------------
	 * 5. Footer.
	 * ----------------------------------------------------------------- */
	$wp_customize->add_section(
		'school_master_footer',
		array(
			'title'    => __( 'Footer', 'school-master' ),
			'priority' => 120,
		)
	);
	$wp_customize->add_setting(
		'school_master_footer_copyright',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'school_master_footer_copyright',
		array(
			'label'       => __( 'Copyright Text', 'school-master' ),
			'description' => __( 'Leave blank to use the site name and current year.', 'school-master' ),
			'section'     => 'school_master_footer',
			'type'        => 'text',
		)
	);
	$wp_customize->add_setting(
		'school_master_footer_credit_text',
		array(
			'default'           => 'Developed by Santosh Adhikari',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'school_master_footer_credit_text',
		array(
			'label'       => __( 'Credit Text', 'school-master' ),
			'description' => __( 'Shown after the copyright, e.g. "Developed by …". Leave blank to hide.', 'school-master' ),
			'section'     => 'school_master_footer',
			'type'        => 'text',
		)
	);
	$wp_customize->add_setting(
		'school_master_footer_credit_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'school_master_footer_credit_url',
		array(
			'label'       => __( 'Credit URL', 'school-master' ),
			'description' => __( 'Optional link for the credit text (opens in a new tab).', 'school-master' ),
			'section'     => 'school_master_footer',
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'school_master_customize_register' );

/**
 * Register a number control (small helper to keep register clean).
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $id          Setting id (no prefix).
 * @param string               $label       Label.
 * @param string               $section     Section id.
 * @param int                  $default     Default.
 * @return void
 */
function school_master_number_control( $wp_customize, $id, $label, $section, $default ) {
	$wp_customize->add_setting(
		'school_master_' . $id,
		array(
			'default'           => $default,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'school_master_' . $id,
		array(
			'label'       => $label,
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 12,
				'step' => 1,
			),
		)
	);
}

/**
 * Sanitize a checkbox value.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function school_master_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize a select value against the control's choices.
 *
 * @param string               $value   Value.
 * @param WP_Customize_Setting $setting Setting.
 * @return string
 */
function school_master_sanitize_select( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );

	if ( $control && isset( $control->choices[ $value ] ) ) {
		return $value;
	}

	return $setting->default;
}

/**
 * Load the Customizer live-preview script.
 *
 * @return void
 */
function school_master_customize_preview_js() {
	wp_enqueue_script(
		'school-master-customizer',
		SCHOOL_MASTER_URI . '/assets/js/customizer.js',
		array( 'customize-preview' ),
		SCHOOL_MASTER_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'school_master_customize_preview_js' );
