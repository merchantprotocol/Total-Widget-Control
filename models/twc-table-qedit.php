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
global $wp_registered_widget_controls, $wp_registered_sidebars, $sidebars_widgets, $widget;
$sidebars_widgets = twc_wp_get_sidebars_widgets();
$sidebar_id = twc_get_widgets_sidebar( $widget['id'] );
$sidebar_args = $wp_registered_sidebars[$sidebar_id];
$control = $wp_registered_widget_controls[$widget['id']];

require $view;