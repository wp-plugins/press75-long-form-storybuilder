<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
<?php
	$page_id     = get_queried_object_id();
	$page_options = LFC_Options::getPageOptions( $page_id );
?>
	<?php if( $page_options['fonts'] != '' ):
		$font_options = LFC_OPTIONS::getFontOptions();
		$font_combo = $font_options[ $page_options['fonts'] ]; ?>
		<link href='http://fonts.googleapis.com/css?family=<?php echo $font_combo['title_import']; ?>' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=<?php echo $font_combo['body_import']; ?>' rel='stylesheet' type='text/css'>
	<?php endif; ?>

<style>
	.lfc-section_callout, .lfc-section_nav, .lfc-dropdown-menu {
		background: <?php echo $page_options['primary_color']; ?>;
		color: <?php echo $page_options['text_color']; ?>;
	}

	.lfc-section_nav a, .callout{
		color: <?php echo $page_options['text_color']; ?>;
	}

	.lfc-module a {
		color: <?php echo $page_options['secondary_color']; ?>;
	}

	.lfc-module a:hover {
		color: <?php echo $page_options['hover_color']; ?>;
	}

	<?php if( $font_options ): ?>
		
		#lfc-sections, .lfc-section_heading-subtitle, .intro-lfc-content-subtitle, .lfc-section_nav {
			font-family: <?php echo $font_combo['body_family']; ?>;
		}

		#lfc-sections h1, #lfc-sections h2, #lfc-sections h3,
		.callout-title, .lfc-section_heading-title, .intro-lfc-content-title {
			font-family: <?php echo $font_combo['title_family']; ?>;
		}

		.intro-lfc-content-title, .intro-lfc-content-title h1 {
			font-weight: <?php echo $font_combo['title_weight']; ?>;
		}
	
		<?php endif; ?>
</style>
</head>
<body>
<div id="lfc-sections" class="lfc-sections">
    <?php $id = 'modules-' . get_the_id();?>
	<?php if( ! dynamic_sidebar( $id ) ): ?>
		<?php if( is_user_logged_in() ): ?>
			<div class="lfcEmpty">
				<div class="lfcEmpty-content">
					<div class="lfcEmpty-section">
						<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/images/logo-new.svg" class="lfcEmpty-image lfcEmpty-image_narrow" />
					</div>
					<hr />
					<div class="lfcEmpty-section">
						<strong>Step 1:</strong><br />
						Let’s get you started with the Long Form Storybuilder plugin! You can get started building your Long Form story by clicking the button on the left side of the window...
						<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/images/step01.png" class="lfcEmpty-image" />
					</div>
					<hr />
					<div class="lfcEmpty-section">
						<strong>Step 2:</strong><br />
						Select your page on the next pane and get started adding your panels, by clicking the “Add a Section” button. A new pane will open where you can select the type of “Section” or “Panel” you would like to add. 
						<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/images/step02.png" class="lfcEmpty-image" />
					</div>
					<hr />
					<div class="lfcEmpty-section">
						<strong>Step 3:</strong><br />
						Edit your Panel content within the fly-out editor. Add images, add text, change colors, edit fonts, and add your content. There are a lot of amazing things you can create with the Long Form Storybuilder plugin. Don’t be afraid to play around with the different Panels and options for each “Section”. Most of all have fun and contact us for support. 
						<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/images/step03.png" class="lfcEmpty-image" />
					</div>
					<hr />
					<div class="lfcEmpty-section">
						<a href="http://press75.com" target="_blank" class="lfcEmpty-button lfcEmpty-button_grey">Get Support</a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div><!-- #main-content -->

<?php
get_footer();
