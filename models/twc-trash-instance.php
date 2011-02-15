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

//initializing
$widget_id = $_REQUEST['widget_id'];
$old_sidebar_id = $_REQUEST['old_sidebar_id'];
$delete_permanently = (isset($_REQUEST['delete_confirmation']));
$widget = twc_get_widget_from_slug( $widget_id );


twc_delete_widget_instance($widget_id, $delete_permanently);


if (!isset($_REQUEST['view']))
{
	wp_redirect('widgets.php');
	exit();
}

require $view;