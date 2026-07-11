<?php
/**
 * 404 template.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main container">
	<div class="content-area content-area--full error-404">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Page not found', 'school-master' ); ?></h1>
		</header>
		<div class="page-content">
			<p><?php esc_html_e( 'The page you were looking for could not be found. It might have been moved or deleted.', 'school-master' ); ?></p>
			<?php get_search_form(); ?>
			<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'school-master' ); ?></a></p>
		</div>
	</div>
</main>
<?php
get_footer();
