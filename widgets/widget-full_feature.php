<?php

/**
 * Adds Lfc_Full_Feature_Widget widget.
 */
class Lfc_Full_Feature_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_full_feature_widget', 'description' => __( 'A full-width image with text overlay.', 'lfc-full-feature' ) );
        $control_ops = array( 'width' => 400, 'height' => 400 );
        parent::__construct( 'lfc-full-feature-module', __( 'Full Feature', 'lfc-full-feature' ), $widget_ops, $control_ops );
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

        if ( isset( $instance[ 'image_type' ] ) ) {
            $image_type = $instance[ 'image_type' ];
        }else{
            $image_type = 'fixed';
        }

        if ( isset( $instance[ 'text_alignment' ] ) ) {
            $text_alignment = $instance[ 'text_alignment' ];
        }else{
            $text_alignment = 'centered';
        }

        if( isset( $instance['heading'] ) ){
            $heading = nl2br( $instance[ 'heading' ] );
        }else{
            $heading = '';
        }

        if( isset( $instance['content'] ) ){
            $content = nl2br( $instance['content'] );
        }else{
            $content = '';
        }

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/full_feature.php' );
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

        if ( isset( $instance[ 'image_type' ] ) ) {
            $image_type = $instance[ 'image_type' ];
        }else{
            $image_type = 'fixed';
        }

        if ( isset( $instance[ 'text_alignment' ] ) ) {
            $text_alignment = $instance[ 'text_alignment' ];
        }else{
            $text_alignment = 'centered';
        }

        if ( isset( $instance[ 'heading' ] ) ) {
            $heading = $instance[ 'heading' ];
        }else{
            $heading = '';
        }

        if ( isset( $instance[ 'content' ] ) ) {
            $content = $instance[ 'content' ];
        }else{
            $content = '';
        }
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Full Feature Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Full Feature panel allows you to add a full-width image with a title and subtitle to your content.
            </div>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'image_type' ); ?>"><?php _e( 'Image Type:' ); ?></label> 
            <select class="widefat lfc-menu-toggle" id="<?php echo $this->get_field_id( 'image_type' ); ?>" name="<?php echo $this->get_field_name( 'image_type' ); ?>">
                <option value="static" <?php if( $image_type == 'static' ){ echo "selected"; } ?>>Static Image</option>
                <option value="fixed" <?php if( $image_type == 'fixed' ){ echo "selected"; } ?>>Fixed Image</option>
            </select>
        </div>

        <div class="lfc-settings-section">
            <?php lfc_image_field( $this, $instance ); ?>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'text_alignment' ); ?>"><?php _e( 'Text Alignment:' ); ?></label> 
            <select class="widefat lfc-menu-toggle" id="<?php echo $this->get_field_id( 'text_alignment' ); ?>" name="<?php echo $this->get_field_name( 'text_alignment' ); ?>">
                <option value="centered" <?php if( $text_alignment == 'centered' ){ echo "selected"; } ?>>Centered</option>
                <option value="left" <?php if( $text_alignment == 'left' ){ echo "selected"; } ?>>Left</option>
                <option value="right" <?php if( $text_alignment == 'right' ){ echo "selected"; } ?>>Right</option>
            </select>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'heading' ); ?>"><?php _e( 'Main Heading:' ); ?></label> 
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'heading' ); ?>" name="<?php echo $this->get_field_name( 'heading' ); ?>" rows="5"><?php echo esc_attr( $heading ); ?></textarea>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" rows="5"><?php echo esc_attr( $content ); ?></textarea>
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
        $instance['image_type'] = ( ! empty( $new_instance['image_type'] ) ) ? $new_instance['image_type'] : 'fixed';
        $instance['text_alignment'] = ( ! empty( $new_instance['text_alignment'] ) ) ? $new_instance['text_alignment'] : 'centered';
        $instance['heading'] = ( ! empty( $new_instance['heading'] ) ) ? $new_instance['heading'] : '';
        $instance['content'] = ( ! empty( $new_instance['content'] ) ) ? $new_instance['content'] : '';
        return $instance;
    }

}