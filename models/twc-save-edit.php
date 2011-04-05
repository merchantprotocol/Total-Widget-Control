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
global $widget;

//recreate the time data
$_REQUEST['twcp_publish_time'] = strtotime($_REQUEST['twcp_YY'].'-'.$_REQUEST['twcp_mm'].'-'.$_REQUEST['twcp_jj'].' '.$_REQUEST['twcp_hh'].':'.$_REQUEST['twcp_ii']);
unset($_REQUEST['menu-item']);

//saving
twc_save_widget_fields( $widget['id'], $_REQUEST );
twc_save_widget_sidebar( $widget['id'], $_REQUEST['sidebar'], $_REQUEST['sidebar_position'] );
