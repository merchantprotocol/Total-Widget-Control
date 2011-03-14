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

//initializing
$widget_id = $_REQUEST['widget_id'];
$old_sidebar_id = $_REQUEST['old_sidebar_id'];
$delete_permanently = (isset($_REQUEST['delete_confirmation']));
$widget = twc_get_widget_by_id( $widget_id );


twc_delete_widget_instance($widget_id, $delete_permanently);


if (!isset($_REQUEST['view']))
{
	wp_redirect( admin_url('widgets.php') );
	exit();
}

require $view;