<?php 
/**
 * LFC Widgets Class
 *
 * Enqueues our LFC module widgets so that they are available within
 * the Customizer
 *
 * @package lfc
 */
class LFC_Widgets {
  
    private static $instance;

    function __construct() {

        $widget_dir = plugin_dir_path( __DIR__ ) . 'widgets/';

        $widget_list = array(
                'callout',
                'content',
                'full_feature',
                'header',
                'three_column_content',
                'two_column_content',
                'nav',
                'section_heading'
            );

        foreach( $widget_list as $widget ){
            include_once( $widget_dir . 'widget-' . $widget . '.php' );
            add_action( 'widgets_init', array( $this, 'register_lfc_' . $widget . '_widget' ) );
        }

    }

    function register_lfc_callout_widget() {
        register_widget( 'Lfc_Callout_Widget' );
    }

    function register_lfc_content_widget() {
        register_widget( 'Lfc_Content_Widget' );
    }

    function register_lfc_full_feature_widget() {
        register_widget( 'Lfc_Full_Feature_Widget' );
    }

    function register_lfc_header_widget() {
        register_widget( 'Lfc_Header_Widget' );
    }

    function register_lfc_image_carousel_widget() {
        register_widget( 'Lfc_Image_Carousel_Widget' );
    }

    function register_lfc_three_column_content_widget() {
        register_widget( 'Lfc_Three_Column_Content_Widget' );
    }

    function register_lfc_two_column_content_widget() {
        register_widget( 'Lfc_Two_Column_Content_Widget' );
    }

    function register_lfc_nav_widget() {
        register_widget( 'Lfc_Nav_Widget' );
    }

    function register_lfc_section_heading_widget() {
        register_widget( 'Lfc_Section_Heading_Widget' );
    }

    /**
    * Get Class Instance
    * @return obj
    */
    public static function getInstance(){
        if(self::$instance === null){
          self::$instance = new LFC_Widgets();
        }
        return self::$instance;
    }
}