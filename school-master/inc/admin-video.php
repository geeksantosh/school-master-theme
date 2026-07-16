<?php
/**
 * Campus Video admin page.
 *
 * The same settings exist in the Customizer, but that is gated on
 * `edit_theme_options` — Administrators only. Content teams are usually
 * Editors, so this gives them a plain form for the one field they actually
 * need to change. It reads and writes the same theme mods, so the two stay
 * in sync and neither is a second source of truth.
 *
 * Top-level rather than under Appearance: Editors cannot see that menu.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Capability required to edit the campus video.
 *
 * Defaults to `edit_pages`, which Editors have and Authors do not. Filterable
 * so a site can hand it to a different role without touching core theme code.
 *
 * @return string
 */
function school_master_video_capability() {
	return apply_filters( 'school_master_video_capability', 'edit_pages' );
}

/**
 * Register the Campus Video menu.
 *
 * @return void
 */
function school_master_video_menu() {
	add_menu_page(
		__( 'Campus Video', 'school-master' ),
		__( 'Campus Video', 'school-master' ),
		school_master_video_capability(),
		'school-master-video',
		'school_master_video_page',
		'dashicons-video-alt3',
		61
	);
}
add_action( 'admin_menu', 'school_master_video_menu' );

/**
 * Render the Campus Video page.
 *
 * @return void
 */
function school_master_video_page() {
	if ( ! current_user_can( school_master_video_capability() ) ) {
		wp_die( esc_html__( 'You are not allowed to edit the campus video.', 'school-master' ), '', array( 'response' => 403 ) );
	}

	$enabled = (bool) school_master_option( 'section_video_enable', true );
	$title   = school_master_option( 'video_title', __( 'Campus Life', 'school-master' ) );
	$url     = school_master_option( 'video_url' );
	$text    = school_master_option( 'video_text' );
	$embed   = school_master_youtube_embed_url( $url );
	$notice  = isset( $_GET['sm_video'] ) ? sanitize_key( wp_unslash( $_GET['sm_video'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Campus Video', 'school-master' ); ?></h1>

		<?php if ( 'saved' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved. Your homepage is updated.', 'school-master' ); ?></p></div>
		<?php endif; ?>

		<p style="max-width:640px">
			<?php esc_html_e( 'This video appears on the homepage, below "Why Choose Us". Paste a YouTube link and press Save — the preview below shows exactly what visitors will see.', 'school-master' ); ?>
		</p>

		<?php if ( $url && ! $embed ) : ?>
			<div class="notice notice-warning">
				<p><?php esc_html_e( 'The saved link is not a YouTube address, so the video is hidden on the homepage. Paste a link that starts with youtube.com or youtu.be.', 'school-master' ); ?></p>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="school_master_video" />
			<?php wp_nonce_field( 'school_master_video' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show on homepage', 'school-master' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="video_enable" value="1" <?php checked( $enabled ); ?> />
							<?php esc_html_e( 'Display the campus video section', 'school-master' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Untick to hide the section without losing the link below.', 'school-master' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sm-video-url"><?php esc_html_e( 'YouTube link', 'school-master' ); ?></label></th>
					<td>
						<input type="url" id="sm-video-url" name="video_url" class="regular-text" value="<?php echo esc_attr( $url ); ?>" placeholder="https://www.youtube.com/watch?v=..." />
						<p class="description"><?php esc_html_e( 'Copy the address from your browser while watching the video on YouTube, or use the Share button. Leave empty to hide the section.', 'school-master' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sm-video-title"><?php esc_html_e( 'Heading', 'school-master' ); ?></label></th>
					<td>
						<input type="text" id="sm-video-title" name="video_title" class="regular-text" value="<?php echo esc_attr( $title ); ?>" />
						<p class="description"><?php esc_html_e( 'Shown above the video.', 'school-master' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sm-video-text"><?php esc_html_e( 'Intro text', 'school-master' ); ?></label></th>
					<td>
						<textarea id="sm-video-text" name="video_text" class="large-text" rows="3"><?php echo esc_textarea( $text ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Optional sentence between the heading and the video.', 'school-master' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save Video', 'school-master' ) ); ?>
		</form>

		<?php if ( $embed ) : ?>
			<h2><?php esc_html_e( 'Preview', 'school-master' ); ?></h2>
			<?php if ( ! $enabled ) : ?>
				<p class="description"><?php esc_html_e( 'The section is currently hidden on the homepage.', 'school-master' ); ?></p>
			<?php endif; ?>
			<div style="max-width:560px">
				<div style="position:relative;padding-top:56.25%">
					<iframe src="<?php echo esc_url( $embed ); ?>" title="<?php esc_attr_e( 'Campus video preview', 'school-master' ); ?>" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen loading="lazy"></iframe>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Save the Campus Video form.
 *
 * Sanitization mirrors the matching Customizer settings.
 *
 * @return void
 */
function school_master_video_save() {
	if ( ! current_user_can( school_master_video_capability() ) ) {
		wp_die( esc_html__( 'You are not allowed to edit the campus video.', 'school-master' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'school_master_video' );

	set_theme_mod( 'school_master_section_video_enable', isset( $_POST['video_enable'] ) );
	set_theme_mod( 'school_master_video_url', isset( $_POST['video_url'] ) ? esc_url_raw( wp_unslash( $_POST['video_url'] ) ) : '' );
	set_theme_mod( 'school_master_video_title', isset( $_POST['video_title'] ) ? sanitize_text_field( wp_unslash( $_POST['video_title'] ) ) : '' );
	set_theme_mod( 'school_master_video_text', isset( $_POST['video_text'] ) ? sanitize_text_field( wp_unslash( $_POST['video_text'] ) ) : '' );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'     => 'school-master-video',
				'sm_video' => 'saved',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_school_master_video', 'school_master_video_save' );
