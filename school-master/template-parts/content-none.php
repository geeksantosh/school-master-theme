<?php
/**
 * Template shown when no posts are found.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="no-results not-found card">
	<div class="card__body">
		<h2 class="entry-title"><?php esc_html_e( 'Nothing found', 'school-master' ); ?></h2>
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, nothing matched your search. Please try again with different keywords.', 'school-master' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'It seems there is nothing here yet.', 'school-master' ); ?></p>
		<?php endif; ?>
	</div>
</section>
