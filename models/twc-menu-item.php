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

// DISPLAY A SINGLE VIEW ON LOOP
if (isset($args[1]))
{
	$twc_menu_items = $args[1];
}
else
{
	if (isset($_REQUEST['menu-item']) && is_array($_REQUEST['menu-item'])) 
	{
		$twc_menu_items = $_REQUEST['menu-item'];
		foreach ($twc_menu_items as $key => $array)
		{
			unset($twc_menu_items[$key]);
			$twc_menu_items[$array['menu-item-object-id']] = $array;
		}
	}
}

// SAVING THE MENU ITEMS ON AJAX UPDATE
if (array_key_exists('view', $_REQUEST) && $_REQUEST['view'] == 'twc-menu-item')
{
	if (!isset($widget['p']['twc_menu_item']) || !is_array($widget['p']['twc_menu_item']))
		$widget['p']['twc_menu_item'] = array();
	
	$old_menu_items = $widget['p']['twc_menu_item'];
	$all_menu_items = $twc_menu_items + $widget['p']['twc_menu_item'];
	$widget['p']['twc_menu_item'] = $all_menu_items;
	
	//saving
	twc_save_widget_fields( $widget['id'], $widget['p'] );
	
	$twc_menu_items = array_diff_assoc($all_menu_items, $old_menu_items);
}

// LOOPING THE MENU ITEMS
if (!empty($twc_menu_items))
{
	foreach ($twc_menu_items as $id => $menu_item) 
	{
		require $view;
	}
}
else
{
	die('false');
}