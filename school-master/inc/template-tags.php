<?php
/**
 * Reusable template helper functions.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a Customizer setting with a default.
 *
 * @param string $key     Setting key (without the `school_master_` prefix).
 * @param mixed  $default Default value.
 * @return mixed
 */
function school_master_option( $key, $default = '' ) {
	return get_theme_mod( 'school_master_' . $key, $default );
}

/**
 * Whether a given homepage section is enabled.
 *
 * @param string $section Section id (e.g. 'hero', 'notices').
 * @return bool
 */
function school_master_section_enabled( $section ) {
	return (bool) school_master_option( 'section_' . $section . '_enable', true );
}

/**
 * Build up to two uppercase initials from a name.
 *
 * Used as a fallback avatar when an item (e.g. a testimonial) has no photo.
 *
 * @param string $name Full name.
 * @return string One or two initials, or an empty string.
 */
function school_master_initials( $name ) {
	$name = trim( wp_strip_all_tags( (string) $name ) );

	if ( '' === $name ) {
		return '';
	}

	$initials = '';

	foreach ( preg_split( '/\s+/', $name ) as $word ) {
		if ( '' === $word ) {
			continue;
		}
		$initials .= mb_substr( $word, 0, 1 );
		if ( mb_strlen( $initials ) >= 2 ) {
			break;
		}
	}

	return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $initials ) : strtoupper( $initials );
}

/**
 * Output the site logo, falling back to the site title text.
 *
 * @return void
 */
function school_master_site_branding() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	$tag = ( is_front_page() && is_home() ) ? 'h1' : 'p';

	printf(
		'<%1$s class="site-title"><a href="%2$s" rel="home">%3$s</a></%1$s>',
		esc_attr( $tag ),
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) )
	);

	$description = get_bloginfo( 'description', 'display' );

	if ( $description ) {
		printf( '<p class="site-description">%s</p>', esc_html( $description ) );
	}
}

/**
 * Social networks a school can configure.
 *
 * @return array<string,string> Map of key => label.
 */
function school_master_social_networks() {
	return array(
		'facebook'  => __( 'Facebook', 'school-master' ),
		'instagram' => __( 'Instagram', 'school-master' ),
		'youtube'   => __( 'YouTube', 'school-master' ),
		'twitter'   => __( 'X / Twitter', 'school-master' ),
		'linkedin'  => __( 'LinkedIn', 'school-master' ),
		'tiktok'    => __( 'TikTok', 'school-master' ),
	);
}

/**
 * Inline SVG brand glyphs for the supported social networks.
 *
 * Self-contained so the icons always render with no external requests. Each
 * entry is a `<path>`/`<rect>` set drawn on a 24x24 viewBox and colored via
 * `currentColor`, so CSS controls the fill.
 *
 * @param string $key Network key.
 * @return string SVG markup, or empty string if unknown.
 */
function school_master_social_icon_svg( $key ) {
	$paths = array(
		'facebook'  => '<path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07c0 6.02 4.39 11.01 10.13 11.93v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.25h3.33l-.53 3.49h-2.8V24C19.61 23.08 24 18.09 24 12.07z"/>',
		'instagram' => '<path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41 1.27-.06 1.65-.07 4.85-.07M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63c-.79.31-1.46.72-2.13 1.38C1.35 2.68.94 3.35.63 4.14.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.72 1.46 1.38 2.13.67.66 1.34 1.07 2.13 1.38.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56.79-.31 1.46-.72 2.13-1.38.66-.67 1.07-1.34 1.38-2.13.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91-.31-.79-.72-1.46-1.38-2.13C21.32 1.35 20.65.94 19.86.63 19.1.33 18.22.13 16.95.07 15.67.01 15.26 0 12 0z"/><path d="M12 5.84A6.16 6.16 0 1018.16 12 6.16 6.16 0 0012 5.84zm0 10.16A4 4 0 1116 12a4 4 0 01-4 4z"/><circle cx="18.41" cy="5.59" r="1.44"/>',
		'youtube'   => '<path d="M23.5 6.19a3.02 3.02 0 00-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 00.5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 002.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 002.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81zM9.6 15.6V8.4l6.24 3.6z"/>',
		'twitter'   => '<path d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.41l-5.8-7.58-6.64 7.58H.46l8.6-9.83L0 1.15h7.6l5.24 6.93zm-1.29 19.5h2.04L6.48 3.24H4.29z"/>',
		'linkedin'  => '<path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.13 1.44-2.13 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.63-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zm1.78 13.02H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/>',
		'tiktok'    => '<path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.6-4.62V9.34a6.34 6.34 0 105.44 6.27V8.86a8.16 8.16 0 004.76 1.52V6.94a4.85 4.85 0 01-.38-.25z"/>',
	);

	return isset( $paths[ $key ] ) ? $paths[ $key ] : '';
}

