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
global $wp_registered_sidebars, $wp_registered_widgets;
$sidebars_widgets = twc_wp_get_sidebars_widgets();
$current_screen = twc_get_current_screen();

$list_type = 'active';
if (twc_inactive_list()) $list_type = 'inactive';

//load only a single instance if this is an undo request
if ($current_screen->action == 'undo')
{
	//initializing variables
	$widget_slug = $_REQUEST['widget_id'];
	$widget = twc_get_widget_from_slug( $widget_slug );
	$sidebar_id = 'wp_inactive_widgets';
	$position = twc_get_widgets_sidebar( $widget_slug, 'position' );
	
	require $view;
	exit();
}

foreach ($wp_registered_sidebars as $sidebar_slug => $sidebar): 
	if (is_array($sidebars_widgets[$sidebar_slug]))
	foreach ($sidebars_widgets[$sidebar_slug] as $position => $widget_slug): 
		
		//reasons to continue
		if (((!twc_filter_list_for() && !twc_inactive_list() && $sidebar_slug == 'wp_inactive_widgets')
		|| (!twc_filter_list_for() && twc_inactive_list() && $sidebar_slug != 'wp_inactive_widgets'))
		|| (twc_filter_list_for() && twc_filter_list_for() != $sidebar_slug))
			continue;
		
		//initializing variables
		$widget = twc_get_widget_from_slug( $widget_slug );
		$sidebar_id = twc_get_widgets_sidebar( $widget_slug );
		$position = twc_get_widgets_sidebar( $widget_slug, 'position' );
		
		require $view;
		
	endforeach;
endforeach;



