<?php

/**
 * Adds Lfc_Image_Carousel_Widget widget.
 */
class Lfc_Image_Carousel_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        $widget_ops = array( 'classname' => 'lfc_image_carousel_widget', 'description' => __( 'A carousel of images.', 'lfc-image-carousel' ) );
        $control_ops = array( 'width' => 500, 'height' => 400 );
        parent::__construct( 'lfc-image-carousel-module', __( 'Image Carousel', 'lfc-image-carousel' ), $widget_ops, $control_ops );
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

        if( isset( $instance['images'] ) ){
            $images_json = $instance['images'];
            $images = json_decode( $images_json, true );
            $count = count( $images );
        }else{
            $images = array();
            $count = 0;
        }

        $widget_id = $args['widget_id'];

        include( plugin_dir_path( __DIR__ ) . 'views/image_carousel.php' );
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

        if ( isset( $instance[ 'images' ] ) ) {
            $images_json = sanitize_text_field( $instance[ 'images' ] );
            $images = json_decode( $images_json, true );
        }
        else {
            $images_json = '[]';
            $images = array();
        }
        ?>

        <div class="lfc-module-instructions">
            <div class="lfc-instructions-hd">
                Image Carousel Instructions
            </div>
            <div class="lfc-instructions-bd">
                The Image Carousel panel allows you to add a slideshow of images to your content, which the users can navigate through.
            </div>
        </div>

        <div class="lfc-settings-section">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Section Name:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </div>

        <div class="lfc-settings-section">
            <button class="add-image-to-carousel">Add Image</button>
            <div class="lfc-carousel-images">
                <?php if( count( $images ) > 0 ): foreach( $images as $image ): ?>
                    <div class="lfc-carousel-image" data-json='<?php echo json_encode( $image ); ?>'>
                        <div class="remove-lfc-carousel-image">x</div>
                        <img src="<?php echo $image['thumbnail']; ?>" />
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
        <input class="images_hidden" id="<?php echo $this->get_field_id( 'images' ); ?>" name="<?php echo $this->get_field_name( 'images' ); ?>" type="hidden" value='<?php echo $images_json; ?>'>
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
        $instance['images'] = ( ! empty( $new_instance['images'] ) ) ? $new_instance['images'] : '[]';

        return $instance;
    }

}