/**
 * Render the configured social links.
 *
 * @param string $class Wrapper CSS class.
 * @return void
 */
function school_master_social_links( $class = 'social-links' ) {
	$links = array();

	foreach ( school_master_social_networks() as $key => $label ) {
		$url = school_master_option( 'social_' . $key );

		if ( $url ) {
			$links[ $key ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}

	if ( empty( $links ) ) {
		return;
	}

	$allowed_svg = array(
		'svg'    => array(
			'class'       => true,
			'width'       => true,
			'height'      => true,
			'viewbox'     => true,
			'fill'        => true,
			'xmlns'       => true,
			'aria-hidden' => true,
			'focusable'   => true,
			'role'        => true,
		),
		'path'   => array( 'd' => true, 'fill' => true ),
		'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
		'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
	);

	printf( '<ul class="%s">', esc_attr( $class ) );

	foreach ( $links as $key => $link ) {
		$svg = school_master_social_icon_svg( $key );

		$icon = $svg
			? sprintf(
				'<svg class="social-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">%s</svg>',
				$svg
			)
			: '';

		printf(
			'<li class="social-%1$s"><a href="%2$s" target="_blank" rel="noopener noreferrer" aria-label="%3$s"><span class="screen-reader-text">%3$s</span>%4$s</a></li>',
			esc_attr( $key ),
			esc_url( $link['url'] ),
			esc_attr( $link['label'] ),
			wp_kses( $icon, $allowed_svg )
		);
	}

	echo '</ul>';
}

/**
 * Output a labelled contact item if the option is set.
 *
 * @param string $key   Option key ('address', 'phone', 'email').
 * @param string $label Screen-reader label.
 * @return void
 */
function school_master_contact_item( $key, $label ) {
	$value = school_master_option( 'contact_' . $key );

	if ( ! $value ) {
		return;
	}

	$content = esc_html( $value );

	if ( 'phone' === $key ) {
		$content = sprintf( '<a href="tel:%s">%s</a>', esc_attr( preg_replace( '/[^0-9+]/', '', $value ) ), esc_html( $value ) );
	} elseif ( 'email' === $key ) {
		$content = sprintf( '<a href="mailto:%s">%s</a>', esc_attr( $value ), esc_html( $value ) );
	}

	printf(
		'<span class="contact-item contact-%1$s"><span class="screen-reader-text">%2$s: </span>%3$s</span>',
		esc_attr( $key ),
		esc_html( $label ),
		$content // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
	);
}

/**
 * Convert a YouTube watch/share URL into a privacy-friendly embed URL.
 *
 * Accepts full watch URLs, youtu.be short links and bare IDs.
 *
 * @param string $url YouTube URL or video ID.
 * @return string Embed URL, or empty string if the ID can't be parsed.
 */
function school_master_youtube_embed_url( $url ) {
	$id = '';

	if ( preg_match( '~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/|v/))([A-Za-z0-9_-]{11})~', $url, $m ) ) {
		$id = $m[1];
	} elseif ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $url ) ) {
		$id = $url;
	}

	if ( ! $id ) {
		return '';
	}

	return 'https://www.youtube-nocookie.com/embed/' . $id;
}

/**
 * Provide default "Why Choose Us" features when nothing else supplies them.
 *
 * A school can override these entirely by returning its own array from the
 * `school_master_whyus_features` filter (or via the demo importer). Kept here
 * so the section renders sensibly on a fresh activation instead of vanishing.
 *
 * @param array $features Existing features (empty by default).
 * @return array
 */
