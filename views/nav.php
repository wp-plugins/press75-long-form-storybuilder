<div class="lfc-section lfc-section_nav module" id="<?php echo $widget_id; ?>">
	<div class="lfcNav">
        <div class="lfcNavLogo">
            <a href="<?php echo $site_url; ?>"><img src="<?php echo $image; ?>" /></a>
        </div>
        <?php if( $menu_type == 'wp_menu' ): ?>
            <?php $args = array( 'menu' => $wp_menu, 'menu_class' => 'lfcNavItems', 'container' => false, 'walker' => new Lfc_Walker_Nav_Menu() ); wp_nav_menu( $args ); ?>
        <?php else: ?>
            <ul class="lfcNavItems">
                <?php if( isset( $current_pages ) ): foreach( $current_pages as $page ): ?>
                    <?php if( strpos( $page->url, '#' ) !== false ): ?>
                        <li class="lfcNavItem">
                            <a href="javascript:void(0)" title="Section link to <?php echo $page->title; ?>" data-section="<?php echo $page->title; ?>" target="_self" class="section_nav"><?php echo $page->title; ?></a></li>
                        </li>
                    <?php else: ?>
                        <li class="lfcNavItem">
                            <a href="<?php echo $page->url; ?>" title="Permanent link to <?php echo $page->title; ?>" target="_self"><?php echo $page->title; ?></a></li>
                        </li>
                    <?php endif; ?>
                <?php endforeach; endif; ?>
            </ul>
        <?php endif; ?>
        
    </div>
</div> <!-- END .lfc-section -->