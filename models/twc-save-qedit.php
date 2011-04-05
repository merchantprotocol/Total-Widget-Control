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

// save the widget
global $widget;
$fields = array();

if ($widget['multiwidget'])
{
	//variable as constructed by wordpress
	if (isset($_REQUEST['widget-'.$widget['id_base']]) && isset($_REQUEST['widget-'.$widget['id_base']][$widget['number']]))
	{
		$fields = $_REQUEST['widget-'.$widget['id_base']][$widget['number']];
	}
}
$fields = wp_parse_args($fields, $widget['p']);

twc_save_widget_fields( $widget['id'], $fields );



