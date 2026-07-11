/**
 * Admin media uploader for file fields.
 *
 * @package SchoolMasterCore
 */
( function ( $ ) {
	'use strict';

	$( document ).on( 'click', '.smcore-upload', function ( e ) {
		e.preventDefault();

		var $button = $( this );
		var $input = $button.prevAll( '.smcore-file-url' ).first();

		var frame = wp.media( {
			title: 'Select or Upload File',
			button: { text: 'Use this file' },
			multiple: false,
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			$input.val( attachment.url );
		} );

		frame.open();
	} );
} )( jQuery );
