// Implementation of Drupal behavior.

Drupal.behaviors.dltsRubik = function(context) {  
  Drupal.dltsRubik.init( context );  
};

Drupal.dltsRubik = {
  'init': function( context ) {
	if ( typeof window.CKEDITOR === 'object' ) { 
	  Drupal.dltsRubik.setup.ckeditor( context );
	}
	
	Drupal.dltsRubik.setup.cluster( context );
	Drupal.dltsRubik.setup.piece( context );

	if ($('.page-node-edit, .page-node-add').length) {
	  var jquery_ui = Drupal.settings.tne.jquery_ui || false;
	  if ( jquery_ui ) {
	    Drupal.dltsRubik.setup.tabs( context );
	  }
	}
  },
  'setup' : {
    'ckeditor' : function( context ) { // CKEditor; config.
          
        CKEDITOR.config.pasteFromWordRemoveFontStyles = true;
		CKEDITOR.config.pasteFromWordRemoveStyles = true;
		CKEDITOR.config.startupOutlineBlocks = true;			
		CKEDITOR.config.protectedSource.push( /<img[^>]+style="(?:'[^']*'|""[^""]*""|[^\s>]+)([^>]*)"[^>\/]*\/>/g );

		$("textarea.ckeditor-mod").each(function ( index ) { // CKeditor. Make sure "Browse Server Window" pop-up open with the best possible size.
		  var ta_id = $(this).attr("id");  
		  Drupal.settings.ckeditor.settings[ta_id].height = 500;  
		  if ( $(window).height() >= 700 ) {
		    Drupal.settings.ckeditor.settings[ta_id].filebrowserWindowHeight = 700;
		  } 
		  else if ( $(window).height() < 700 ) {
		    Drupal.settings.ckeditor.settings[ta_id].filebrowserWindowHeight = ( $(window).height() - 15 );
		  }
		});
    },
    'cluster' : function( context ) { // cluster; config.
      // Cluster edit view
      if ( $('input[name=field_cluster_type[value]]').length ) { // Hide "Embedded video" or "Image" fieldset given "Content settings" selection.
        var cluster_edit_view_content_settings = $('input[name=field_cluster_type[value]]:checked').val();
        if ( cluster_edit_view_content_settings === '0' ) {
    	  $('.group-image').hide().addClass('hidden');
    	  $('.group-video').addClass('open');
    	}
    	else if ( cluster_edit_view_content_settings === '1' ) {
    	  $('.group-video').hide().addClass('hidden');
    	  $('.group-image').addClass('open');
    	}
        $('input[name=field_cluster_type[value]]:radio').change( function() {  // Cluster edit view, change event for "Content settings"
    	  var value = $('input[name=field_cluster_type[value]]:checked').val();
    	  if ( value === '0' ) {
    		if ($('.group-image').is('.open') ) {
    		  $('.group-image').removeClass('open').hide();
    		}
    		$('.group-video').addClass('open').show();        
    	  }
    	  else if ( value === '1' ) {
    		if ( $('.group-video').is('.open') ) {
    		  $('.group-video').removeClass('open').hide();
    		}
    		$('.group-image').addClass('open').show();        
    	  }      
        });	
      }
    },
    'piece' : function( context ) { // piece; config.
      if ( Drupal.settings.tne.ckeditor.images ) {
        var template = $('<div class="presetSizes"></div>');
    	for ( var size in Drupal.settings.tne.ckeditor.images ) {
    	  if ( Drupal.settings.tne.ckeditor.images.hasOwnProperty( size ) ) {
    		var a = $('<a>')
    		    .html('<< add: ' + Drupal.settings.tne.ckeditor.images[size].label)
    			.attr({
    			  'class' : size + ' cluetip',
    			     'href' : '#' + size,
    			        'title' : 'Click to add "' + Drupal.settings.tne.ckeditor.images[size].label +'" image to the document body. After adding image you can move around, resize or edit image properties.' 
    			      });
    			  template.append( a );
    			}
    		  }
    		}

    		$('.image.image-thumbnail').each(function(index) {
    		  // Change <label>Thumbnail</label> to <label>Image Title</label> for each image.
    		  $(this).siblings("label").html($(this).attr('title')).attr({ 'class' : 'tne-rubik-processed rubik-processed thumbnail'});
    		  // add "Insert image to editor links"
    		  if ( template ) {       
    		    template.clone().appendTo( $(this).parents('div.form-item-labeled') );
    		  }
    		});
    			      
    		$('div.presetSizes a[title]').qtip({
    			   content: this.title,
    			   position: {
    				      corner: {
    				         target: 'leftMiddle',
    				         tooltip: 'rightMiddle'
    				      }
    			   },
    			   style: {
    				  width: 200,
    			      background: '#f8fffc',
    			      textAlign: 'left',
    			      fontSize: '10px',
    			      color: '#687',
    			      border: {
    			         width: 1,
    			         color: '#ccc'
    			      },
    			      width: 200,
    			      tip: { // Now an object instead of a string
    			          corner: 'rightMiddle', // We declare our corner within the object using the corner sub-option
    			          color: '#ccc'    			          
    			        }
    		          }
    		        });    		
    			      
    		// Add tooltip and bind event listener to insert image links

    		$('div.presetSizes a').bind('click', function( event ) {
    		  var image = $(this).parent('div').siblings("img.image-thumbnail"),
    		      imageSRC = image.attr("src"),
    		      imageHASH = this.hash.replace('#', ''), 
    		      path = window.top.location.href.replace(window.top.location.pathname, '');
    			           
    		  if ( imageHASH === '_original' ) {
    		    imageSRC = imageSRC.replace('.thumbnail.', '.');
    		  }
    		  else {
    		    imageSRC = imageSRC.replace('.thumbnail.', '.' + imageHASH + '.');
    		  }
    			       
    		  Drupal.ckeditorInsertHtml('<img src="'+ imageSRC.replace(path, '') +'"/>');
    			       
    		  event.preventDefault();
    		});
    			     
    		var box = $('<div class="fieldset attached-images-op js-rubik-processed"><h2 class="fieldset-title">' + Drupal.t('Attach new image') + '</h2></div>');
    		$('#edit-image-title-wrapper, #edit-image-wrapper, #edit-image-attach-multiple').appendTo(box);
    		$(box).insertBefore($('.attached-images'));    	
    	
        	// end piece    	
    },
    'tabs' : function ( contex ) { // "theme" tabs
  	  var others_tabs = $('<div id="others_tabs"><ul class="others_tabs_ul"></ul></div>');
          others_tabs.insertBefore($('.column-main .buttons'));
        
      $("fieldset.menu-settings, fieldset.revision-information, fieldset.url-path-settings, fieldset.file-attachments, fieldset.authoring-information, fieldset.publishing-options, fieldset.comment-settings, fieldset.url-redirects").not('.group-document').each(function( index ) {		  
  	    if ( $(this).hasClass('collapsible') ) {
  	      $(this).removeClass('collapsible');
  	    }
  	    if ( $(this).hasClass('collapsed') ) {
  	      $(this).removeClass('collapsed');
  	    }
  	
  	    var title = $(this).children('legend').text();
  	
  	    if (title === "Revision information") { title = "Revision"; }
  	      else if (title === "Menu settings") { title = "Menu"; }
  	      else if (title === "URL path settings") { title = "URL"; }
  	      else if (title === "Comment settings") { title = "Comment"; }
  	      else if (title === "Authoring information") { title = "Authoring"; }
  	      else if (title === "Publishing options") { title = "Publishing"; }
  	      else if (title === "URL redirects") { title = "Redirects"; }
  	      else if (title === "File attachments") { title = "Attachments"; }
  	
  	    $('<li><a href="#tab-'+index+'">'+title+'</a></li>').appendTo($('.others_tabs_ul'));		  
  	    $('<div id="tab-'+index+'"></div>').appendTo(others_tabs);
        $(this).appendTo('#tab-'+index);
      });
      
      if ($('#edit-language-wrapper').length) {
    	$('<li><a href="#edit-language-wrapper">Language</a></li>').appendTo($('.others_tabs_ul')); 
        $('#edit-language-wrapper').appendTo('#others_tabs');      
      }    
      
      $("#others_tabs").tabs(); 	  
    }
  }  
};