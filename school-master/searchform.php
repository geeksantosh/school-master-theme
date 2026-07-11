<?php
/**
 * Custom search form.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$unique_id = wp_unique_id( 'search-field-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $unique_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'school-master' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $unique_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search &hellip;', 'school-master' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="search-submit btn btn--primary"><?php esc_html_e( 'Search', 'school-master' ); ?></button>
</form>
