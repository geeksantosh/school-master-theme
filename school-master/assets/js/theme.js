/**
 * School Master — front-end interactions.
 *
 * Vanilla JS, no jQuery dependency. Handles the mobile nav toggle,
 * submenu dropdowns on touch/small screens, and the animated stat
 * counters. Kept intentionally small and dependency-free.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		setupMobileNav();
		setupDropdowns();
		setupCounters();
		setupNoticePopup();
	} );

	/**
	 * Toggle the primary menu on small screens.
	 */
	function setupMobileNav() {
		var nav = document.querySelector( '.main-navigation' );
		var toggle = document.querySelector( '.menu-toggle' );

		if ( ! nav || ! toggle ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var open = nav.classList.toggle( 'toggled' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
	}

	/**
	 * Expand/collapse sub-menus. On desktop CSS handles hover; the
	 * injected toggle buttons only appear (via CSS) on small screens.
	 */
	function setupDropdowns() {
		var toggles = document.querySelectorAll( '.dropdown-toggle' );

		Array.prototype.forEach.call( toggles, function ( btn ) {
			btn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var submenu = btn.parentNode.querySelector( '.sub-menu' );
				if ( ! submenu ) {
					return;
				}
				var open = submenu.classList.toggle( 'is-open' );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );
		} );
	}

	/**
	 * Animate any element with a data-count attribute from 0 up to its
	 * target once it scrolls into view.
	 */
	function setupCounters() {
		var counters = document.querySelectorAll( '[data-count]' );

		if ( ! counters.length ) {
			return;
		}

		var run = function ( el ) {
			var target = parseInt( el.getAttribute( 'data-count' ), 10 ) || 0;
			var suffix = el.getAttribute( 'data-suffix' ) || '';
			var duration = 1600;
			var start = null;

			var step = function ( timestamp ) {
				if ( ! start ) {
					start = timestamp;
				}
				var progress = Math.min( ( timestamp - start ) / duration, 1 );
				el.textContent = Math.floor( progress * target ).toLocaleString() + suffix;
				if ( progress < 1 ) {
					window.requestAnimationFrame( step );
				}
			};

			window.requestAnimationFrame( step );
		};

		if ( 'IntersectionObserver' in window ) {
			var observer = new IntersectionObserver( function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						run( entry.target );
						obs.unobserve( entry.target );
					}
				} );
			}, { threshold: 0.4 } );

			Array.prototype.forEach.call( counters, function ( el ) {
				observer.observe( el );
			} );
		} else {
			Array.prototype.forEach.call( counters, run );
		}
	}

	/**
	 * Reveal the first-visit notice popup once per browser session. The popup
	 * is keyed by a content signature (data-popup-id), so an updated message
	 * shows again even to a visitor who dismissed the previous one.
	 */
	function setupNoticePopup() {
		var popup = document.querySelector( '.sm-popup' );

		if ( ! popup ) {
			return;
		}

		var id = popup.getAttribute( 'data-popup-id' ) || '';
		var storeKey = 'smNoticePopupDismissed';
		var lastFocus = null;

		var dismissed = '';
		try {
			dismissed = window.sessionStorage.getItem( storeKey ) || '';
		} catch ( e ) {
			dismissed = '';
		}

		if ( dismissed === id && id ) {
			return;
		}

		var close = function () {
			popup.classList.remove( 'is-open' );
			popup.setAttribute( 'hidden', 'hidden' );
			document.removeEventListener( 'keydown', onKey );
			try {
				window.sessionStorage.setItem( storeKey, id );
			} catch ( e ) {}
			if ( lastFocus && lastFocus.focus ) {
				lastFocus.focus();
			}
		};

		var onKey = function ( e ) {
			if ( 'Escape' === e.key || 'Esc' === e.key ) {
				close();
			}
		};

		var open = function () {
			lastFocus = document.activeElement;
			popup.removeAttribute( 'hidden' );
			// Force a reflow so the CSS transition runs from the hidden state.
			void popup.offsetWidth;
			popup.classList.add( 'is-open' );
			document.addEventListener( 'keydown', onKey );

			var closeBtn = popup.querySelector( '.sm-popup__close' );
			if ( closeBtn ) {
				closeBtn.focus();
			}
		};

		Array.prototype.forEach.call(
			popup.querySelectorAll( '[data-popup-close]' ),
			function ( el ) {
				el.addEventListener( 'click', close );
			}
		);

		window.setTimeout( open, 700 );
	}
}() );
