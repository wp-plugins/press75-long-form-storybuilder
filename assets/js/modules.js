  /* ---------------------------------------------------------------------
 -Module-
    Allows us to add onload and on resize events to modules by extending them as module objects

 Author:
 Scott Garrison/Aaron Speer
 ------------------------------------------------------------------------ */
 var Module = {
    init : function() {
        var self = this;
        jQuery( window ).on( 'refreshModules', function() {
            self.refresh();
        });
        jQuery( window ).on( 'load', function() {
            self.onLoad();
         });
        jQuery( window ).smartresize( function() {
            self.onResize();
        });
    },
    extend : function( ref ) {
        var extended = jQuery.extend( {}, this, ref );
        return extended.init();
    },
    refresh : function() {
    },
    onLoad : function() {
    },
    onResize : function() {
    }
};

 /* ---------------------------------------------------------------------
 -headerSizing-
    Controls the header fade and parallax functions

 Author:
 Scott Garrison/Aaron Speer
 ------------------------------------------------------------------------ */

var headerScroll = ( function( $ ) {
    var pub = {};

    pub.init = function() {
        // Using an interval instead of the insane window.scroll event
        var scroll_interval = setInterval( function() {
            if( $( '.lfc-section_intro' ).length ) {
                header_fade();
                header_parallax();
            }
        }, 10 );
    };

    // Fade effect for header
    function header_fade() {
        var scroll = $( window ).scrollTop();
        var h = $( '.lfc-section_intro' ).outerHeight();
        var opacity = ( h - scroll ) / h;
        $( '.lfc-section_intro .intro-lfc-content' ).css( 'opacity', opacity.toFixed( 2 ) );
    }

    // Parallax effect for header
    function header_parallax() {
        var scroll = $( window ).scrollTop();
        var h = $( '.lfc-section_intro' ).outerHeight();
        var opacity = ( h - scroll ) / h;
        var transform = scroll/6;
        $( '.lfc-section_intro .intro-lfc-content' ).css( '-webkit-transform', 'translate(-50%, 0 ) translateZ(0) translateY( ' + Math.round( transform ) + 'px)' );
        $( '.lfc-section_intro .intro-lfc-content' ).css( '-moz-transform', 'translate(-50%, 0 ) translateZ(0) translateY( ' + Math.round( transform ) + 'px)' );
    }

    return pub;
}( jQuery ) );

 /* ---------------------------------------------------------------------
 -Full Page Load-
    Controls the sizing and positioning of elements depending on if the
    user is utilizing the full page template or standard template

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
 var fullPageLoad = ( function ( $ ) {

    var pub = {}; //public facing functions
    var menuHeight = 0;

    pub.init = function() {

        contentPosition();

        var is_chrome = window.chrome;

        if( typeof is_chrome != 'undefined' ) {
            var doc = document.documentElement;
            doc.setAttribute( 'data-useragent', 'chrome' );
        }

        // Initialize headroom script to show/hide nav
        var navBox = document.querySelector( '.lfc-section_nav' );
        if(  navBox !== null ) {
             var headroom  = new Headroom( navBox, {
              "offset": 80,
              "tolerance": 5,
            });
            headroom.init();
        }

        if( $( '.section_nav' ).length ) {
            menuHeight = $( '.section_nav' ).outerHeight();
            $( 'body' ).css( 'margin-top', menuHeight );
        }

        var headers = Module.extend({
            onResize : function() { sizeUpdate( menuHeight ) },
            onLoad : function() { sizeUpdate( menuHeight ) }
        });
        
    };

    function contentPosition() {
        $( '#lfc-sections_full' ).width( $( window ).width() );
        $( '#lfc-sections_full' ).offset( {left:0} );
    }

    function sizeUpdate( menuHeight ) {
        var $windowSized = $( '.windowSized' );
        var $windowSized_peak = $( '.windowSized_peak' );
        var $fullfeature_static = $( '.fullFeature-static' );
        var $windowHeight = $( window ).height();
        var $sectionHeight = $windowHeight - 65;

        if( $windowSized.length ) {
            $windowSized.closest( '.lfc-section' ).css( "height" , $windowHeight );
        }

        if( $windowSized_peak.length ) {
            $windowSized_peak.css( "height" , $windowHeight - menuHeight );
        }

        if( $fullfeature_static.length ) {
            $fullfeature_static.each( function() {
                $( this ).css( "height", $windowHeight );
            });
        }

    }

    return pub;
}( jQuery ) );

/* ---------------------------------------------------------------------
 -Debounce/Smart Resize-
    Defines a debounce function, then applies it to our Smart Resize
    method

 Author:
 Scott Garrison
 ------------------------------------------------------------------------ */

