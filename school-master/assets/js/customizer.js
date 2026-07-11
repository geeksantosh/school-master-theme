/**
 * School Master — Customizer live preview.
 *
 * Only the settings registered with transport 'postMessage' in
 * inc/customizer.php are wired here (site title and description).
 * Everything else uses the default 'refresh' transport.
 */
( function ( $ ) {
	'use strict';

	// Site title.
	wp.customize( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );

	// Site description / tagline.
	wp.customize( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
}( jQuery ) );
