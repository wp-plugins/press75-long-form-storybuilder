<?php

/**
 * LFC Scripts Class
 *
 * Enqueues scripts for front-end and back-end usage
 *
 * @package lfc
 */
class LFC_Scripts {

  private static $instance;

  function __construct() {
    add_action( 'wp_head', array( $this, 'lfc_ajaxurl' ));
    add_action( 'wp_enqueue_scripts', array( $this, 'lfc_scripts' ));
    add_action( 'customize_preview_init', array( $this, 'lfc_customizer_live_preview' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'lfc_enqueue_admin_scripts' ) );
    add_action( 'admin_print_footer_scripts', array( $this, 'lfc_footer_scripts' ) );
  }

  /**
   * LFC Ajax URL
   *
   * Adds JS to define the AJAX URL for front-end scripts
   */
  function lfc_ajaxurl() {
    $page_id = get_queried_object_id();
    ?>
      <script>
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
        var views_url = '<?php echo plugin_dir_url( __DIR__ ); ?>views/';
        var page_id = <?php echo $page_id; ?>
      </script>
    <?php
  }

  /**
   * LFC Scripts
   *
   * Enqueues font-end scripts
   */
  function lfc_scripts() {

    // Scripts
    global $post;
    $template_name = get_post_meta( $post->ID, '_wp_page_template', true );
    $is_lfc = get_post_meta( $post->ID, 'is_lfc', true );

    if( strpos( $template_name, 'template-lfc' ) !== false || $is_lfc ) {
      
      wp_enqueue_script( array( 'jquery', 'editor', 'thickbox', 'media-upload' ) );
      wp_enqueue_script( 'lfc_headroom', '//cdn.jsdelivr.net/headroomjs/0.5.0/headroom.min.js' );
      wp_enqueue_script( 'lfc_module_js', plugin_dir_url( __DIR__ ) . 'assets/js/modules.js', array( 'jquery' ) );
      wp_enqueue_script( 'lfc_chosen', plugin_dir_url( __DIR__ ) . 'assets/js/plugins/chosen.jquery.min.js', array( 'jquery' ) );
      wp_enqueue_script( 'lfc_slick', plugin_dir_url( __DIR__ ) . 'assets/js/plugins/slick.min.js', array( 'jquery' ));

      wp_enqueue_style( 'lfc_module_style', plugin_dir_url( __DIR__ ) . 'assets/css/module_styles.css', array());
      wp_enqueue_style( 'lfc_style', plugin_dir_url( __DIR__ ) . 'assets/css/lfc.css' );
      wp_enqueue_style( 'lora', 'http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' );
      wp_enqueue_style( 'lato', 'http://fonts.googleapis.com/css?family=Lato:100,300,400,700,400italic' );
      wp_enqueue_style( 'lato', 'http://fonts.googleapis.com/css?family=Open+Sans:400,700' );
      wp_enqueue_media();
    }
  }

  /**
   * LFC Customizer Live Preview
   *
   * Enqueues scripts for interacting between the customizer controls and live preview panes
   */
  function lfc_customizer_live_preview() {
    wp_enqueue_script( 'lfc-customizer', plugin_dir_url( __DIR__ ) . 'assets/js/customizer.js', array( 'jquery' ) );
  }  

  /**
   * LFC Enqueue Admin Scripts
   *
   * Enqueues Scripts for Back-End use
   */
  function lfc_enqueue_admin_scripts() {
    global $wp_customize;
   
    if( isset( $wp_customize ) ) {
      wp_enqueue_media();
      do_action( 'wp_enqueue_editor', array( 'tinymce' => true ) ); // Advanced Image Styles compatibility
      wp_register_script( 'lfc-admin-script', plugin_dir_url( __DIR__ ) . 'assets/js/admin.js', array( 'jquery' ) );
      $local_paths = array( 'plugin_dir' => plugin_dir_url( __DIR__ ), 'js_dir' => plugin_dir_url( __DIR__ ) . 'assets/js/' );
      wp_localize_script( 'lfc-admin-script', 'paths', $local_paths );
      wp_enqueue_script( 'lfc-admin-script' );
      wp_enqueue_style( 'lfc_admin_style', plugin_dir_url( __DIR__ ) . 'assets/css/admin.css' );
      wp_enqueue_script( 'heartbeat' );
    }
  }

  /**
   * LFC Footer Scripts
   *
   * Adds the Tiny MCE editor instance to the footer
   */
  function lfc_footer_scripts() {
    global $wp_customize;
    if( isset( $wp_customize ) ) {
      wp_editor( '', 'lfc-tinymce-widget' );
    }
  }

  /**
   * Get Class Instance
   *
   * @return obj
   */
  public static function getInstance() {
    if( self::$instance === null ) {
      self::$instance = new LFC_Scripts();
    }
    return self::$instance;
  }
}

?>
