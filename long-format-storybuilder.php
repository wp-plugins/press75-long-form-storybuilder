<?php
/*
Plugin Name: Long Form Storybuilder
Plugin URI: #
Description: A Plugin to allow easy creation of Long Form Content
Version: 0.1.3
Author: Aaron Speer, Press75
Author URI: http://press75.com
Package: lfc
*/

$dir = dirname(__FILE__);
@include_once "$dir/inc/scripts.php";
@include_once "$dir/inc/init.php";
@include_once "$dir/inc/widgets.php";
@include_once "$dir/inc/image-field.php";
@include_once "$dir/inc/templates.php";
@include_once "$dir/inc/options.php";

/**
 * LFC Init
 *
 * Initializes the plugin files
 */
function lfc_init() {
  global $lfc_instance;
  $lfc_instance = LongformContent::getInstance();
}

/**
 * LFC Activation
 *
 * Hooks to run on plugin activation
 */
function lfc_activation() {
  // activation functions
}

/**
 * LFC Deactivation
 *
 * Hooks to run on plugin deactivation
 */
function lfc_deactivation() {
  // deactivation functions
}

/**
 * LFC Widgets Init
 *
 * Registers unique sidebars for each page or post set as an LFC Page
 */
function lfc_widgets_init() {
    $args = array( 'post_type' => 'any', 'post_status' => 'any', 'posts_per_page' => -1 );
    $args['meta_query'] = array(
            'relation' => 'OR',
            array( 'key' => '_wp_page_template', 'value' => 'lfc', 'compare' => 'like' ),
            array( 'key' => 'is_lfc', 'value' => true, 'compare' => '=' )
        );
    $posts = get_posts( $args );
    foreach( $posts as $post ){
        $id = 'modules-' . $post->ID;
        $name = $post->post_title; 
        register_sidebar( array(
            'name' => $name,
            'id' => $id,
            'class' => 'lfc_section',
            'description' => __( 'Arrange your page sections here.', 'theme-slug' ),
            'before_title' => '<h1>',
            'after_title' => '</h1>',
        ) );
    }
}
add_action( 'widgets_init', 'lfc_widgets_init' );

/**
 * LFC Hide Widgets
 *
 * Hides LFC Module widgets from the Widget dashboard page
 */
function lfc_hide_widgets(){
    global $wp_customize;
    if ( isset( $wp_customize ) ) {
        return;
    }

    if( !is_admin() ) {
        return;
    }

    global $wp_registered_widgets;
    foreach( $wp_registered_widgets as $key=>$value ){
        if( strpos( $key, 'lfc' ) !== false ){
            unset( $wp_registered_widgets[$key] );
        }
    }
   
}
add_action( 'widgets_init', 'lfc_hide_widgets', 999 );

/**
 * LFC Sidebar Hide
 *
 * Hides LFC Sidebar areas from the Widget Dashboard page
 */
function lfc_sidebar_hide($sidebar) {
    
    global $wp_customize;
    if ( isset( $wp_customize ) ) {
        return $sidebar;
    }

    if( !is_admin() ) {
        return $sidebar;
    }

    global $wp_registered_sidebars;
    if( $sidebar['class'] == 'lfc_section' ){
        unset( $wp_registered_sidebars[$sidebar['id']] );
    }

    return $sidebar;
}
add_action( 'register_sidebar', 'lfc_sidebar_hide' );

/**
 * Save LFC Widgets
 *
 * Saves an array of LFC widgets for each page when theme is switched
 */
function save_lfc_widgets(){
    $widgets = get_option('sidebars_widgets', true);

    $saved = array();

    foreach( $widgets as $key=>$value ){
        if( strpos( $key, 'modules' ) !== false ){
            $saved[$key] = $value;
        }
    }

    update_option( 'lfc-saved-widgets', $saved );
}
add_action('switch_theme', 'save_lfc_widgets');

/**
 * Restore LFC Widgets
 *
 * Uses previously saved array of LFC widgets to restore them
 * after theme is switched
 */
function restore_lfc_widgets() {
    $saved = get_option( 'lfc-saved-widgets', true );
    $widgets = get_option('sidebars_widgets', true);

    foreach( $saved as $key=>$value ){
        $widgets[$key] = $value;
    }

    update_option( 'sidebars_widgets', $widgets, true);
}

add_action('after_switch_theme', 'restore_lfc_widgets', 999);

/**
 * Restore LFC Page Options
 *
 * Restores any LFC-related Page Options that were enabled for the 
 * previous theme on theme switch
 */
function restore_lfc_page_options( $old_name, $old_theme ) {

    $old_slug = $old_theme->get_stylesheet(); 

    update_option( 'lfc_old_slug', $old_slug );
    $new_slug = get_option( 'template', true );

    $old_mods = get_option( "theme_mods_$old_slug" ); 
    $new_mods = get_option( "theme_mods_$new_slug" ); 

    foreach( $old_mods as $key=>$value ){
        if( strpos( $key, 'lfc_' ) !== false ){
            $new_mods[$key] = $value;
        }
    }

    update_option( "theme_mods_$new_slug", $new_mods ); 
}
add_action('after_switch_theme', 'restore_lfc_page_options', 999, 2);

/**
 * Get Brightness
 *
 * Gets the brightness of a color based on the hex value
 * @param str $hex
 * @return int
 */
function get_brightness($hex) {
    
    $hex = str_replace('#', '', $hex);

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

/**
 * Adjust Color
 *
 * Makes a color brighter or darker
 * @param str $color_code
 * @return str
 */
function adjustColor( $color_code, $percentage_adjuster = 0 ) {
    
    $percentage_adjuster = round( $percentage_adjuster/100, 2 );
  
    $hex = str_replace( "#", "", $color_code );
    $r = ( strlen( $hex ) == 3 ) ? hexdec( substr( $hex, 0, 1 ).substr( $hex, 0, 1 ) ) : hexdec( substr( $hex, 0, 2 ) );
    $g = ( strlen( $hex ) == 3 ) ? hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1 ) ) : hexdec( substr( $hex, 2, 2 ) );
    $b = ( strlen( $hex ) == 3 ) ? hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1 ) ) : hexdec( substr( $hex, 4, 2 ) );
    $r = round( $r - ( $r*$percentage_adjuster ) );
    $g = round( $g - ( $g*$percentage_adjuster ) );
    $b = round( $b - ( $b*$percentage_adjuster ) );

    return "#".str_pad( dechex( max( 0, min( 255, $r ) ) ), 2, "0", STR_PAD_LEFT )
        .str_pad( dechex( max( 0, min( 255, $g ) ) ), 2, "0", STR_PAD_LEFT )
        .str_pad( dechex( max( 0, min( 255, $b ) ) ), 2, "0", STR_PAD_LEFT );
 
}

/**
 * LFC Activate Template
 *
 * Changes page template to the LFC template if option is enabled
 * @param str $template
 * @return str
 */
function lfc_activate_template( $template ){
    global $post;
    $is_lfc = get_post_meta( $post->ID, 'is_lfc', true );

    if( $is_lfc ){
        $new_template =  dirname(__FILE__) . '/templates/template-lfc.php';
        return $new_template;
    }

    return $template;
}
add_filter( 'single_template', 'lfc_activate_template' );
add_filter( 'page_template', 'lfc_activate_template' );

// Add initialization and activation hooks
add_action('plugins_loaded', 'lfc_init');
register_activation_hook("$dir/lfc.php", 'lfc_activation');
register_deactivation_hook("$dir/lfc.php", 'lfc_deactivation');
  

?>
