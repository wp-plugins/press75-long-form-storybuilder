/* ---------------------------------------------------------------------
 -Nav Controls-
    Adds functionality to the Nav Module controls/inputs

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
var lfcNavControls = ( function( $ ) {
    var pub = {};

    pub.init = function() {

        // Toggle custom inputs
        $( '.lfc-menu-toggle' ).each( function() {
            toggle_inputs( $( this ) );
        });

        // The text template for the individual LI elements with placeholders
        var li_text = '<li data-url="%url%" data-title="%data-title%"><span class="lfc-delete-page">x</span>%title%</li>';

        // Initialize the sortable functions whenever we add/edit a nav control group
        $( 'body' ).on( 'mouseup', 'li[id*=lfc][class*=customize-control]', function( e ) {
            initializeSortable( $( this ) );
        }).on( 'mouseup', 'div[id*=lfc][class*=widget-tpl]', function( e ) {
            setTimeout( function() {
                initializeSortable( $( this ) );
            }, 0);
        });

        // Add selected pages to current items list on click
        $( 'body' ).on( 'click', '.lfc-add-pages', function( e ) {
            e.preventDefault();

            // Get widget parent
            var widget = $( this ).closest( '.widget-content' );

            // Loop through items and add them to the list
            $( this ).closest( '.posttypediv' ).find( '.menu-item-checkbox' ).each(function() {
                if( $( this ).is( ':checked' ) ) {
                    var title = $( this ).attr( 'data-title' );
                    var value = $( this ).val();

                    // Setup new item using the template and replacing placeholders with accurate data
                    var li = li_text.replace( '%title%', title).replace( '%data-title%', title).replace( '%url%', value);
                    widget.find( '.lfc-current-pages-list' ).append(li);

                    // Uncheck all the items
                    $( this ).prop( 'checked', false); 
                }
            });

            update_menu_pages(widget);
        });

         // Add custom link to current items list on click
        $( 'body' ).on( 'click', '.lfc-add-custom', function( e ) {
            e.preventDefault();

            // Get parent widget
            var widget = $( this ).closest( '.widget-content' );

            // Grab the data from the input fields
            $( this ).closest( '.posttypediv' ).find( '.lfc-new-link-wrapper' ).each(function() {
                var title = $( this ).find( '.new_custom_title' ).val();
                var value = $( this ).find( '.new_custom_link' ).val();

                // Only add the link if both fields are filled out
                if( title != '' && value != '' ) {
                    var li = li_text.replace( '%title%', title).replace( '%data-title%', title).replace( '%url%', value);
                    widget.find( '.lfc-current-pages-list' ).append(li);

                    // Clear inputs on success
                    $( this ).find( '.new_custom_title' ).val( '' );
                    $( this ).find( '.new_custom_link' ).val( '' );
                }
            });

            update_menu_pages(widget);
        });

        // Add section link to current items list on click
        $( 'body' ).on( 'click', '.lfc-add-section', function( e ) {
            e.preventDefault();

            // Get parent widget
            var widget = $( this ).closest( '.widget-content' );

            // Grab the data from the input field
            $item = $( this ).closest( '.posttypediv' ).find( '.lfc-page-section-wrapper' ).val();
            $href = '#' + $item;
            var li = li_text.replace( '%title%', $item).replace( '%data-title%', $item).replace( '%url%', $href);
            widget.find( '.lfc-current-pages-list' ).append(li);
                

            update_menu_pages(widget);
        });

        // Delete pages from list when delete button is clicked
        $( 'body' ).on( 'click', '.lfc-delete-page', function() {
            var widget = $( this ).closest( '.widget-content' );
            $( this ).parent().remove();
            update_menu_pages(widget);
        });

        // Toggle inputs based on menu type
        $( 'body' ).on( 'change', '.lfc-menu-toggle', function() {
            toggle_inputs( $( this ) );
        });

    };

    function toggle_inputs( input ) {
        var container = input.closest( '.widget-content' );
        if( input.val() == 'wp_menu' ) {
            $( '.lfc_show_wp', container).each( function() {
                $( this ).show();
            });
            $( '.lfc_show_custom', container).each( function() {
                $( this ).hide();
            });
        }else{
            $( '.lfc_show_custom', container).each( function() {
                $( this ).show();
            });
            $( '.lfc_show_wp', container).each( function() {
                $( this ).hide();
            });
        }
    }

    // Update JSON string of current pages whenever the list is changed/updated
    function update_menu_pages(widget) {

        // Wrap in a zero second timeout to remove race conditions
        setTimeout( function() {
            var all_pages = [];
            widget.find( '.lfc-current-pages-list li' ).each(function() {
                var this_data = { 'url': $( this ).attr( 'data-url' ), 'title': $( this ).attr( 'data-title' ) };
                all_pages.push(this_data);
            });

            // Update JSON string and push to input
            widget.find( '.lfc-all-pages' ).val( JSON.stringify( all_pages ) ).change();
        }, 0);
        
    }

    // Initialize the sortable functionality
    function initializeSortable( obj ) {
        var widget = obj.find( '.widget-content' );
        $( '.lfc-sortable-items' ).sortable({
            change: update_menu_pages(widget)
        });
    }

    return pub;
}( jQuery ) );

/* ---------------------------------------------------------------------
 -LFC Image Field-
    Adds controls and methods for our custom Image Field

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
 
var lfcImageField = ( function( $ ) {
    var pub = {};

    pub.init = function() {
        var selectors = '#wpbody, #customize-controls';
        // Initialize the LFC Image fields
        lfcImage.init(selectors);
    };

    lfcImage = {
        // Load the frame
        frame: function() {
            // Return the frame if it already exists
            if ( this._frame )
                return this._frame;

            // Initialize the frame
            this._frame = wp.media({
                title: data.title,
                frame: 'select', // Post instead will give options for image from url and galleries
                library: { type: 'image' },
                button: { text: data.update },
                multiple: false
            });

            // On specific events
            this._frame.on( 'open', this.updateFrame);
            this._frame.state( 'library' ).on( 'select', this.select);

            // Save the frame
            return this._frame;
        },

        // When the frame is closing
        select: function() {
            // Closing the frame and selecting image
            var selection = this.get( 'selection' );
            //TODO more images var attachmentIds = [];

            // If a target has been selected get the id and put it inside a variable
            if ( target.length ) {
                target.val( selection.pluck( 'id' ) ).change();
            }

            // For every attachment selected
            selection.map( function( attachment ) {
                // To JSON as we are no longer working with the backbone object
                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    // Get the image inside the widget
                    var img = $( "#lfc-image-" + data.target + ".lfc-image .image-section div img" );
                    // Find the right widget from the data target
                    var sectionImage = $( "#lfc-image-" + data.target + " .image-section" );
                    var sectionNewImage = $( "#lfc-image-" + data.target + " .newimage-section" );
                    var imageField = $(sectionImage).find( ".lfc-image-id" );

                    // Change the src on the image chosen
                    // When the image is too small it will not return attachment.sizes
                    if( attachment.sizes && attachment.sizes.medium )
                        $(img).attr( "src", attachment.sizes.medium.url );
                    else
                        $(img).attr( "src", attachment.url );

                    // If there is an id show the image and hide the bigger button
                    if( imageField.val() > 0 ) {
                        sectionImage.show();
                        sectionNewImage.hide();
                    }
                }
            });

            //TODO more images attachmentIds = attachmentIds.join( "," );
        },

        // When the frame is opening
        updateFrame: function() {
            // Get the selected image to also make it selected when we open the frame
            var selection = this.get( 'library' ).get( 'selection' );
            var attachment;

            if ( target.length ) {
                // Get the variable from we choose in select and make it selected
                selectedIds = target.val();
                if ( selectedIds && '' !== selectedIds && -1 !== selectedIds && '0' !== selectedIds ) {
                    // Get the attachments
                    attachment = wp.media.model.Attachment.get( selectedIds );
                    attachment.fetch();
                }
            }

            // No idea why we fetch and reset them. But it works
            selection.reset( attachment ? [ attachment ] : [] );
        },

        // Initialize the whole object
        init: function(selectors) {

            // Initialize all lfc-image widgets: show or hide image/newImage
            var imageFields = $( ".lfc-image" );
            $.each(imageFields, function() {
                var sectionImage = $( this ).find( ".image-section" );
                var sectionNewImage = $( this ).find( ".newimage-section" );
                var imageField = $(sectionImage).find( ".lfc-image-id" );

                // If there is an id show the image and hide the bigger button
                if( imageField.val() > 0 ) {
                    sectionImage.show();
                    sectionNewImage.hide();
                }
            });

            // Make sure the markup stays the same even after a click on the save button
            $( 'body' ).on( 'click', '.widget-control-save', function() {
                // Do this after the ajax call and the values has been saved
                $( this ).ajaxSuccess(function() {
                    // Same thing as when we initialized the lfc-image widgets. Just only initialize the one with the button clicked
                    var form = $( this ).closest( "form" );
                    var sectionImage = $(form).find( ".image-section" );
                    var sectionNewImage = $(form).find( ".newimage-section" );
                    var imageField = $(sectionImage).find( ".lfc-image-id" );

                    // If there is an id show the image and hide the bigger button
                    if( imageField.val() > 0 ) {
                        sectionImage.show();
                        sectionNewImage.hide();
                    }
                });
            });

            // Open media frame when we click the image button
            $( 'body' ).on( 'click', '.lfc-image-select', function( e ) {
                e.preventDefault();

                // Save all the data- attr inside a global variable
                data = $( this ).data();
                // The target is the hidden field inside the .image-section
                target = $( ".image-section #" + data.target);

                // Open the frame by calling the frame() function
                lfcImage.frame().open();
            });

            // Hide image and set the target field to 0 to also remove it when the widget is saved
            $( 'body' ).on( 'click', '.lfc-image-remove', function( e ) {
                e.preventDefault();

                var data = $( this ).data();
                var target = data.target;

                // Set the target value to 0
                $( "#lfc-image-" + target + " .image-section .lfc-image-id" ).val(0);
                // Hide image section as there now is no image
                $( "#lfc-image-" + target + " .image-section" ).hide();
                // Show the newimage section as there now is no image
                $( "#lfc-image-" + target + " .newimage-section" ).show();
            });
            
        }

    };

    return pub;
}( jQuery ) );

/* ---------------------------------------------------------------------
 -LFC Carousel Controls-
    Adds methods for dealing with our hand-rolled WYSIWYG field

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
 var lfcCarouselControls = ( function( $ ) {
     var pub = {};
 
     pub.init = function() {

        // Function to insert selected media into the list of current images
        $( 'body' ).on( 'click', 'div[id*=lfc-image-carousel-module] .add-image-to-carousel', function( e ) {
            e.preventDefault();

            var image_container = $( this ).parent().find( '.lfc-carousel-images' );
            var images_hidden = $( this ).parent().find( '.images_hidden' );
            var images_json = images_hidden.val();
            var images_list = $.parseJSON( images_json );

            file_frame  = wp.media({
                frame:   'select',
                state:   'mystate',
                library:   {type: 'image'},
                multiple:   false
            });

            file_frame.states.add([

                new wp.media.controller.Library({
                    id: 'mystate',
                    title: 'Add an Image to Carousel',
                    button: {
                        text: 'Add to Carousel'
                    },
                    priority:   20,
                    toolbar:    'select',
                    filterable: 'uploaded',
                    multiple:   true,
                    editable:   false,
                    displayUserSettings: false,
                    displaySettings: false,
                    allowLocalEdits: false,
                })

            ]);

            //var send_attachment_bkp = wp.media.editor.send.attachment;
            file_frame.on( 'select', function() {
                var attachments = file_frame.state().get( 'selection' );
                var files = [];
                var new_file = {};
                var loop = attachments.map( function( attachment ) {
                    image = attachment.toJSON();
                    new_file = {
                        'id' : image.id,
                        'url' : image.url,
                        'thumbnail' : image.sizes.thumbnail.url,
                        'title' : image.title
                    };
                    files.push( new_file );
                });



                for( var i=0; i<files.length; i++ ) {
                    var json_markup = JSON.stringify( files[i] );
                    var markup = '<div class="lfc-carousel-image" data-json=\'' + json_markup + '\'><div class="remove-lfc-carousel-image">x</div><img src="' + files[i].thumbnail + '" /></div>';
                    image_container.append( markup );
                    images_list.push( files[i] );
                }

                images_hidden.val( JSON.stringify( images_list ) ).change();

            });

            file_frame.open();
            return false;
        });
        
        // Initialize the sortable functions whenever we add/edit a nav control group
        $( 'body' ).on( 'mouseup', 'li[id*=lfc][class*=customize-control]', function( e ) {
            initializeSortable( $( this ) );
        }).on( 'mouseup', 'div[id*=lfc][class*=widget-tpl]', function( e ) {
            setTimeout( function() {
                initializeSortable( $( this ) );
            }, 0);
        });

        // Remove images when remove button is clicked
         $( 'body' ).on( 'click', '.remove-lfc-carousel-image', function( e ) {
            var image = $( this ).closest( '.lfc-carousel-image' );
            image.remove();
            var widget = $( this ).find( '.widget-content' );
            update_image_json(widget);
        })

     }; // endof pub.init()
    
     // Initialize the sortable functionality
    function initializeSortable( obj ) {
        var widget = obj.find( '.widget-content' );
        $( '.lfc-carousel-images' ).sortable({
            change: update_image_json(widget)
        });
    }

    function update_image_json(widget) {
        // Wrap in a zero second timeout to remove race conditions
        setTimeout( function() {
            var all_images = [];
            widget.find( '.lfc-carousel-images > div' ).each(function() {
                var this_json = $( this ).attr( 'data-json' );
                var this_data = JSON.parse( this_json );
                all_images.push(this_data);
            });

            // Update JSON string and push to input
            widget.find( '.images_hidden' ).val( JSON.stringify( all_images ) ).change();
        }, 0);
    }

     return pub;
 }( jQuery ) );

/* ---------------------------------------------------------------------
 -LFC WYSIWYG Controls-
    Adds methods for dealing with our hand-rolled WYSIWYG field

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
var lfcControls = ( function( $ ) {

    var pub = {};

    pub.init = function() {
        
        // Toggle module instructions on click
        $( 'body' ).on( 'click', '.lfc-module-instructions', function( e ) {
            $( this ).find( '.lfc-instructions-bd' ).slideToggle( 'fast' );
        });

        // Toggle the WYSIWYG editor when user clicks tabs
        $( 'body' ).on( 'click', '.html-button', function( e ) {
            e.preventDefault();
            toggle_editor_mode( $( this ), 'html' );
        });

        $( 'body' ).on( 'click', '.visual-button', function( e ) {
            e.preventDefault();
            toggle_editor_mode( $( this ), 'visual' );
        });

        // Go through all the flim-flam of initializing the visual editor every time we add/edit/move a field...yes it's overly complicated but there you are. 
        $( 'body' ).on( 'mouseup', 'li[id*=lfc][class*=customize-control]', function( e ) {

            // Do nothing if we're not dealing with one of our custom fields
            if( !$( this ).find( '.widget-title h4' ).is( ':hover' ) && !$( this ).find( '.widget-title-action' ).is( ':hover' ) ) {
                return;
            }

            // Find visual editors on the page
            var editors = $( this ).find( '.lfc-visual-editor' );

            // If they exist, tear down and rebuild the editor instances
            if( typeof editors != 'undefined' ) {

                editors.each( function() {
                    var id = $( this ).attr( 'id' );
                    if( typeof(tinymce) !== 'undefined' ) {
                        if( tinymce.get(id) != 'null' ) {
                            setTimeout( function() {
                                tinymce.EditorManager.execCommand( 'mceRemoveEditor', true, id );
                                tinyMCE_init(id);
                            }, 0);
                        }
                    }
                });
                
            }
        });
        
        // Same thing, except when we create a new widget
        $( 'body' ).on( 'mouseup', 'div[id*=lfc][class*=widget-tpl]', function( e ) {
            // After timeout to avoid race conditions, reinstate all the editors
            setTimeout( function() {
                $( '.lfc-visual-editor' ).each(function() {
                    var editor = $( this );
                    var id = editor.attr( 'id' );
                    if( id.indexOf( '__i__' ) == -1 ) {
                        if( typeof(tinymce) !== 'undefined' ) {
                            tinymce.EditorManager.execCommand( 'mceRemoveEditor', true, id );
                            tinyMCE_init(id);
                        }
                    }
                  
                });
                
            }, 300);
        });

        // Function to insert selected media into the proper WYSIWYG field
        $( 'body' ).on( 'click', 'div[id*=lfc-content] .insert-media', function( e ) {
            e.preventDefault();
            var id = $( this ).closest( '.lfc-editor-container' ).find( 'textarea' ).attr( 'id' );
            var editor = tinymce.get( id );
            send_to_editor = function( media ) {
                editor.execCommand( 'inserthtml', false, media, null );
                editor.focus();
                var textarea = tinymce.activeEditor.getElement();
                var content = editor.getContent( {format : 'raw'} );
                $( textarea ).val( content ).change();
            };
        });

    }; // end pub.init()
    
    // Function to actually initialize the TinyMCE editor
    function tinyMCE_init(id) {
        tinymce.init({
            selector: "textarea#" + id,
            menu: false,
            menubar: false,
            theme: 'modern',
            plugins: ["wplink"],
            toolbar: 'bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link unlink ',
            content_css: paths.plugin_dir + '/assets/css/content_css.css',
            height: 400,
            setup: function(editor) {
                editor.on( 'keyup blur paste', function( e ) {
                    var content = tinymce.activeEditor.getContent( {format : 'raw'} );
                    var textarea = tinymce.activeEditor.getElement();
                    $( textarea ).val( content ).change();
                });
                editor.on( 'click', function( e ) {
                    tinymce.execCommand( 'mceFocus', false );
                });
            }
        });
    }

    // Swap the editor mode when user clicks on a tab
    function toggle_editor_mode(el, mode) {
        el.parent().toggleClass( 'html-active' );
        el.parent().toggleClass( 'tmce-active' );
        var editor = el.closest( '.lfc-editor-container' ).find( '.lfc-visual-editor' );
        var id = editor.attr( 'id' );

        if( 'html' == mode ) {
            if( typeof(tinymce) !== 'undefined' ) {
                var area = tinymce.get(id).getElement();
                var frame = $( area ).parent().find( 'iframe' );
                var status = $( area ).parent().find( '.mce-statusbar' );
                $( area ).height( 400 ).show();
                $( frame ).hide();
                $( status ).hide();
            }
        }else{
            var area = tinymce.get(id).getElement();
            var frame = $( area ).parent().find( 'iframe' );
            var status = $( area ).parent().find( '.mce-statusbar' );
            $( area ).height( 400 ).hide();
            var content = $( area ).val();
            tinymce.activeEditor.setContent( content );
            $( frame ).show();
            $( status ).show();
        }
    }

    return pub;
}( jQuery ) );

/* ---------------------------------------------------------------------
 -LFC Nav Update-
    Adds Methods for dealing with updating the nav bar sections

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
var lfcNavUpdate = ( function( $ ) {
    var pub = {};

    pub.init = function() {
        getSections();
        setInterval( getSections, 5000 );
    };

    function getSections() {
        // Get all the nav modules
        $nav_sections = $( '.customize-control[id*=lfc-nav-module' );

        // If nav modules exist, update section names for each one
        if( $nav_sections.length ) {
            $nav_sections.each( function() {
                if( $( this ).closest( '.customize-control' ).hasClass( 'expanded' ) ) {
                    //do nothing
                }else{
                    $self = $( this );

                    // Get the list section and clear it
                    $list = $self.find( '.lfc-page-section-wrapper' );
                    
                    // Get the parent section for each module
                    $parent = $self.closest( '.control-section' );

                    // Get the various widgets for this parent
                    $widgets = $parent.find( '.customize-control' );
                    $list.html( '' );
                    // Grab the Title values for each widget
                    for( var i=0; i < $widgets.length; i++ ) {
                            
                        $widget = $( $widgets[i] );
                        $title = $widget.find( 'input[id*=title]' ).val();
                        
                        // Update the nav module list if title exists
                        if( $title != '' && typeof $title != 'undefined' ) {
                            $markup = '<option value="' + $title + '">' + $title + '</option>';
                            $list.append( $markup );
                        }
                        
                    }
                }
                
                
            });
        }
    }

    return pub;
}( jQuery ) );

/* ---------------------------------------------------------------------
 -LFC Remove Widget Confirmation-
    Adds methods for confirming that the user wants to delete a widget

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
var removeConfirm = ( function( $ ) {
    var pub = {};

    pub.init = function() {
        $( 'body' ).on( 'click', '.widget-control-actions .alignright', function( e ) {
            var r = confirm( "Are you sure you want to remove this section?" );
            if ( r === true ) {
                $( this ).closest( '.widget-control-actions' ).find( '.widget-control-remove' ).click();
            }
        });
    };

    return pub;
}( jQuery ) );

/* ---------------------------------------------------------------------
 -LFC Scroll To Section-
    Adds methods to scroll to the desired widget section in the preview
    when the control is enabled

 Author:
 Aaron Speer
 ------------------------------------------------------------------------ */
var scrollToSection = ( function( $ ) {
    var pub = {};

    pub.init = function() {

        $( 'body' ).on( 'mouseup', 'li[id*=lfc][class*=customize-control]', function( e ) {
            
            if( $( this ).hasClass( 'expanded' ) ) {
                return;
            }

            var id = $( this ).attr( 'id' ).replace( 'customize-control-widget_', '' );
            $( document ).trigger( 'scroll_to_section', [ id ] );
        });

        $( 'body' ).on( 'mouseup', 'div[id*=lfc][class*=widget-tpl]', function( e ) {
            setTimeout( function() {
                $( document ).trigger( 'scroll_to_bottom' );
            }, 1000);
        });
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

    $( document ).ready( function() {
        lfcNavControls.init();
        lfcImageField.init();
        lfcCarouselControls.init();
        lfcControls.init();
        lfcNavUpdate.init();
        removeConfirm.init();
        scrollToSection.init();
    });

}( jQuery ) );