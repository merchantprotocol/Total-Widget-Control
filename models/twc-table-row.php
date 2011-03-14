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
global $twcp_pagi, $twc_rows;
$current_screen = twc_get_current_screen();
$count = 0;
$list_type = 'active';
if (twc_inactive_list()) $list_type = 'inactive';

//reasons to fail
if (empty($twc_rows)) return false;

//load only a single row if this is an undo request
if ($current_screen->action == 'undo')
{
	//initializing variables
	$widget_slug = $_REQUEST['widget_id'];
	$widget = twc_get_widget_by_id( $widget_slug );
	$sidebar_id = 'wp_inactive_widgets';
	$position = twc_get_widgets_sidebar( $widget_slug, 'position' );
	
	require $view;
	exit();
}

foreach ((array)$twc_rows as $sidebar_slug => $positioned)
{
	foreach ((array)$positioned as $position => $widget_data)
	{
		foreach ((array)$widget_data as $widget_slug => $widget)
		{
			//for pagination
			$count++;
			if ($twcp_pagi['per_page'] != 0 && ($twcp_pagi['start'] > $count || $count > $twcp_pagi['stop']))
				continue;
			
			//initializing variables
			$sidebar_id = twc_get_widgets_sidebar( $widget_slug );
			$position = twc_get_widgets_sidebar( $widget_slug, 'position' );
			
			require $view;
		}
	}
}
