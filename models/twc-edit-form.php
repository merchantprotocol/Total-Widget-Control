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






//initializing variables
global $wp_registered_widget_controls, $wp_registered_sidebars, $sidebars_widgets, $widget;
$sidebars_widgets = twc_wp_get_sidebars_widgets();

if (!$widget)
{
	wp_redirect('widgets.php');
	exit();
}

$sidebar_id = twc_get_widgets_sidebar( $widget['id'] );

//this is an addnew
if (isset($_REQUEST['addnew']) && $_REQUEST['addnew'])
{
	$sidebar_id = array_shift( $keys = array_keys($wp_registered_sidebars) );
}

$sidebar_args = $wp_registered_sidebars[$sidebar_id];
$control = $wp_registered_widget_controls[$widget['id']];

require $view;

