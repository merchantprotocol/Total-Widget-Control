<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.6.0
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');

//initializing variables
global $widget;
$twc_menu_items = array();

if (array_key_exists('object-id', $_REQUEST))
{
	if ( !isset($widget['p']['twc_menu_item']) || !is_array($widget['p']['twc_menu_item']) ) die();
	if ( !isset($widget['p']['twc_menu_item'][$_REQUEST['object-id']]) ) die();
	
	unset($widget['p']['twc_menu_item'][$_REQUEST['object-id']]);
	
	//saving
	twc_save_widget_fields( $widget['id'], $widget['p'] );
}

//nothing else to do
die();