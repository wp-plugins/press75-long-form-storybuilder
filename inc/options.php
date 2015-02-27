<?php

/**
 * LFC Options Class
 *
 * Adds various page options and Customizer Panels
 *
 * @package lfc
 */
class LFC_Options {

  private static $instance;

  function __construct() {
    add_action( 'add_meta_boxes', array( $this, 'lfc_metaboxes' ) );
    add_action( 'customize_register', array( $this, 'lfc_add_customizer_section') );
    add_action( 'save_post', array( $this, 'save' ) );
    $this->add_image_sizes();
  }

  /**
   * LFC MetaBoxes
   *
   * Adds metabox for setting a page as Long Format
   */
  function lfc_metaboxes() {
    $screens = get_post_types();

    $unset = array( 'revision', 'attachment', 'nav_menu_item' );
    // Allow users to define other post types to exclude
    $unset = apply_filters( 'lfc_excluded_post_types', $unset );

    foreach( $unset as $type ){
      unset( $screens[$type] );
    }
    
    foreach ( $screens as $screen ) {
      global $post;
      $template = get_post_meta( $post->ID, '_wp_page_template', TRUE );
      $is_lfc = get_post_meta( $post->ID, 'is_lfc', true );

      if( strpos( $template, 'template-lfc' ) !== false || $is_lfc ){

        add_meta_box(
          'lfc_description',
          __( 'Long Format Content Enabled', 'lfc_textdomain' ),
          array( $this, 'lfc_description_metabox_display' ),
          $screen, 
          'normal', 
          'high'
          );

        remove_post_type_support( $screen, 'editor' );

      }

      add_meta_box( 'lfc_toggle', 'Enable Long Form Content', array( $this, 'lfc_toggle_metabox_display' ), $screen, 'side', 'high' );

    }

  }

  /**
   * LFC Toggle Metabox Display
   *
   * Displays the checkbox for setting a page as LFC
   * @param obj $post
   */
  function lfc_toggle_metabox_display( $post ) {
    wp_nonce_field( 'lfc_toggle_metabox', 'lfc_toggle_metabox_nonce' );
    $is_lfc = get_post_meta( $post->ID, 'is_lfc', true );
    
    ?>
    <label for="is_lfc"><input type="checkbox" name="is_lfc" <?php if( $is_lfc ){ ?>checked="checked" <?php } ?> >Display as Long Form Content</label>
    <?php
  }


  /**
   * LFC Description Metabox Display
   *
   * Prints the metabox content for LFC pages
   * @param WP_Post $post
   */
  function lfc_description_metabox_display( $post ) {

    echo "<p>You've chosen to make this page a Long Format Content page. In order to edit this page, please navigate to this page in the Customizer<br />( Appearance -> Customize ) or use the link below.</p>";
    echo '<div><a href="' . site_url( 'wp-admin/customize.php?url=' ) . get_permalink( $post->ID ) . '" class="button button-primary button-large" target="_blank">Edit my Long Format Content Page</a></div>';
    
  }

