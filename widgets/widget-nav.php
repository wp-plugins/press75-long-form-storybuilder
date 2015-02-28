<?php

/**
 * Adds Lfc_Nav_Widget widget.
 */
class Lfc_Nav_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_nav_widget', 'description' => __( 'A navigation bar with specified pages and a logo.', 'lfc-nav' ) );
        $control_ops = array( 'width' => 400, 'height' => 400 );
        parent::__construct( 'lfc-nav-module', __( 'Navigation', 'lfc-nav' ), $widget_ops, $control_ops );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        if( isset( $instance['title'] ) ){
            $title = apply_filters( 'widget_title', $instance['title'] );
        }else{
            $title = '';
        }

        if( isset( $instance['menu_type'] ) ){
            $menu_type = $instance['menu_type'];
        }else{
            $menu_type = '';
        }

        if( isset( $instance['wp_menu'] ) ){
            $wp_menu = $instance['wp_menu'];
        }else{
            $wp_menu = '';
        }

        if ( isset( $instance[ 'pages_json' ] ) ) {
            $pages_json = $instance[ 'pages_json' ];
            $current_pages = json_decode( $pages_json );
        }
        else {
            $pages_json = '';
        }

        if( isset( $instance['image_id'] ) ){
            $image_id = $instance['image_id'];
            $image_obj = wp_get_attachment_image_src( $image_id, 'medium' );
            $image = $image_obj[0];
        }else{
            $image = '';
        }

        $site_url = home_url();

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/nav.php' );
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = '';
        }

        if ( isset( $instance[ 'menu_type' ] ) ) {
            $menu_type = $instance[ 'menu_type' ];
        }
        else {
            $menu_type = '';
        }

        if ( isset( $instance[ 'wp_menu' ] ) ) {
            $wp_menu = $instance[ 'wp_menu' ];
        }
        else {
            $wp_menu = '';
        }

        if ( isset( $instance[ 'pages_json' ] ) ) {
            $pages_json = $instance[ 'pages_json' ];
            $current_pages = json_decode( $pages_json );
        }
        else {
            $pages_json = '';
        }

        $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

        foreach ( $menus as $menu ){
            $reg_menus[$menu->slug] = $menu->name;
        }

        // Get all the current pages
        $args = array( 'post_type' => 'page', 'posts_per_page' => -1 );
        $pages = get_posts( $args );
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Navigation Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Navigation panel allows you to add a floating navigation bar to your content. You may use an existing menu, or create a unique menu for this post.
            </div>
        </div>

        <div class="lfc-settings-section">
            <?php lfc_image_field( $this, $instance ); ?>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'menu_type' ); ?>"><?php _e( 'Menu Type:' ); ?></label> 
            <select class="widefat lfc-menu-toggle" id="<?php echo $this->get_field_id( 'menu_type' ); ?>" name="<?php echo $this->get_field_name( 'menu_type' ); ?>">
                <option value="wp_menu" <?php if( $menu_type == 'wp_menu' ){ echo "selected"; } ?>>Existing WordPress Menu</option>
                <option value="custom_menu" <?php if( $menu_type == 'custom_menu' ){ echo "selected"; } ?>>Custom Menu</option>
            </select>
        </div>

        <div class="lfc-settings-section lfc_show_wp">
            <label for="<?php echo $this->get_field_id( 'wp_menu' ); ?>"><?php _e( 'Existing Menu:' ); ?></label> 
            <select class="widefat lfc-menu-type" id="<?php echo $this->get_field_id( 'wp_menu' ); ?>" name="<?php echo $this->get_field_name( 'wp_menu' ); ?>">
                <?php foreach( $reg_menus as $key=>$value ): ?>
                    <option value="<?php echo $key; ?>" <?php if( $key == $wp_menu ){ echo "selected"; } ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php /* ?><label>Logo</label>
        <?php lfc_image_field( $this, $instance ); ?><?php */ ?>

        <div class="lfc-current-pages lfc-settings-section lfc_show_custom">
            <label>Current Menu Items</label>
            <ul class="lfc-sortable-items lfc-current-pages-list">
                <?php if( isset( $current_pages ) && $current_pages ){
                    foreach( $current_pages as $page ){
                        echo "<li data-title='$page->title' data-url='$page->url'><span class='lfc-delete-page'>x</span>$page->title</li>";
                    }
                } ?>
            </ul>
        </div>

        <input class="lfc-all-pages" type="hidden" id="<?php echo $this->get_field_id('pages_json'); ?>" value='<?php echo $pages_json; ?>' name="<?php echo $this->get_field_name('pages_json'); ?>" />
        <div class="lfc-accordion-container lfc-settings-section lfc_show_custom">
            <ul class="outer-border">
                <li class="control-section accordion-section">
                    <h3 class="accordion-section-title hndle" tabindex="0">Page Section</h3>
                    <div class="accordion-section-content">
                        <div class="inside">
                            <div class="posttypediv">
                                <select class="lfc-page-section-wrapper form-no-clear">
                                    
                                </select>
                                <p class="button-controls">
                                    <span class="add-to-menu">
                                        <button class="button-secondary submit-add-to-menu right lfc-add-section">Add to Menu</button>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="control-section accordion-section open">
                    <h3 class="accordion-section-title hndle" tabindex="0">Pages</h3>
                    <div class="accordion-section-content">
                        <div class="inside">
                            <div class="posttypediv">
                                <ul class="categorychecklist form-no-clear">
                                    <?php foreach( $pages as $page ): ?>
                                        <li>
                                            <label class="menu-item-title">
                                                <input type="checkbox" class="menu-item-checkbox" data-title="<?php echo $page->post_title; ?>" value="<?php echo get_permalink( $page->ID ); ?>">
                                                <?php echo $page->post_title; ?>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <p class="button-controls">
                                    <span class="add-to-menu">
                                        <button class="button-secondary submit-add-to-menu right lfc-add-pages" value="Add to Menu" id="">Add to Menu</button>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="control-section accordion-section">
                    <h3 class="accordion-section-title hndle" tabindex="0">Custom Link</h3>
                    <div class="accordion-section-content">
                        <div class="inside">
                            <div class="posttypediv">
                                <ul class="lfc-new-link-wrapper form-no-clear">
                                    <li>
                                        <label class="menu-item-title">
                                            Page Link
                                            <input type="text" class="new_custom_link">
                                        </label>
                                        <label class="menu-item-title">
                                            Page Title
                                            <input type="text" class="new_custom_title">
                                        </label>
                                    </li>
                                </ul>
                                <p class="button-controls">
                                    <span class="add-to-menu">
                                        <button class="button-secondary submit-add-to-menu right lfc-add-custom">Add to Menu</button>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <?php 
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['image_id'] = ( ! empty( $new_instance['image_id'] ) ) ? $new_instance['image_id'] : '';
        $instance['menu_type'] = ( ! empty( $new_instance['menu_type'] ) ) ? $new_instance['menu_type'] : '';
        $instance['wp_menu'] = ( ! empty( $new_instance['wp_menu'] ) ) ? $new_instance['wp_menu'] : '';
        $instance['pages_json'] = (  ! empty( $new_instance['pages_json'] ) ) ? $new_instance['pages_json'] : '';
        return $instance;
    }

}