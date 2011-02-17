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

//initializing variables
$widget_id = $_REQUEST['widget_id'];
$object_ids = array();

foreach ($_REQUEST['menu-item'] as $item) foreach ($item as $menu_item_object_id => $id)
{
	$object_ids[$id] = $id;
}

twc_save_widget_fields( $widget_id, array('menu_item_object_id' => $object_ids) );