  /**
   * Save
   *
   * Save is_lfc data on post Save
   * @param int $post_id
   */
  public function save( $post_id ) {

    // Check if our nonce is set.

    if ( ! isset( $_POST['lfc_toggle_metabox_nonce'] ) )
      return $post_id;
    $nonce = $_POST['lfc_toggle_metabox_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'lfc_toggle_metabox' ) )
      return $post_id;

    // If this is an autosave, our form has not been submitted,
          //     so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

    if( $_POST['is_lfc'] == 'on' ){
      $is_lfc = true;
    }else{
      $is_lfc = false;
    }
    // Update the meta field.
    update_post_meta( $post_id, 'is_lfc', $is_lfc );
  }

  /**
   * Add Image Sizes
   *
   * Adds a custom image size
   */
  function add_image_sizes(){
    add_image_size( 'content-image', 475, 0, false );
  }

  /**
   * LFC Add Customizer Section
   *
   * For each LFC page/post, add a Page Options customizer section
   * @param WP_Customize $wp_customize
   */
  function lfc_add_customizer_section( $wp_customize ){

    $args = array( 'post_type' => 'any', 'post_status' => 'any', 'posts_per_page' => -1 );
    $args['meta_query'] = array(
      'relation' => 'OR',
      array( 'key' => '_wp_page_template', 'value' => 'lfc', 'compare' => 'like' ),
      array( 'key' => 'is_lfc', 'value' => true, 'compare' => '=' )
      );
    $posts = get_posts( $args );
    foreach( $posts as $post ){

      // Add Page Options Section
      $wp_customize->add_section( 'page_options_' . $post->ID,
        array(
          'title'    => 'Page Options',
          'priority' => 110,
          ) 
        );

      // Add setting for Primary Color
      $wp_customize->add_setting( 'lfc_colors' . $post->ID . '[primary_color]',
        array(
          'default' => '#000000',
          )
        );

      $wp_customize->add_control(
        new WP_Customize_Color_Control(
          $wp_customize,
          'lfc_colors' . $post->ID . '[primary_color]',
          array(
            'label' => 'Primary Color',
            'section' => 'page_options_' . $post->ID,
            'settings' => 'lfc_colors' . $post->ID . '[primary_color]',
            )
          )
        ); 

      // Setting for Secondary color
      $wp_customize->add_setting( 'lfc_colors' . $post->ID . '[secondary_color]',
        array(
          'default' => '#000000',
          )
        );

      $wp_customize->add_control(
        new WP_Customize_Color_Control(
          $wp_customize,
          'lfc_colors' . $post->ID . '[secondary_color]',
          array(
            'label' => 'Secondary Color',
            'section' => 'page_options_' . $post->ID,
            'settings' => 'lfc_colors' . $post->ID . '[secondary_color]',
            )
          )
        ); 

      // Setting for Font Combinations
      $wp_customize->add_setting( 'lfc_fonts' . $post->ID,
        array(
          'default' => 'playfair_open_sans',
          )
        );

      $font_options = LFC_OPTIONS::getFontOptions();
      $choices = array();
      foreach( $font_options as $key=>$value ) {
        $choices[$key] = $value['label'];
      }

      $wp_customize->add_control(
          'lfc_fonts' . $post->ID,
          array(
            'type' => 'select',
            'label' => 'Font Combination',
            'section' => 'page_options_' . $post->ID,
            'choices' => $choices
            )
        ); 
    }

  }

  /**
   * Get Page Options
   *
   * Grab the LFC page options array for the given page
   * @param int $page_id
   * @return array
   */
  public static function getPageOptions( $page_id ){

    // Get colors
    $colors = get_theme_mod( 'lfc_colors'.$page_id );
    $primary_color =  $colors['primary_color'];
    $secondary_color =  $colors['secondary_color'];

    // Text color based on primary color
    if ( get_brightness( $primary_color ) > 130){
      $text_color = adjustColor( $primary_color, 75 );
    }else{
      $text_color = '#ffffff';
    }

    // Hover Color based on secondary color
    if ( get_brightness( $secondary_color ) > 130){
      $hover_color = adjustColor( $secondary_color, 75 );
    }elseif( get_brightness( $secondary_color ) < 50 ) {
      $hover_color = adjustColor( $secondary_color, -150 );
    }else{
      $hover_color = adjustColor( $secondary_color, -75 );
    }

    // Get Font combos
    $fonts = get_theme_mod( 'lfc_fonts'.$page_id );

    return array( 'fonts' => $fonts, 'primary_color' => $primary_color, 'secondary_color' => $secondary_color, 'text_color' => $text_color, 'hover_color' => $hover_color );

  }

  /**
   * Get Font Options
   *
   * Get array of available font options for the LFC Customizer
   * @return array
   */
  public static function getFontOptions(){

    $font_options = array(
      'playfair_open_sans' => array(
        'label' => 'Playfair / Open Sans', 
        'title_import' => 'Playfair+Display:400,900',
        'title_family' => "'Playfair Display', serif",
        'title_weight' => '900',
        'body_import' => 'Open+Sans:400italic,400,300,700',
        'body_family' => "'Open Sans', sans-serif"
      ),
      'montserrat_neuton' => array( 
        'label' => 'Montserrat / Neuton', 
        'title_import' => 'Montserrat:400,700',
        'title_weight' => '700',
        'title_family' => "'Montserrat', sans-serif",
        'body_import' => 'Neuton:300,400,700,400italic',
        'body_family' => "'Neuton', serif"
      ),
      'roboto' => array( 
        'label' => 'Roboto Slab / Roboto', 
        'title_import' => 'Roboto+Slab:400,700',
        'title_weight' => '700',
        'title_family' => "'Roboto Slab', serif",
        'body_import' => 'Roboto:500,400italic,300,700,400',
        'body_family' => "'Roboto', sans-serif"
      ),
      'oswald_quattrocento' => array( 
        'label' => 'Oswald / Quattrocento', 
        'title_import' => 'Oswald:400,700',
        'title_weight' => '700',
        'title_family' => "'Oswald', sans-serif",
        'body_import' => 'Quattrocento:400,700',
        'body_family' => "'Quattrocento', serif"
      ),
      'megrim_roboto_slab' => array( 
        'label' => 'Megrim / Roboto Slab', 
        'title_import' => 'Megrim',
        'title_family' => "'Megrim', sans-serif",
        'title_weight' => 'normal',
        'body_import' => 'Roboto+Slab:400,700',
        'body_family' => "'Roboto Slab', serif"
      )
    );

    return apply_filters( 'lfc_font_options', $font_options );

  }
  
  /**
   * Get Instance
   *
   * @return LFC_Options
   */
  public static function getInstance(){
    if(self::$instance === null){
      self::$instance = new LFC_Options();
    }
    return self::$instance;
  }
}

?>
