<a name="<?php echo $title; ?>"></a>
<div class="lfc-module lfc-section lfc-section_fullFeature lfc-section_fullFeature__<?php echo $text_alignment; ?>" id="<?php echo $widget_id; ?>">
    <?php if( $image_type == 'fixed' ): ?>
        <div class="fullFeature_clip">
        	<div class="fullFeature windowSized" style="background-image:url(<?php echo $image; ?>);">
                <?php if( $heading != '' ): ?>
                    <div class="lfc-section_intro-bg"></div>
                <?php endif; ?>
        		<div class="intro-lfc-content fontRepaint">
                    <div class="intro-lfc-content-title">
                        <?php echo $heading; ?>
                    </div>
                    <div class="intro-lfc-content-subtitle">
                        <?php echo $content; ?>
                    </div>
                </div>
        	</div><!-- END .callout -->
        </div>
    <?php else: ?>
            <div class="fullFeature-static" style="background-image: url('<?php echo $image; ?>');" >
                <?php if( $heading != '' ): ?>
                    <div class="lfc-section_intro-bg"></div>
                <?php endif; ?>
                <div class="intro-lfc-content fontRepaint">
                    <div class="intro-lfc-content-title">
                        <?php echo $heading; ?>
                    </div>
                    <div class="intro-lfc-content-subtitle">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div><!-- END .callout -->
    <?php endif; ?>
</div> <!-- END .lfc-section -->
