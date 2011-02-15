<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.0
 * 
 * 
 */


//initializing variables
global $widget;

?>
<div class="head_space"></div>
<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder">
		<?php do_action('twc_nav_menu_list'); ?>
	</div>
	<div id="menu-management-liquid">
		<?php do_action('twc_widget_metabox'); ?>
	</div>
</div>