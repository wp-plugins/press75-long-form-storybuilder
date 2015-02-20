<?php

/**
 * Adds Lfc_Callout_Widget widget.
 */
class Lfc_Callout_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_callout_widget', 'description' => __( 'A full-width callout section with a colored background.', 'lfc-callout' ) );
        $control_ops = array( 'width' => 400, 'height' => 400 );
        parent::__construct( 'lfc-callout-module', __( 'Callout', 'lfc-callout' ), $widget_ops, $control_ops );
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

        if( isset( $instance['callout_title'] ) ){
            $callout_title = $instance['callout_title'];
        }else{
            $callout_title = '';
        }

        if( isset( $instance['callout_subtitle'] ) ){
            $callout_subtitle = $instance['callout_subtitle'];
        }else{
            $callout_subtitle = '';
        }

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/callout.php' );
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
        
        if ( isset( $instance[ 'callout_title' ] ) ) {
            $callout_title = $instance[ 'callout_title' ];
        }
        else {
            $callout_title = '';
        }
        if( isset( $instance['callout_subtitle'] ) ){
            $callout_subtitle = $instance['callout_subtitle'];
        }else{
            $callout_subtitle = '';
        }
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Callout Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Callout panel allows you to add a full-width colored bar (using your Primary Color) with a Main Title and Subtitle. 
            </div>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'callout_title' ); ?>"><?php _e( 'Main Title:' ); ?></label> 
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'callout_title' ); ?>" name="<?php echo $this->get_field_name( 'callout_title' ); ?>"><?php echo esc_attr( $callout_title ); ?></textarea>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'callout_subtitle' ); ?>"><?php _e( 'Subtitle:' ); ?></label> 
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'callout_subtitle' ); ?>" name="<?php echo $this->get_field_name( 'callout_subtitle' ); ?>"><?php echo esc_attr( $callout_subtitle ); ?></textarea>
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
        $instance['callout_title'] = ( ! empty( $new_instance['callout_title'] ) ) ? $new_instance['callout_title'] : '';
        $instance['callout_subtitle'] = ( ! empty( $new_instance['callout_subtitle'] ) ) ? $new_instance['callout_subtitle'] : '';
        
        return $instance;
    }

}