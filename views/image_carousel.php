<a name="<?php echo $title; ?>"></a>
<div class="lfc-module lfc-section lfc-section_slideshow" id="<?php echo $widget_id; ?>">
	<div class="carousel"  data-slides="<?php echo $count; ?>">
		<?php foreach( $images as $image ): ?>
			<div>
				<img src="<?php echo $image['url']; ?>" />
			</div>
		<?php endforeach; ?>
	</div>
</div>