function school_master_default_whyus_features( $features ) {
	if ( ! empty( $features ) ) {
		return $features;
	}

	return array(
		array(
			'icon'  => 'dashicons-welcome-learn-more',
			'title' => __( 'Qualified Faculty', 'school-master' ),
			'text'  => __( 'Experienced, industry-certified instructors dedicated to student success.', 'school-master' ),
		),
		array(
			'icon'  => 'dashicons-building',
			'title' => __( 'Modern Facilities', 'school-master' ),
			'text'  => __( 'Well-equipped labs, workshops and classrooms for hands-on learning.', 'school-master' ),
		),
		array(
			'icon'  => 'dashicons-hammer',
			'title' => __( 'Practical Training', 'school-master' ),
			'text'  => __( 'A skills-first curriculum that prepares graduates for real careers.', 'school-master' ),
		),
		array(
			'icon'  => 'dashicons-awards',
			'title' => __( 'Proven Results', 'school-master' ),
			'text'  => __( 'A strong track record of graduate placement and achievement.', 'school-master' ),
		),
	);
}
add_filter( 'school_master_whyus_features', 'school_master_default_whyus_features' );

/**
 * Print the post meta line (date + author) for blog posts.
 *
 * @return void
 */
function school_master_posted_on() {
	printf(
		'<span class="posted-on"><time class="entry-date published" datetime="%1$s">%2$s</time></span>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
}

/**
 * Query the latest notices, important ones first then newest.
 *
 * Shared by the homepage Notice Board, the ticker and the popup so they all
 * order notices the same way. Returns null if the Notices post type (from the
 * companion plugin) is unavailable, so callers can bail cleanly.
 *
 * @param int $count Maximum notices to fetch.
 * @return WP_Query|null
 */
function school_master_notices_query( $count ) {
	if ( ! function_exists( 'smcore_has_post_type' ) || ! smcore_has_post_type( 'sm_notice' ) ) {
		return null;
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'sm_notice',
			'posts_per_page'      => max( 1, (int) $count ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);

	// The plugin stores _sm_is_important only when checked (the row is deleted
	// when unchecked), so ordering by that meta in SQL would drop normal
	// notices via the postmeta join. Instead we fetch newest-first and stably
	// float important ones to the top in PHP — the counts here are small.
	if ( ! empty( $query->posts ) ) {
		$important = array();
		$regular   = array();
		foreach ( $query->posts as $notice_post ) {
			if ( get_post_meta( $notice_post->ID, '_sm_is_important', true ) ) {
				$important[] = $notice_post;
			} else {
				$regular[] = $notice_post;
			}
		}
		$query->posts      = array_merge( $important, $regular );
		$query->post_count = count( $query->posts );
	}

	return $query;
}

/**
 * Render the scrolling notice ticker shown near the top of every page.
 *
 * Pure-CSS marquee: the item list is printed twice so the animation can loop
 * seamlessly (the duplicate is hidden from assistive tech). Renders nothing
 * when disabled or when there are no notices to show.
 *
 * @return void
 */
function school_master_notice_ticker() {
	if ( ! school_master_option( 'ticker_enable', true ) ) {
		return;
	}

	$query = school_master_notices_query( (int) school_master_option( 'ticker_count', 6 ) );

	if ( ! $query || ! $query->have_posts() ) {
		return;
	}

	// Build the list of items once, then output it twice for a seamless loop.
	$items = '';
	while ( $query->have_posts() ) {
		$query->the_post();
		$important = function_exists( 'smcore_get_meta' ) ? smcore_get_meta( 'is_important' ) : false;
		$items    .= sprintf(
			'<li class="notice-bar__item%1$s"><a href="%2$s">%3$s%4$s</a></li>',
			$important ? ' notice-bar__item--important' : '',
			esc_url( get_permalink() ),
			$important ? '<span class="notice-bar__flag">' . esc_html__( 'New', 'school-master' ) . '</span>' : '',
			esc_html( get_the_title() )
		);
	}
	wp_reset_postdata();

	$speeds   = array(
		'slow'   => '48s',
		'normal' => '32s',
		'fast'   => '18s',
	);
	$speed    = school_master_option( 'ticker_speed', 'normal' );
	$duration = isset( $speeds[ $speed ] ) ? $speeds[ $speed ] : $speeds['normal'];
	$label    = school_master_option( 'ticker_label', __( 'Notices', 'school-master' ) );

	$megaphone = '<svg class="notice-bar__icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M3 10v4a1 1 0 001 1h2l3.5 4V5L6 9H4a1 1 0 00-1 1zm12.5 2a4.5 4.5 0 00-2.5-4.03v8.06A4.5 4.5 0 0015.5 12zM13 3.23v2.06a6.5 6.5 0 010 13.42v2.06A8.5 8.5 0 0013 3.23z"/></svg>';
	?>
	<div class="notice-bar" style="--sm-ticker-duration: <?php echo esc_attr( $duration ); ?>;">
		<div class="container notice-bar__inner">
			<?php if ( $label ) : ?>
				<span class="notice-bar__label"><?php echo $megaphone; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static, safe SVG. ?><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
			<div class="notice-bar__viewport">
				<div class="notice-bar__marquee">
					<ul class="notice-bar__track">
						<?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped parts above. ?>
					</ul>
					<ul class="notice-bar__track" aria-hidden="true">
						<?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped parts above. ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Whether at least one top-bar action button is configured.
 *
 * @return bool
 */
function school_master_has_topbar_buttons() {
	for ( $b = 1; $b <= 2; $b++ ) {
		if ( '' !== (string) school_master_option( "topbar_btn{$b}_text" ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Render the top-bar action buttons (e.g. Apply Now / Login).
 *
 * A button appears only when it has label text. URL falls back to `#` so a
 * label-only button still renders rather than pointing nowhere unexpectedly.
 *
 * @return void
 */
function school_master_topbar_buttons() {
	if ( ! school_master_has_topbar_buttons() ) {
		return;
	}

	echo '<div class="top-bar__actions">';

	for ( $b = 1; $b <= 2; $b++ ) {
		$text = school_master_option( "topbar_btn{$b}_text" );

		if ( '' === (string) $text ) {
			continue;
		}

		$url   = school_master_option( "topbar_btn{$b}_url" );
		$style = school_master_option( "topbar_btn{$b}_style", 1 === $b ? 'solid' : 'outline' );

		printf(
			'<a class="topbar-btn topbar-btn--%1$s" href="%2$s">%3$s</a>',
			'outline' === $style ? 'outline' : 'solid',
			esc_url( $url ? $url : '#' ),
			esc_html( $text )
		);
	}

	echo '</div>';
}

/**
 * Render the first-visit notice popup markup (hidden until JS reveals it).
 *
 * Content is either a custom message from the Customizer (one card) or every
 * important notice that has not passed its "Popup until" date (one card each,
 * newest first). The JS shows the cards one at a time, remembering which have
 * been dismissed for the browser session so a notice never re-pops after it is
 * closed — including when a visitor follows its "Read more" link. A short
 * content signature is emitted as `data-popup-id` so a card reappears when its
 * message changes even if a visitor dismissed the previous version.
 *
 * @return void
 */
function school_master_notice_popup() {
	if ( ! school_master_option( 'popup_enable', false ) ) {
		return;
	}

	$source   = school_master_option( 'popup_source', 'important' );
	$btn_text = school_master_option( 'popup_btn_text', __( 'Read more', 'school-master' ) );
	$cards    = array();

	if ( 'custom' === $source ) {
		$text = trim( (string) school_master_option( 'popup_text' ) );

		if ( '' === $text ) {
			return; // Nothing to show.
		}

		$cards[] = array(
			'title'      => school_master_option( 'popup_title', __( 'Important Notice', 'school-master' ) ),
			'text'       => $text,
			'btn_url'    => school_master_option( 'popup_btn_url' ),
			'attachment' => '',
		);
	} else {
		$query = school_master_notices_query( 20 );

		if ( ! $query || ! $query->have_posts() ) {
			return;
		}

		$today = current_time( 'Y-m-d' );

		// One card per important notice that has not passed its "Popup until"
		// date. The shared query already floats important notices to the top.
		foreach ( $query->posts as $post_obj ) {
			if ( ! get_post_meta( $post_obj->ID, '_sm_is_important', true ) ) {
				continue;
			}

			$expiry = (string) get_post_meta( $post_obj->ID, '_sm_popup_expiry', true );

			// A blank date keeps the notice popping up indefinitely.
			if ( '' !== $expiry && $expiry < $today ) {
				continue;
			}

			$cards[] = array(
				'title'      => get_the_title( $post_obj->ID ),
				'text'       => wp_strip_all_tags( get_the_excerpt( $post_obj->ID ) ),
				'btn_url'    => get_permalink( $post_obj->ID ),
				'attachment' => (string) get_post_meta( $post_obj->ID, '_sm_attachment', true ),
			);
		}

		if ( empty( $cards ) ) {
			return;
		}
	}

	echo '<div class="sm-popup-stack" data-popup-stack>';

	foreach ( $cards as $card ) {
		school_master_render_popup_card( $card['title'], $card['text'], $card['btn_url'], $btn_text, $card['attachment'] );
	}

	echo '</div>';
}

/**
 * Render a single notice popup card.
 *
 * Each card carries a content signature as its `data-popup-id`, and a stable
 * per-card title id so multiple cards remain valid, accessible dialogs. The
 * "Read more" link is tagged `data-popup-read` so the JS can record it as
 * dismissed before the browser navigates away — otherwise the same notice
 * would pop again on the page it links to.
 *
 * @param string $title      Card heading.
 * @param string $text       Card body (plain text; paragraph-wrapped on output).
 * @param string $btn_url    Optional button URL.
 * @param string $btn_text   Optional button label.
 * @param string $attachment Optional attachment URL (image preview or file link).
 * @return void
 */
function school_master_render_popup_card( $title, $text, $btn_url, $btn_text, $attachment = '' ) {
	$signature = substr( md5( $title . '|' . $text . '|' . $btn_url . '|' . $attachment ), 0, 12 );
	$title_id  = 'sm-popup-title-' . $signature;
	?>
	<div class="sm-popup" data-popup-id="<?php echo esc_attr( $signature ); ?>" hidden>
		<div class="sm-popup__overlay" data-popup-close></div>
		<div class="sm-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
			<button type="button" class="sm-popup__close" data-popup-close aria-label="<?php esc_attr_e( 'Close', 'school-master' ); ?>">&times;</button>
			<?php if ( $title ) : ?>
				<h2 class="sm-popup__title" id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<div class="sm-popup__body"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
			<?php endif; ?>
			<?php school_master_popup_attachment( $attachment, $title ); ?>
			<?php if ( $btn_url && $btn_text ) : ?>
				<a class="btn btn--primary sm-popup__btn" href="<?php echo esc_url( $btn_url ); ?>" data-popup-read><?php echo esc_html( $btn_text ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Render a notice's attachment inside the popup.
 *
 * Images (jpg/png/gif/webp/svg) show an inline preview that opens full size in
 * a new tab; any other file (PDF, Doc, …) shows a labelled button. The file
 * extension is read from the URL, so it works with any media-library upload.
 *
 * @param string $url   Attachment URL.
 * @param string $title Notice title, used for the image alt text.
 * @return void
 */
function school_master_popup_attachment( $url, $title = '' ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return;
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	$ext  = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

	echo '<div class="sm-popup__attachment">';

	if ( in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' ), true ) ) {
		$alt = '' !== $title
			? sprintf( /* translators: %s: notice title. */ __( 'Attachment for %s', 'school-master' ), $title )
			: __( 'Notice attachment', 'school-master' );

		printf(
			'<a class="sm-popup__attachment-image" href="%1$s" target="_blank" rel="noopener"><img src="%1$s" alt="%2$s" loading="lazy" /></a>',
			esc_url( $url ),
			esc_attr( $alt )
		);
	} else {
		$label = '' !== $ext
			/* translators: %s: file type, e.g. PDF. */
			? sprintf( __( 'View attachment (%s)', 'school-master' ), strtoupper( $ext ) )
			: __( 'View attachment', 'school-master' );

		printf(
			'<a class="btn btn--secondary sm-popup__attachment-link" href="%1$s" target="_blank" rel="noopener">%2$s</a>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	echo '</div>';
}
