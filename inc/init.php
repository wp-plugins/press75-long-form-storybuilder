<?php

/**
 * Long Form Content Class
 *
 * Instigates the various classes required for the plugin;
 * manages actions related to the global plugin functionality
 *
 * @package lfc
 */
class LongformContent {
  
  private static $instance;
  function __construct() {
    $this->scripts = LFC_Scripts::getInstance();
    $this->options = LFC_Options::getInstance();
    $this->widgets = LFC_Widgets::getInstance();

    add_filter('show_admin_bar', '__return_false');
    add_action( 'widget_update_callback', array( $this, 'lfc_queue_backups' ), 10, 4 );
    add_action( 'customize_save_after', array( $this, 'lfc_content_backup' ) );
  }

  /**
   * LFC Queue Backups
   *
   * Enqueues any pages that have been changed for the future
   * lfc_content_backups function below
   * @param obj $instance
   * @param obj $new_instance
   * @param obj $old_instance
   * @param WP_Widget $widget
   * @return obj 
   */
  function lfc_queue_backups( $instance, $new_instance, $old_instance, $widget ){

    // Get ID string for this widget
    $id = $widget->id;

    // Get all sidebars stored currently
    $sidebars = get_option( 'sidebars_widgets' );
    
    // Get our stored posts queue, otherwise set it to a blank array
    if( get_option('lfc_posts_to_update') ){
      $posts_to_update = get_option( 'lfc_posts_to_update', true );
    }else{
      $posts_to_update = array();
    }

    // For each sidebar, grab the widgets involved
    foreach ( $sidebars as $name=>$widgets ){

      // If our updated widget exists in this sidebar, grab its ID and add it to our queue to update
      if( is_array( $widgets ) && in_array( $id, $widgets ) ){

        // Parse out our post ID from the sidebar name
        $post_id = str_replace( 'modules-', '', $name );
        if( !in_array( $post_id, $posts_to_update ) ){
          $posts_to_update[] = $post_id;
        }
        
        // No need to keep looping through
        break;
      }
    }

    // update_option doesn't work in these calls for some reason, so we have to do it the good ol' fashioned way
    delete_option( 'lfc_posts_to_update' );
    add_option( 'lfc_posts_to_update', $posts_to_update );


    return $instance;
  }

  /**
   * LFC Content Backup
   *
   * For each enqueued page, grab the module data and set it as the 
   * body content for that post
   * @param array $data
   */
  function lfc_content_backup( $data = null ) {

    // Grab our queued post ID's. If they aren't set, exit
    if( get_option('lfc_posts_to_update') ){
      $posts_to_update = get_option( 'lfc_posts_to_update', true );

      // Grab the list of fields to include
      $included_fields = $this->lfc_included_fields();
    }else{
      return;
    }

    foreach( $posts_to_update as $post ){

      // Set up the sidebar name based on the post ID
      $name = 'modules-' . $post;

      // Get the widget data for this sidebar
      $widget_data = $this->lfc_get_widget_data_for( $name );

      $body = '';

      // If we have widgets here, search them for the search string
      if( count( $widget_data ) <= 0 ){
        return;
      }

      foreach( $widget_data as $widget ){

        // Convert object to array so we can loop through with a foreach
        $widget_arr = json_decode( json_encode( $widget ), true );
        
        // Loop through each widget key/value
        foreach( $widget_arr as $key=>$value ){

          // If our field is in the allowed list, append it's value to the new body
          if( in_array( $key, $included_fields ) && $value != '' ){

            $body .= '<p>' . $value . '</p>'; 

          }

        }

      }

      // Create new post object and update our post's body
      $new_post = array(
          'ID' => $post,
          'post_content' => $body
        );

      wp_update_post( $new_post );

    }

    // Delete the option so this doesn't repeat next time
    delete_option( 'lfc_posts_to_update' );

  }

  /**
   * LFC Get Widget Data For...
   *
   * For the provided unique sidebar, grab all the content for each
   * module widget
   * @param str $sidebar_name
   * @return array
   */
  function lfc_get_widget_data_for( $sidebar_name ) {
    global $wp_registered_sidebars, $wp_registered_widgets;
    // Holds the final data to return
    $output = array();
    
    // Loop over all of the registered sidebars looking for the one with the same name as $sidebar_name
    $sibebar_id = false;
    foreach( $wp_registered_sidebars as $sidebar ) {
      if( $sidebar['id'] == $sidebar_name ) {
        // We now have the Sidebar ID, we can stop our loop and continue.
        $sidebar_id = $sidebar['id'];
        break;
      }
    }
    
    if( !$sidebar_id ) {
      // There is no sidebar registered with the name provided.
      return $output;
    } 
    
    // A nested array in the format $sidebar_id => array( 'widget_id-1', 'widget_id-2' ... );
    $sidebars_widgets = wp_get_sidebars_widgets();
    $widget_ids = $sidebars_widgets[$sidebar_id];
    
    if( !$widget_ids ) {
      // Without proper widget_ids we can't continue. 
      return array();
    }
    
    // Loop over each widget_id so we can fetch the data out of the wp_options table.
    foreach( $widget_ids as $id ) {
      // The name of the option in the database is the name of the widget class.  
      $option_name = $wp_registered_widgets[$id]['callback'][0]->option_name;
      
      // Widget data is stored as an associative array. To get the right data we need to get the right key which is stored in $wp_registered_widgets
      $key = $wp_registered_widgets[$id]['params'][0]['number'];
      
      $widget_data = get_option($option_name);
      
      // Add the widget data on to the end of the output array.
      $output[] = (object) $widget_data[$key];
    }
    
    return $output;
  }

  /**
   * LFC Included Fields
   *
   * Provides an array of Module Widget fields to include
   * in the content backups
   * @return array
   */
  function lfc_included_fields(){

    // Set up an initial set of included fields for the body backup functions
    $included = array(
        'content',
        'callout_title',
        'callout_subtitle',
        'main_title',
        'subtitle',
        'heading_title',
        'heading_subtitle',
        'first_column',
        'second_column',
        'third_column'
      );

    // Allow users to append to this list
    $included = apply_filters( 'lfc_included_fields', $included );

    return $included;

  }

  /**
   * Get Class Instance
   * @return obj
   */
  public static function getInstance(){
    if(self::$instance === null){
      self::$instance = new LongformContent();
    }
    return self::$instance;
  }
}

?>
