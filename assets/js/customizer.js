/* ---------------------------------------------------------------------
 -Customizer Controls-
    Detects LFC module sections inside the customizer and adds our custom
    classes and styles

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
 var lfcCustomizerControls = ( function( $ ) {

 	var pub = {};
 	var parentDoc = window.parent.jQuery( window.parent.document );

 	pub.init = function() {

		// Catch event that user has clicked on a Section editor in the customizer; scroll to that section in the body
		parentDoc.on( 'scroll_to_section', function( e, section ) {
			var id = "#" + section;

			if( $( id ).length ) {
				var y = document.getElementById( section ).offsetTop;
				scrollTo( y, 500, easing.easeInOutCubic );
			}

		});

		// Scroll to bottom of page when adding a new section
		parentDoc.on( 'scroll_to_bottom', function() {
			scrollTo( document.body.scrollHeight, 500, easing.easeInOutCubic );
		});

		// Wrapped in a 0-second timeout, because race conditions. Sucky but what can you do. 
		setTimeout( function() {
			var sidebars = wp.customize.WidgetCustomizerPreview.renderedSidebars;
			var is_in = 0;
			var current_page_id = 0;
			for( var key in sidebars ) {
				if( key.indexOf( 'module' ) != -1 ) {

					// Found an LFC sidebar
					is_in = 1;

					// Set current page ID based on this sidebar
					current_page_id = key.replace( 'modules-', '' );
				}
			}

			// We have some LFC sidebars, let's add classes to them (all through parent.document because we're currently in the iframe)
			if( is_in == 1 ) {

				// Add classes to the panels, hide any widget areas that don't belong on this page
				$( '#accordion-panel-widgets', window.parent.document ).addClass( 'lfc-panel' );
				$( '#accordion-panel-widgets > h3', window.parent.document ).text( 'Long Format Page Sections' ).addClass( 'lfc-section' );
				$( '#accordion-panel-widgets .panel-title', window.parent.document ).text( 'Long Format Page Sections' );
				$( '#accordion-panel-widgets .control-section', window.parent.document ).each( function() {
					if( typeof $( this ).attr( 'id' ) != 'undefined' ) {
						if( $( this ).attr( 'id' ).indexOf( 'module' ) == -1 ) {
							$( this ).addClass( 'hidden-by-modules' );
						}
					}
				});

				// Hide any widgets that aren't one of our Modules so the user can't add them here
				$( '#available-widgets .widget-tpl', window.parent.document ).each( function() {
					if( typeof $( this ).attr( 'id' ) != 'undefined' ) {
						if( $( this ).attr( 'id' ).indexOf( 'module' ) == -1 ) {
							$( this ).addClass( 'hidden-by-modules' );
						}
						if( $( this ).attr( 'id' ).indexOf( 'module' ) != -1 ) {
							$( this ).removeClass( 'hidden-by-modules' );
						}
					}
				});

				// Replace the text on widget buttons to make it Sections instead
				$( '.add-new-widget', window.parent.document ).each( function() {
					$( this ).text( 'Add a Section' );
				});
			}else{

				// No LFC sidebars found, revert names, content, and classes
				$( '#accordion-panel-widgets', window.parent.document ).removeClass( 'lfc-panel' );
				$( '#accordion-panel-widgets > h3', window.parent.document ).text( 'Widgets' ).removeClass( 'lfc-section' );
				$( '#accordion-panel-widgets .panel-title', window.parent.document ).text( 'Widgets' );
				$( '#accordion-panel-widgets .control-section', window.parent.document ).each( function() {
					if( typeof $( this ).attr( 'id' ) != 'undefined' ) {
						if( $( this ).attr( 'id' ).indexOf( 'module' ) == -1 ) {
							$( this ).removeClass( 'hidden-by-modules' );
						}
					}
				});

				// Show all widgets
				$( '#available-widgets .widget-tpl', window.parent.document ).each( function() {
					if( typeof $( this ).attr( 'id' ) != 'undefined' ) {
						if( $( this ).attr( 'id' ).indexOf( 'module' ) == -1 ) {
							$( this ).removeClass( 'hidden-by-modules' );
						}
						if( $( this ).attr( 'id' ).indexOf( 'module' ) != -1 ) {
							$( this ).addClass( 'hidden-by-modules' );
						}
					}
				});

				// Change text back to Widgets
				$( '.add-new-widget', window.parent.document ).each( function() {
					$( this ).text( 'Add a Widget' );
				});
			}

			// Adding a class so we can style our panels
			$( 'li[id*=page_options] h3', window.parent.document ).each( function() {
				$( this ).addClass( 'lfc-section' );
			});

			// If we're on a page with LFC content, show the Page Options settings section for that page. Otherwise, hide them all
			if( current_page_id != 0 ) {

				$options_sections = $( 'li[id*=page_options]', window.parent.document );

				setTimeout( function(){
					for( var i=0; i<$options_sections.length; i++ ) {
						$section = $( $options_sections[i] );
						var id = $section.attr( 'id' );
						if( id.indexOf( 'options_' + current_page_id ) == -1 ) {
							$section.hide();
						}else{
							$section.show();
						}
					}
				}, 300);
				
			}else{
				$( 'li[id*=page_options]', window.parent.document ).each( function() {
					$( this ).hide();
				});
			}
		}, 0);
};


function getOffset( el ) {
	var _x = 0;
	var _y = 0;
	while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
		_x += el.offsetLeft - el.scrollLeft;
		_y += el.offsetTop - el.scrollTop;
		el = el.offsetParent;
	}
	return { top: _y, left: _x };
}

function scrollTo( Y, duration, easingFunction, callback ) {

	var start = Date.now(),
	elem = document.documentElement.scrollTop?document.documentElement:document.body,
	from = elem.scrollTop;

	if( from === Y ) {
		if( callback ) callback();
		return; /* Prevent scrolling to the Y point if already there */
	}

	function min( a, b ) {
		return a<b?a:b;
	}

	function scroll( timestamp ) {

		var currentTime = Date.now(),
		time = min( 1, ( ( currentTime - start ) / duration ) ),
		easedT = easingFunction(time);

		elem.scrollTop = ( easedT * ( Y - from ) ) + from;

		if( time < 1 ) requestAnimationFrame( scroll );
		else
			if( callback ) callback();
	}

	requestAnimationFrame(scroll);
}