( function( $, sr ) {

  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function ( func, threshold, execAsap) {
      var timeout;

      return function debounced () {
          var obj = this, args = arguments;
          function delayed () {
              if ( !execAsap )
                  func.apply( obj, args );
              timeout = null;
          };

          if ( timeout )
              clearTimeout( timeout );
          else if ( execAsap )
              func.apply( obj, args );

          timeout = setTimeout( delayed, threshold || 100 );
      };
  }
  // smartresize 
  jQuery.fn[sr] = function( fn ) {  return fn ? this.bind( 'resize', debounce( fn ) ) : this.trigger( sr ); };

})( jQuery,'smartresize' );


/* ---------------------------------------------------------------------
 -Font Repaint-
	Since we're using a newer technology, it doesn't work perfect. 
	This helps it along with responsiveness

 Author:
 Scott Garrison
 ------------------------------------------------------------------------ */

 var fontRepaint = ( function ( $ ) {

	var pub = {}; //public facing functions

	pub.init = function() {
		var repaintMod = Module.extend({
			onResize : function() { $( '.fontRepaint' ).css( "z-index", 1 ); },
		});
	};

	return pub;
}( jQuery ) );

 /* ---------------------------------------------------------------------
 -Carousel-
	Controls the image carsousel

 Author:
 Scott Garrison
 ------------------------------------------------------------------------ */

 var carousel = ( function ( $ ) {

	var pub = {}; //public facing functions

	pub.init = function() {
		var carouselSections = Module.extend({
			onLoad : makeCarousel,
			refresh : refreshCarousel
		});
	};

    function makeCarousel() {
        $( '.carousel' ).each( function() {
            var self = $( this );
            var slideNumber = self.attr( 'data-slides' );
            
            switch ( slideNumber ) {
                case '1':
                    oneSlide( self );
                    break;
                case '2':
                    twoSlides( self );
                    break;
                case '3':
                    threeSlides( self );
                    break;
                case '4':
                    fourSlides( self );
                    break;
                case '5':
                    fiveSlides( self );
                    break;
                default: 
                    fiveSlides( self );
            }
        });
    }

    function refreshCarousel() {
        $( '.carousel' ).each( function() {
            var self = $( this );
            var slideNumber = self.attr( 'data-slides' );
            switch ( slideNumber ) {
                case '1':
                    oneSlide( self );
                    break;
                case '2':
                    twoSlides( self );
                    break;
                case '3':
                    threeSlides( self );
                    break;
                case '4':
                    fourSlides( self );
                    break;
                case '5':
                    fiveSlides( self );
                    break;
                default: 
                    fiveSlides( self );
            }
        });
    }

	function oneSlide(self) {
        if( self.hasClass( 'slick-slider' ) ) {
            self.unslick();
        }
		self.slick({
			dots: false,
			arrows: false
		});
	}

	function twoSlides( self ) {
        if( self.hasClass( 'slick-slider' ) ) {
            self.unslick();
        }
		self.slick({
		  dots: false,
		  arrows: false,
		  infinite: true,
		  slidesToShow: 2,
		  slidesToScroll: 2
		});
	}

	function threeSlides( self ) {
        if( self.hasClass( 'slick-slider' ) ) {
            self.unslick();
        }
		self.slick({
		  dots: false,
		  arrows: false,
		  infinite: true,
		  slidesToShow: 3,
		  slidesToScroll: 3
		});
	}

	function fourSlides( self ) {
        if( self.hasClass( 'slick-slider' ) ) {
            self.unslick();
        }
		self.slick({
		  dots: false,
		  arrows: false,
		  infinite: true,
		  slidesToShow: 4,
		  slidesToScroll: 4
		});
	}

	function fiveSlides( self ) {
        if( self.hasClass( 'slick-slider' ) ) {
            self.unslick();
        }
		self.slick({
		  dots: false,
		  arrows: false,
		  infinite: true,
		  slidesToShow: 5,
		  slidesToScroll: 5
		});
	}

	return pub;
}( jQuery ) );
 
var navControls = ( function( $ ) {
    var pub = {};

    pub.init = function() {
        $( 'body' ).on( 'click', '.section_nav', function( e ) {
            e.preventDefault();
            $section = $( 'a[name="' + $(this).attr( 'data-section' ) + '"]' );
            $scrollto = $section.offset().top;
            $( 'html, body' ).animate({
                scrollTop: $scrollto
            }, 500 );
            return false;
        })
    };

    return pub;
}( jQuery ) );


/* ---------------------------------------------------------------------
-Controller-
Initialize your scripts in the CONTROL function 

Author:
	Boilerplate
------------------------------------------------------------------------ */
var CONTROL = ( function ( $ ) {

	$(document).ready( function() {
		fontRepaint.init();
		carousel.init();
        fullPageLoad.init();
        headerScroll.init();
        navControls.init();
	});

}( jQuery ) );