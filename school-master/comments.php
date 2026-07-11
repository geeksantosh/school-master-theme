<?php
/**
 * The comments template.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

// Bail if the post is password protected and the visitor hasn't entered it.
if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( '1' === $comment_count ) {
				esc_html_e( 'One comment', 'school-master' );
			} else {
				printf(
					/* translators: %s: comment count number. */
					esc_html( _n( '%s comment', '%s comments', $comment_count, 'school-master' ) ),
					esc_html( number_format_i18n( $comment_count ) )
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'school-master' ); ?></p>
			<?php
		endif;

	endif;

	comment_form();
	?>

</div>