/* bits and bytes of the scrollTo function inspired by the works of Benjamin DeCock */

	/*
	 * Easing Functions - inspired from http://gizma.com/easing/
	 * only considering the t value for the range [0, 1] => [0, 1]
	 */
	 var easing = {
		// no easing, no acceleration
		linear: function ( t ) { return t },
		// accelerating from zero velocity
		easeInQuad: function ( t ) { return t*t },
		// decelerating to zero velocity
		easeOutQuad: function ( t ) { return t*(2-t) },
		// acceleration until halfway, then deceleration
		easeInOutQuad: function ( t ) { return t<.5 ? 2*t*t : -1+(4-2*t)*t },
		// accelerating from zero velocity 
		easeInCubic: function ( t ) { return t*t*t },
		// decelerating to zero velocity 
		easeOutCubic: function ( t ) { return (--t)*t*t+1 },
		// acceleration until halfway, then deceleration 
		easeInOutCubic: function ( t ) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1 },
		// accelerating from zero velocity 
		easeInQuart: function ( t ) { return t*t*t*t },
		// decelerating to zero velocity 
		easeOutQuart: function ( t ) { return 1-(--t)*t*t*t },
		// acceleration until halfway, then deceleration
		easeInOutQuart: function ( t ) { return t<.5 ? 8*t*t*t*t : 1-8*(--t)*t*t*t },
		// accelerating from zero velocity
		easeInQuint: function ( t ) { return t*t*t*t*t },
		// decelerating to zero velocity
		easeOutQuint: function ( t ) { return 1+(--t)*t*t*t*t },
		// acceleration until halfway, then deceleration 
		easeInOutQuint: function ( t ) { return t<.5 ? 16*t*t*t*t*t : 1+16*(--t)*t*t*t*t }
	}

	return pub;

}(jQuery));

(function() {
	var lastTime = 0;
	var vendors = ['ms', 'moz', 'webkit', 'o'];
	for( var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x ) {
		window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
		window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
		|| window[vendors[x]+'CancelRequestAnimationFrame'];
	}

	if ( !window.requestAnimationFrame )
		window.requestAnimationFrame = function( callback, element ) {
			var currTime = new Date().getTime();
			var timeToCall = Math.max( 0, 16 - ( currTime - lastTime ) );
			var id = window.setTimeout( function() { callback( currTime + timeToCall ); },
				timeToCall );
			lastTime = currTime + timeToCall;
			return id;
		};

	if ( !window.cancelAnimationFrame )
		window.cancelAnimationFrame = function( id ) {
			clearTimeout( id );
		};
}());

/* ---------------------------------------------------------------------
-Controller-
Initialize your scripts in the CONTROL function 

Author:
Boilerplate
------------------------------------------------------------------------ */
var CONTROL = (function ( $ ) {

	$( window ).ready( function() {
		lfcCustomizerControls.init();
	});

}(jQuery));