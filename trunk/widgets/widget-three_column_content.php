<?php

/**
 * Adds Lfc_Three_Column_Content_Widget widget.
 */
class Lfc_Three_Column_Content_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_three_column_content_widget', 'description' => __( 'A three-column text area.', 'lfc-three-column-content' ) );
        $control_ops = array( 'width' => 500, 'height' => 400 );
        parent::__construct( 'lfc-content-three-column-module', __( 'Three Column Content', 'lfc-content-three-column' ), $widget_ops, $control_ops );
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

        if ( isset( $instance[ 'first_column' ] ) ) {
            $first_column = $instance[ 'first_column' ];
        }
        else {
            $first_column = '';
        }

        if ( isset( $instance[ 'second_column' ] ) ) {
            $second_column = $instance[ 'second_column' ];
        }
        else {
            $second_column = '';
        }

        if ( isset( $instance[ 'third_column' ] ) ) {
            $third_column = $instance[ 'third_column' ];
        }
        else {
            $third_column = '';
        }

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/three_column_content.php' );
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
         if( isset( $instance['title'] ) ){
            $title = apply_filters( 'widget_title', $instance['title'] );
        }else{
            $title = '';
        }

        if ( isset( $instance[ 'first_column' ] ) ) {
            $first_column = $instance[ 'first_column' ];
        }
        else {
            $first_column = '';
        }

        if ( isset( $instance[ 'second_column' ] ) ) {
            $second_column = $instance[ 'second_column' ];
        }
        else {
            $second_column = '';
        }

        if ( isset( $instance[ 'third_column' ] ) ) {
            $third_column = $instance[ 'third_column' ];
        }
        else {
            $third_column = '';
        }
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Three Column Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Three Column content panel allows you to add three evenly-sized columns of content, using a Visual editor to add text and images. 
            </div>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <div class="lfc-editor-container">
                <label for="<?php echo $this->get_field_id( 'first_column' ); ?>"><?php _e( 'First Column:' ); ?></label> 
                <div id="wp-content-media-buttons" class="wp-media-buttons">
                    <a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="first_column" title="Add Media">
                        <span class="wp-media-buttons-icon"></span> Add Media
                    </a>
                </div>
                <div class="wp-editor-tabs tmce-active">
                    <a href="#" class="html-button wp-switch-editor switch-html" >HTML</a>
                    <a href="#" class="visual-button wp-switch-editor switch-tmce">Visual</a>
                </div>
                <textarea class="widefat lfc-visual-editor" id="<?php echo $this->get_field_id( 'first_column' ); ?>" name="<?php echo $this->get_field_name( 'first_column' ); ?>"><?php echo esc_attr( $first_column ); ?></textarea>
            </div>
        </div>

        <div class="lfc-settings-section">
            <div class="lfc-editor-container">
                <label for="<?php echo $this->get_field_id( 'second_column' ); ?>"><?php _e( 'Second Column:' ); ?></label> 
                <div id="wp-content-media-buttons" class="wp-media-buttons">
                    <a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="second_column" title="Add Media">
                        <span class="wp-media-buttons-icon"></span> Add Media
                    </a>
                </div>
                <div class="wp-editor-tabs tmce-active">
                    <a href="#" class="html-button wp-switch-editor switch-html" >HTML</a>
                    <a href="#" class="visual-button wp-switch-editor switch-tmce">Visual</a>
                </div>
                <textarea class="widefat lfc-visual-editor" id="<?php echo $this->get_field_id( 'second_column' ); ?>" name="<?php echo $this->get_field_name( 'second_column' ); ?>"><?php echo esc_attr( $second_column ); ?></textarea>
            </div>
        </div>

        <div class="lfc-settings-section">
            <div class="lfc-editor-container">
                <label for="<?php echo $this->get_field_id( 'third_column' ); ?>"><?php _e( 'Third Column:' ); ?></label> 
                <div id="wp-content-media-buttons" class="wp-media-buttons">
                    <a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="third_column" title="Add Media">
                        <span class="wp-media-buttons-icon"></span> Add Media
                    </a>
                </div>
                <div class="wp-editor-tabs tmce-active">
                    <a href="#" class="html-button wp-switch-editor switch-html" >HTML</a>
                    <a href="#" class="visual-button wp-switch-editor switch-tmce">Visual</a>
                </div>
                <textarea class="widefat lfc-visual-editor" id="<?php echo $this->get_field_id( 'third_column' ); ?>" name="<?php echo $this->get_field_name( 'third_column' ); ?>"><?php echo esc_attr( $third_column ); ?></textarea>
            </div>
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
        $instance['first_column'] = ( ! empty( $new_instance['first_column'] ) ) ? $new_instance['first_column'] : '';
        $instance['second_column'] = ( ! empty( $new_instance['second_column'] ) ) ? $new_instance['second_column'] : '';
        $instance['third_column'] = ( ! empty( $new_instance['third_column'] ) ) ? $new_instance['third_column'] : '';

        return $instance;
    }

}