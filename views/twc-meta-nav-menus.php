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

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');

// Load all the nav menu interface functions
require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

wp_nav_menu_setup();
wp_initial_nav_menu_meta_boxes();

remove_meta_box( 'nav-menu-theme-locations', 'nav-menus', 'side' );
remove_meta_box( 'add-custom-links', 'nav-menus', 'side' );

do_meta_boxes( 'nav-menus', 'side', null );