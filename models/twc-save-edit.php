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
if ( TWC_CURRENT_USER_CANNOT ) wp_die();

//initializing variables
global $widget;

twc_save_widget_fields( $widget['id'], $_REQUEST );
twc_save_widget_sidebar( $widget['id'], $_REQUEST['sidebar'], $_REQUEST[$_REQUEST['sidebar'].'_position'] );

