<a name="<?php echo $title; ?>"></a>
<div class="lfc-section lfc-lfc-module lfc-section_callout module" id="<?php echo $widget_id; ?>">
	<div class="callout fontRepaint">
        <div class="callout-title">
            <?php echo $callout_title; ?>
        </div>
        <?php if( $callout_subtitle != '' ): ?>
            <div class="callout-hr"></div>
            <div class="callout-subtitle">
                <?php echo $callout_subtitle; ?>
            </div>
        <?php endif; ?>
	</div><!-- END .callout -->
</div> <!-- END .lfc-section -->