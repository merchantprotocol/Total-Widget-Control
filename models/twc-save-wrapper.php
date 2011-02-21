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
$widget = twc_get_widget_from_slug( $_REQUEST['widget-id'] );
$POST = $widget['p'];
$POST['twc_wrapper_file'] = $_REQUEST['wrapper_path'];

twc_save_widget_fields( $_REQUEST['widget-id'], $POST );

