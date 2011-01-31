// $Id: the_new_everyday.js

(function() {  
  
  Drupal.behaviors.theneweveryday = function( context ) {   // Implementation of Drupal behavior.
    Drupal.theneweveryday.init( context );
  };
  
  Drupal.theneweveryday = {   // Theme namespace.
    'init' : function ( context ) {
      Drupal.theneweveryday.search( context ); // Bind focus event to search field.
      if ( jQuery('.node-type-contributed-pieces').length ) { // Contributed Pieces
        Drupal.theneweveryday.comments( context );
        Drupal.theneweveryday.slide( context );
        Drupal.theneweveryday.switcher( context );        
        Drupal.theneweveryday.video( context ); // Search for video links and constructs embed object.        
      }
    },
    'slide': function( context ) {
      var JSONOptions = { 'previous' : { 'mouseenter' : '0px 0px', 'mouseleave' : '64px 0px' }, 'next' : { 'mouseenter' : '0px 0px', 'mouseleave' : '-64px 0px' }  };
      function fnSlider( object, event, options ) {      
        if ( object.hasClass('previous') ) {
          object.stop().animate( { backgroundPosition: options.previous[event] }, 500 );
        }
        else if ( object.hasClass('next') ) {
          object.stop().animate( { backgroundPosition: options.next[event] }, 500 );
        }
      }
      jQuery('#navigator a.previous, #navigator a.next')
        .mouseenter(
          function( event ) {
            fnSlider( jQuery( this ), event.type, JSONOptions ); 
          })
      .mouseleave(
      function( event ) { 
        fnSlider( jQuery( this ), event.type, JSONOptions ); 
      });
    },
    'switcher': function( context ) {
      jQuery('#contributor-pictures div.picture:first').addClass('active');
      var active = jQuery('#contributor-pictures div.active');      
      if ( active.length === 0 ) { 
        active = jQuery('#contributor-pictures div:last');
      }     
      var next = active.next().length ? active.next() : jQuery('#contributor-pictures div:first');    
      active.addClass('last-active')
        .animate({opacity : 0.0}, 1000);      
      next.css({opacity: 0.0})
        .addClass('active')
        .animate({opacity: 1.0}, 1000, function() {
          active.removeClass('active last-active');
        });
    },
    'video' : function ( context ) {
      
      Drupal.theneweveryday.video.elements = [];
      Drupal.theneweveryday.video.proced = [];
      Drupal.theneweveryday.video.setup = { params : { 'allowScriptAccess' : 'always', 'allowFullScreen' : true  }, flashVersion : "9.0.0"  };
      
      jQuery( 'a[href*="youtube.com"], a[href*="vimeo.com"]' ).each( function( index ) {        
        var url = jQuery(this).attr('href'), 
            services = ( Drupal.theneweveryday.regex.services.youtube.exec( url ) || Drupal.theneweveryday.regex.services.vimeo.exec( url ) ) || null;
          
        if ( services !== null && services.length > 0 ) {
          if ( jQuery.inArray( services[2], Drupal.theneweveryday.video.proced ) <= -1 ) {
            Drupal.theneweveryday.video.proced[Drupal.theneweveryday.video.proced.length] = services[2];
            var pane = jQuery( this ).parents('#node-pane, #comments').attr('id');
              Drupal.theneweveryday.video.elements[Drupal.theneweveryday.video.elements.length] = {
                elementObject: jQuery( this ),
                position: index,
                service: services[1], 
                video: services[2],
                pane: pane,
                width: ( pane !== 'comments' ) ? 480 : 280,
                height:( pane !== 'comments' ) ? 295 : 210,
                atts: { 'id' : "player-" +  services[2] }
              };         
            }
          }
        });

     if ( Drupal.theneweveryday.video.elements.length > 0 ) { // if items is > 0 then prepare videos and display.
       jQuery.each( Drupal.theneweveryday.video.elements, function( key, target ) {
         var embed = false,
             video_url,
             video_src,
             video_frame;
       
         if ( target.pane !== 'comments' ) {
           target.elementObject.parent('p').append('<div id="pane-'+ target.video + '" class="'+ target.service +'"><div id="player'+ target.video +'"/></div>');
         }
         else {
           target.elementObject.parents('div').append('<div id="pane-'+ target.video + '" class="'+ target.service +'"><div id="player'+ target.video +'"/></div>');
         }         
         if ( target.service === 'youtube.com' ) {
           embed = true;
           video_frame = $('<iframe src="http://www.youtube.com/embed/' + target.video + '" id="player-' + target.video + '" class="youtube-player" type="text/html" width="'+ target.width + '" height="'+ target.height + '" frameborder="0"></iframe>');
           $( '#pane-' + target.video ).append( video_frame );
         }
         else if ( target.service === 'vimeo.com' ) {
           embed = true;
           video_frame = $('<iframe src="http://player.vimeo.com/video/' + target.video + '?js_api=1&js_swf_id=player-' + target.video +'" id="player-' + target.video + '" class="vimeo-player" type="text/html" width="'+ target.width + '" height="'+ target.height + '" frameborder="0"></iframe>');
         }          
         if ( embed ) {
           $( '#pane-' + target.video ).append( video_frame );
           target.elementObject.attr( { 'class' : target.elementObject.attr('class') + ' video ' + target.service } );
         }
        });
      }
    },
    'search' : function ( context ) {
      var default_search_value = jQuery('#edit-search-block-form-1').val();
      jQuery('#edit-search-block-form-1').focus(function() {
        if ( this.value == default_search_value ) {
          this.value = '';
        }
      });
    },
    'regex' : {
      'services' : { 
        'youtube'  : /(?:(youtube.com\/v\/|youtube.com)\/watch\?v=)([^&]+)/,
        'vimeo' : /(?:(vimeo.com))\/(\d+)/ 
      }
    },
    'throttle' : function ( fn, delay ) {
      var timer = null;
      return function () {
        var context = this, args = arguments;
          clearTimeout(timer);
          timer = setTimeout(function () {
            fn.apply( context, args );
      }, delay);
      };
    },
    'comments' : function ( context ) {
      var content   = jQuery('#node-pane'),
          comments  = jQuery('#comments'),
          scrollbar = comments.find('.view-nodecomments'),
          initSize  = ( jQuery(window).height() - content.offset().top - 30 );
      jQuery.extend( jQuery.fn.jScrollPane.defaults, { 'scrollbarWidth' : 10, 'showArrows' : false } ); // set the default options for scrollpane
      comments.find('div.wrapper').each( function( index ) {
        if ( jQuery( this ).closest('.wrapper').next('.indented').html() ) {
          jQuery( this ).closest('.wrapper').next(".indented").hide();
          jQuery( this ).children().append( '<a href="#" id="m' + index + '">' + Drupal.t( 'See replies to this response' ) + '</a>' );
          jQuery( '#m' + index ).bind('click', function ( event ) {
            if ( jQuery( this ).html() === Drupal.t( 'See replies to this response' ) ) {
              jQuery( this ).closest('.wrapper').next('.indented').show();
              jQuery( this ).html( Drupal.t( 'Hide replies' ) );
              comments.find('.view-nodecomments').jScrollPane();          
            }
            else if ( jQuery( this ).html() === Drupal.t( 'Hide replies' ) ) {
              jQuery( this ).closest('.wrapper').next('.indented').hide();        
              jQuery( this ).html( Drupal.t('See replies to this response') );          
              comments.find('.view-nodecomments').jScrollPane();
            }
            event.preventDefault();
          });
        }
      });
      comments.find('div.content').truncate( { 'max_length' : 200,  'more': Drupal.t( 'Read more' ) } ); // Truncate response content.
      comments.css({ height: function() { return  initSize; } })
        .scrollFollow( { 'container' : content.attr('id'), 'offset' : 5 } ); // Set comment height and add "follow" feature.
      scrollbar.css({ 'height' : function() { return  initSize; } })
        .jScrollPane( { 'reinitialiseOnImageLoad' : true } ); // Create scrollbar and set height.
      jQuery( window ).resize(function () { // bind onresize event
        comments.css({
        height: function() {
          return jQuery( window ).height() - 30;
          }
        });
      scrollbar.css({
        'height' : function() {
          return jQuery( window ).height() - 30;
        }
      })
      .jScrollPane();
      });
      jQuery( window ).scroll( Drupal.theneweveryday.throttle( function ( event ) { // bind scroll enevent    	  
    	var YOffset = window.pageYOffset || document.documentElement.scrollTop;    	
        if ( parseInt( YOffset, 10 ) >= parseInt( comments.offset().top, 10 ) ) {
          comments.css({
            height: function() {
              return jQuery( window ).height() - 30;
            }
          });
          
          scrollbar.jScrollPaneRemove();
          
          scrollbar.css({ 
            'height' : function() { 
              return jQuery( window ).height() - 30;
            }
          }).jScrollPane();
        }
        
        if ( ( parseInt( YOffset, 10 ) < parseInt( comments.offset().top, 10 ) ) && ( parseInt( YOffset, 10 ) <= initSize ) ) {
          comments.css({
            height: function() {
              return ( ( jQuery( window ).height() - 30 ) - ( comments.offset().top - YOffset - 30 ) ) - 10;
            }
          });            
        
          scrollbar.jScrollPaneRemove();
          scrollbar.css({ 
            'height' : function() { 
              return ( ( jQuery( window ).height() - 30 ) - ( comments.offset().top - YOffset - 30 ) ) - 10;
            }
          }).jScrollPane();
        }             
      }, 250));     
    }
  };
})();