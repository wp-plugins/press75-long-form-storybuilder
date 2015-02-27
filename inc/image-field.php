<?php

/**
 * LFC Image Field Class
 *
 * Provides an Image Field widget layout for adding images
 * and editing them within the Customizer
 *
 * @package lfc
 */
class Lfc_Image_field {

    function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_scripts' ) );
    }

    function admin_scripts( $page ) {
        if ( 'widgets.php' == $page ) {
            wp_enqueue_media();
        }
    }

    function customizer_scripts() {
        wp_enqueue_media();
    }

}

/**
 * LFC Image Field
 *
 * Provides the HTML for the advanced Image Field Widget
 * @param obj $obj
 * @param obj $instance
 * @param array $settings
 */
function lfc_image_field( $obj, $instance, $settings = array() ) {
    $defaults = array(
        'title'       => 'Image',
        'update'      => 'Update Image',
        'field'       => 'image_id',
    );

    $settings = wp_parse_args( $settings, $defaults );
    extract( $settings );

    $instance[$field] = !empty( $instance[$field] ) ? $instance[$field] : '';
    $image = wp_get_attachment_image_src( $instance[$field], 'medium' );
    $src = !empty( $image ) ? current( $image ) : '';
    ?>
    <div class="lfc-image-wrap">
        <?php $image_id = $obj->get_field_id( $field ); ?>

        <div class="lfc-image" id="lfc-image-<?php echo $image_id; ?>">
            <div class="newimage-section">
                <input type="button" class="button lfc-image-select" value="Select image" data-title="<?php echo $title; ?>" data-update="<?php echo $update; ?>" data-target="<?php echo $image_id; ?>" />
                <input type="hidden" class="lfc-image-id" name="<?php echo $obj->get_field_name( $field ); ?>" id="<?php echo $image_id; ?>">
            </div>
            <div class="image-section">
                <div class="image">
                    <img src="<?php echo $src ?>" />
                </div>
                <div class="buttons">
                    <input type="hidden" name="<?php echo $obj->get_field_name( $field ); ?>" id="<?php echo $image_id; ?>" class="lfc-image-id" value="<?php echo $instance[$field]; ?>">
                    <input type="button" class="button lfc-image-select" data-title="<?php echo $title; ?>" data-update="<?php echo $update; ?>" data-target="<?php echo $image_id; ?>" value="Edit/change" />
                    <input type="button" class="button lfc-image-remove" value="Remove" data-target="<?php echo $image_id; ?>" />
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 *
 * Instantiate the LFC Image Field Class 
 * @return Lfc_Image_field();
 */
if( is_admin() ) {
    global $lfc_iwf;

    $lfc_iwf = new Lfc_Image_field();
}

