<?php

/**
 * Adds Lfc_Header_Widget widget.
 */
class Lfc_Header_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_header_widget', 'description' => __( 'An opening image with your story title and description.', 'lfc-header' ) );
        $control_ops = array( 'width' => 400, 'height' => 400 );
        parent::__construct( 'lfc-header-module', __( 'Header', 'lfc-header' ), $widget_ops, $control_ops );
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

        if( isset( $instance['image_id'] ) ){
            $image_id = $instance['image_id'];
            $image_obj = wp_get_attachment_image_src( $image_id, 'full' );
            $image = $image_obj[0];
        }else{
            $image = '';
        }

        if ( isset( $instance[ 'main_title' ] ) ) {
            $main_title = $instance[ 'main_title' ];
        }else{
            $main_title = '';
        }

        if ( isset( $instance[ 'subtitle' ] ) ) {
            $subtitle = $instance[ 'subtitle' ];
        }else{
            $subtitle = '';
        }

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/header.php' );
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
        if ( isset( $instance[ 'main_title' ] ) ) {
            $main_title = $instance[ 'main_title' ];
        }else{
            $main_title = '';
        }
        if ( isset( $instance[ 'subtitle' ] ) ) {
            $subtitle = $instance[ 'subtitle' ];
        }else{
            $subtitle = '';
        }
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Header Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Header panel allows you to add a full-width header image, a Main Heading title, and a subtitle.
            </div>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <?php lfc_image_field( $this, $instance ); ?>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'main_title' ); ?>"><?php _e( 'Main Heading:' ); ?></label> 
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'main_title' ); ?>" name="<?php echo $this->get_field_name( 'main_title' ); ?>" rows="5"><?php echo esc_attr( $main_title ); ?></textarea>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'subtitle' ); ?>"><?php _e( 'Subtitle:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>">
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
        $instance['main_title'] = ( ! empty( $new_instance['main_title'] ) ) ? strip_tags( $new_instance['main_title'] ) : '';
        $instance['subtitle'] = ( ! empty( $new_instance['subtitle'] ) ) ? strip_tags( $new_instance['subtitle'] ) : '';
        return $instance;
    }

}