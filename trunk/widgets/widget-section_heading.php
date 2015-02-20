<?php

/**
 * Adds Lfc_Section_Heading_Widget widget.
 */
class Lfc_Section_Heading_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_section_heading_widget', 'description' => __( 'A section heading with title and subtitle.', 'lfc-section-heading' ) );
        $control_ops = array( 'width' => 400, 'height' => 400 );
        parent::__construct( 'lfc-section-heading-module', __( 'Section Heading', 'lfc-section-heading' ), $widget_ops, $control_ops );
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

        if ( isset( $instance[ 'heading_title' ] ) ) {
            $heading_title = $instance[ 'heading_title' ];
        }
        else {
            $heading_title = '';
        }
        if ( isset( $instance[ 'heading_subtitle' ] ) ) {
            $heading_subtitle = $instance[ 'heading_subtitle' ];
        }
        else {
            $heading_subtitle = '';
        }

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/section_heading.php' );
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
        if ( isset( $instance[ 'heading_title' ] ) ) {
            $heading_title = $instance[ 'heading_title' ];
        }
        else {
            $heading_title = '';
        }
        if ( isset( $instance[ 'heading_subtitle' ] ) ) {
            $heading_subtitle = $instance[ 'heading_subtitle' ];
        }
        else {
            $heading_subtitle = '';
        }
        
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Section Heading Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Section Heading panel allows you to add a bold Title and optional Subtitle. This is useful for denoting new unique sections of your content.
            </div>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'heading_title' ); ?>"><?php _e( 'Main Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'heading_title' ); ?>" name="<?php echo $this->get_field_name( 'heading_title' ); ?>" type="text" value="<?php echo esc_attr( $heading_title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'heading_subtitle' ); ?>"><?php _e( 'Sub Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'heading_subtitle' ); ?>" name="<?php echo $this->get_field_name( 'heading_subtitle' ); ?>" type="text" value="<?php echo esc_attr( $heading_subtitle ); ?>">
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
        $instance['heading_title'] = ( ! empty( $new_instance['heading_title'] ) ) ? strip_tags( $new_instance['heading_title'] ) : '';
        $instance['heading_subtitle'] = ( ! empty( $new_instance['heading_subtitle'] ) ) ? strip_tags( $new_instance['heading_subtitle'] ) : '';

        return $instance;
    }

}