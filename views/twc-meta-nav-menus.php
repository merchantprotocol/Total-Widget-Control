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
 * 
 */


// Load all the nav menu interface functions
require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

wp_nav_menu_setup();
wp_initial_nav_menu_meta_boxes();

remove_meta_box( 'nav-menu-theme-locations', 'nav-menus', 'side' );
remove_meta_box( 'add-custom-links', 'nav-menus', 'side' );

//inializing variables
global $widget;
$imploded = '';
if (!empty($widget['p']['menu_item_object_id']))
{
	$imploded = "'".implode("','",(array)$widget['p']['menu_item_object_id'])."'";
}

do_meta_boxes( 'nav-menus', 'side', null ); 
 
 
?>
<script><?php echo "var twcSelectedIds = [$imploded];"; ?></script>



