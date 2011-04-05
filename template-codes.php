<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.5twentystudios.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.5.5
 * 
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");

/**
 * Function is responsible for returning the number of widgets that 
 * are set to display on the current page.
 *
 * @param string $sidebar_id
 * @return integer
 */
function twc_count_widgets( $index = 1 )
{
	//initializing variables
	global $wp_registered_sidebars, $sidebars_widgets;
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	$count = 0;
	
	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title($value['name']) == $index ) {
				$index = $key;
				break;
			}
		}
	}
	
	if (isset($sidebars_widgets[$index]))
	{
		foreach ((array)$sidebars_widgets[$index] as $position => $widget_slug)
		{
			//initializing variables
			$widget = twc_get_widget_by_id($widget_slug);
			$display = twc_is_widget_displaying($widget);
					
			//reasons to continue
			$display = apply_filters('twc_display_widget', $display, $widget);
			if (!$display) continue;
						
			$count++;
		}
	}
		
	return $count;
}

/**
 * Function is responsible for returning the sidebar widgets as a string
 *
 * @param string $sidebar_id
 * @return integer
 */
function twc_get_sidebar( $sidebar_id = 1 )
{
	ob_start();
	twc_dynamic_sidebar( $sidebar_id );
	$sidebar = ob_get_clean();
	
	return $sidebar;
}

/**
 * Function is responsible for echoing the widgets of a given sidebar
 *
 * @param string $sidebar_id
 * @return integer
 */
function twc_sidebar( $sidebar_id = 1 )
{
	twc_dynamic_sidebar( $sidebar_id );
}	

/**
 * Function is responsible to get the currently dislaying widget, or
 * a specific widget from a given id
 *
 * @param string $widget_id
 * @param boolean $force	//forces the widget to display, regardless of admin settings
 * @return string $twc_widget_to_wrap
 */
function twc_get_widget( $widget_id = null, $force = false )
{
	if (is_null($widget_id))
	{
		//initializing variables
		global $twc_widget_to_wrap;
		return $twc_widget_to_wrap;
	}
	
	ob_start();
	twc_display_the_widget(null, $widget_id, null, $force);
	$display = ob_get_clean();
	return $display;
}

/**
 * Function is responsible for echoing the given widget id, or the currently
 * displaying widget.
 * 
 * @param string $widget_id
 * @param boolean $force
 * @return string $twc_widget_to_wrap
 */
function twc_widget( $widget_id = null, $force = false )
{
	echo twc_get_widget( $widget_id, $force = false );
	return true;
}

/**
 * Function returns the object id as a string
 * 
 * The object id is loaded when a specific record is loaded from the database.
 * This id is used to compare against the menu item id.
 * 
 * @return unknown
 */
function twc_get_object_id()
{
	return twc_set_object_id();
}

/**
 * Function is responsible for making sure that set is run
 * and then the string is returned
 *
 * @return string
 */
function twc_get_object_url()
{
	return twc_set_object_url();
}

/**
 * Function is responsible for accepting the shortcode data and 
 * returning the queried widget
 * 
 * @example 
<pre>
	[twc_show_widget id="" force=""]
</pre>
 *
 * @param array $atts
 * @return string
 */
function twc_show_widget( $atts = array() )
{
	//initializing variables
	$atts = shortcode_atts( array(
		'id' => FALSE,
		'force' => FALSE,
	), $atts );
	
	//reasons to fail
	if (!$atts['id']) return false;
	
	return twc_get_widget( $atts['id'], $atts['force'] );
}

/**
 * Function is responsible for accepting the shortcode data and 
 * returning the queried sidebar
 * 
 * @example 
<pre>
	[twc_show_sidebar id=""]
</pre>
 *
 * @param array $atts
 * @return string
 */
function twc_show_sidebar( $atts = array() )
{
	//initializing variables
	$atts = shortcode_atts( array(
		'id' => FALSE,
	), $atts );
	
	//reasons to fail
	if (!$atts['id']) return false;
	
	return twc_get_sidebar( $atts['id'] );
}



