<?php

/**
 * Adds Lfc_Content_Widget widget.
 */
class Lfc_Content_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_content_widget', 'description' => __( 'A single-column text area.', 'lfc-content' ) );
        $control_ops = array( 'width' => 500, 'height' => 730 );
        parent::__construct( 'lfc-content-module', __( 'Content', 'lfc-content' ), $widget_ops, $control_ops );
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
            $title = $instance['title'] ;
        }else{
            $title = '';
        }

        if( isset( $instance['content'] ) ){
            $content = $instance['content'] ;
        }else{
            $content = '';
        }

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/content.php' );
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
        if ( isset( $instance[ 'content' ] ) ) {
            $content = $instance[ 'content' ];
        }
        else {
            $content = '';
        }
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Content Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Content panel allows you to add a media-rich content section, using a Visual editor to add text and images. 
            </div>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>
        
        <div class="lfc-settings-section">
            <div class="lfc-editor-container">
                <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
                <div id="wp-content-media-buttons" class="wp-media-buttons">
                    <a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="content" title="Add Media">
                        <span class="wp-media-buttons-icon"></span> Add Media
                    </a>
                </div>
                <div class="wp-editor-tabs tmce-active">
                    <a href="#" class="html-button wp-switch-editor switch-html" >HTML</a>
                    <a href="#" class="visual-button wp-switch-editor switch-tmce">Visual</a>
                </div>
                <textarea class="widefat lfc-visual-editor" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>"><?php echo esc_attr( $content ); ?></textarea>
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
        $instance['content'] = ( ! empty( $new_instance['content'] ) ) ? $new_instance['content'] : '';

        return $instance;
    }

}