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
		setupMarquees();
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
	 * Reveal the first-visit notice popups once per browser session.
	 *
	 * The markup may contain several cards (one per important notice). Cards
	 * are shown one at a time; closing one reveals the next, and once all are
	 * closed nothing more appears. Each card is keyed by a content signature
	 * (data-popup-id) that is recorded in sessionStorage on dismissal, so a
	 * dismissed notice never re-pops for the session — including when the
	 * visitor follows its "Read more" link (data-popup-read). An updated card
	 * gets a new signature, so it still shows to a visitor who dismissed the
	 * previous version.
	 */
	function setupNoticePopup() {
		var stack = document.querySelector( '[data-popup-stack]' );

		if ( ! stack ) {
			return;
		}

		var popups = Array.prototype.slice.call( stack.querySelectorAll( '.sm-popup' ) );

		if ( ! popups.length ) {
			return;
		}

		var storeKey = 'smNoticePopupDismissed';
		var lastFocus = null;
		var current = null;

		var readDismissed = function () {
			try {
				var raw = window.sessionStorage.getItem( storeKey );
				var list = raw ? JSON.parse( raw ) : [];
				return Array.isArray( list ) ? list : [];
			} catch ( e ) {
				// Older sessions stored a bare string; treat as nothing dismissed.
				return [];
			}
		};

		var addDismissed = function ( id ) {
			if ( ! id ) {
				return;
			}
			var list = readDismissed();
			if ( list.indexOf( id ) === -1 ) {
				list.push( id );
			}
			try {
				window.sessionStorage.setItem( storeKey, JSON.stringify( list ) );
			} catch ( e ) {}
		};

		var dismissed = readDismissed();
		var queue = popups.filter( function ( el ) {
			var id = el.getAttribute( 'data-popup-id' ) || '';
			return id && dismissed.indexOf( id ) === -1;
		} );

		if ( ! queue.length ) {
			return;
		}

		var onKey = function ( e ) {
			if ( 'Escape' === e.key || 'Esc' === e.key ) {
				dismissCurrent();
			}
		};

		var hide = function ( popup ) {
			popup.classList.remove( 'is-open' );
			popup.setAttribute( 'hidden', 'hidden' );
		};

		var showNext = function () {
			current = queue.shift();

			if ( ! current ) {
				document.removeEventListener( 'keydown', onKey );
				if ( lastFocus && lastFocus.focus ) {
					lastFocus.focus();
				}
				return;
			}

			current.removeAttribute( 'hidden' );
			// Force a reflow so the CSS transition runs from the hidden state.
			void current.offsetWidth;
			current.classList.add( 'is-open' );

			var closeBtn = current.querySelector( '.sm-popup__close' );
			if ( closeBtn ) {
				closeBtn.focus();
			}
		};

		var dismissCurrent = function () {
			if ( ! current ) {
				return;
			}
			addDismissed( current.getAttribute( 'data-popup-id' ) || '' );
			hide( current );
			showNext();
		};

		// Wire every card up front. Closing (overlay, X, Esc) advances the
		// queue; "Read more" only records the dismissal, then navigates.
		popups.forEach( function ( popup ) {
			Array.prototype.forEach.call(
				popup.querySelectorAll( '[data-popup-close]' ),
				function ( el ) {
					el.addEventListener( 'click', dismissCurrent );
				}
			);

			var readLink = popup.querySelector( '[data-popup-read]' );
			if ( readLink ) {
				readLink.addEventListener( 'click', function () {
					addDismissed( popup.getAttribute( 'data-popup-id' ) || '' );
				} );
			}
		} );

		lastFocus = document.activeElement;
		document.addEventListener( 'keydown', onKey );
		window.setTimeout( showNext, 700 );
	}

	/**
	 * Auto-scroll a card row (testimonials, partners, …) only when the cards
	 * overflow the viewport. When they overflow, the original cards are cloned
	 * once so the CSS marquee (translateX 0 -> -50%) loops seamlessly; when
	 * they fit, the row is left centered and static. Re-evaluated on resize.
	 * Rows marked data-marquee-viewport="manual" (a per-section Customizer
	 * toggle) and visitors who prefer reduced motion keep a plain,
	 * manually-scrollable row (no clones).
	 */
	function setupMarquees() {
		var viewports = document.querySelectorAll( '[data-marquee-viewport]' );

		if ( ! viewports.length ) {
			return;
		}

		var prefersReduced = window.matchMedia &&
			window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		if ( prefersReduced ) {
			return;
		}

		Array.prototype.forEach.call( viewports, function ( viewport ) {
			if ( 'manual' === viewport.getAttribute( 'data-marquee-viewport' ) ) {
				return;
			}

			var track = viewport.querySelector( '[data-marquee-track]' );

			if ( ! track ) {
				return;
			}

			var originals = Array.prototype.slice.call( track.children );
			var cloned = false;

			var addClones = function () {
				originals.forEach( function ( el ) {
					var clone = el.cloneNode( true );
					clone.setAttribute( 'aria-hidden', 'true' );
					clone.setAttribute( 'tabindex', '-1' );
					track.appendChild( clone );
				} );
				cloned = true;
			};

			var removeClones = function () {
				while ( track.children.length > originals.length ) {
					track.removeChild( track.lastChild );
				}
				cloned = false;
			};

			var evaluate = function () {
				// Measure the true content width from the originals only.
				var setWidth = originals.reduce( function ( sum, el ) {
					return sum + el.getBoundingClientRect().width;
				}, 0 );

				var overflows = setWidth > viewport.clientWidth + 1;

				if ( overflows ) {
					if ( ! cloned ) {
						addClones();
					}
					// Keep a roughly constant speed (~70px/sec) regardless of count.
					var duration = Math.max( 20, Math.round( setWidth / 70 ) );
					track.style.setProperty( '--sm-marquee-duration', duration + 's' );
					viewport.classList.add( 'is-scrolling' );
				} else {
					viewport.classList.remove( 'is-scrolling' );
					if ( cloned ) {
						removeClones();
					}
				}
			};

			evaluate();

			var resizeTimer;
			window.addEventListener( 'resize', function () {
				window.clearTimeout( resizeTimer );
				// Measure the originals cleanly by dropping clones first.
				if ( cloned ) {
					viewport.classList.remove( 'is-scrolling' );
					removeClones();
				}
				resizeTimer = window.setTimeout( evaluate, 200 );
			} );
		} );
	}
}() );
