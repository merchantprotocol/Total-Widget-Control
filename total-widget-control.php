<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.0
 * 
 * @TODO I need to loop the widgets after init and create my own global arrays of certain widgets.
 * @TODO After looping the init global widget arrays, they need to be filtered
 */

defined('ABSPATH') or die("Cannot access pages directly.");


/**
 * function adds adjusts the plugin actions
 *
 * @param unknown_type $orig_links
 * @return unknown
 */
function twc_add_action_links( $orig_links )
{
	//initializing variables
	$links = array();
	$links['deactivate'] = $orig_links['deactivate'];
	
	$links['load'] = '<a href="widgets.php?list_style=twc" title="Open the Total Widget Control System" class="edit">'
	._('Manage Widgets').'</a>';
	
	return $links;
}

/**
 * This function will add help and screen option text to the widget pages
 *
 */
function twc_add_help_text()
{ 
	//initializing variables
	global $wp_registered_sidebars;
	$current_screen = twc_get_current_screen();
	
	switch ($current_screen->action)
	{
		default: 
			add_filter('screen_settings', 'twc_view_screen_options'); 
			add_filter('contextual_help', 'twc_view_help_home'); 
			break;
		case 'edit': 
			add_filter('contextual_help', 'twc_view_help_edit'); 
			break;
		case 'add':
			add_filter('contextual_help', 'twc_view_help_add'); 
			break;
	}
	
}

/**
 * function adds the media resources to the twc views
 *
 * @TODO Need to register and call the CSS file here
 */
function twc_add_javascript()
{
	//initializing variables
	$current_screen = twc_get_current_screen();
	
	if ($current_screen->parent_base != 'widgets') return;
	
	wp_register_script( 'twc-nav-menu', plugin_dir_url(__file__).'js/twc-nav-menu.js');
	wp_register_script( 'twc-base', plugin_dir_url(__file__).'js/twc.js');
	
	switch($current_screen->action)
	{
		default:
			wp_enqueue_script( 'twc-base' );
			break;
		case 'edit':
		case 'add':
			// jQuery
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			
			// Nav Menu functions
			wp_enqueue_script( 'twc-base' );
			wp_enqueue_script( 'twc-nav-menu' );
			
			// Metaboxes 
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

			//css
			wp_admin_css( 'nav-menu' );
			
			break;
	}
	
}

/**
 * Cleans up the sidebar and widget variables
 * 
 * @TODO Make sure that this function cleans up empty sidebar IDs
 *
 * @param object $wp
 */
function twc_clear( $wp = null )
{
	//intiailizing variables
	global $wp_registered_widgets, $wp_registered_sidebars;
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	$lost_widgets = array();
	
	foreach ($wp_registered_widgets as $widget_id => $widget_class)
	{
		if (!$widget_id) continue;
		$sidebar_id = twc_get_widgets_sidebar($widget_id);
		
		if ($sidebar_id == 'wp_inactive_widgets')
		{
			if (substr($widget_id,-2) == '-1') continue;
			if ((int)trim(substr($widget_id,-2)) == -1) continue;
			$lost_widgets[] = $widget_id;
		}
	}
	
	foreach ($wp_registered_sidebars as $sidebar_slug => $sidebar): 
		if (is_array($sidebars_widgets[$sidebar_slug]))
		foreach ($sidebars_widgets[$sidebar_slug] as $position => $widget_slug): 
			
			$widget = twc_get_widget_from_slug( $widget_slug );	
			if (!$widget)
			{
				unset($sidebars_widgets[$sidebar_slug]);
			}
			
		endforeach;
	endforeach;
	
	$sidebars_widgets['wp_inactive_widgets'] = $lost_widgets;
	wp_set_sidebars_widgets($sidebars_widgets);
}

/**
 * Total Widget Controller
 * 
 * This function is responsible for determining which administrative page that we're
 * supposed to be looking at. We use the standard action call system that wp-admin
 * is used to using.
 *
 */
function twc_controller()
{
	//initializing variables
	global $wp_registered_sidebars;
	$current_screen = twc_get_current_screen();
	$view = 'twc-table';
	
	switch ($current_screen->action)
	{
		default: 
			if (count($wp_registered_sidebars) == 1)
				$view = 'twc-no-sidebars';
			break;
		case 'edit': 
			$view = 'twc-edit';
			break;
		case 'delete': 
			twc_get_show_view('twc-trash-instance');
			$view = 'twc-table';
			break;
		case 'save': 
			twc_get_show_view('twc-save-edit');
			$view = 'twc-add';
			break;
		case 'add':
			if (isset($_REQUEST['editwidget']) && !empty($_REQUEST['editwidget']))
			$view = 'twc-edit';
			else 
			$view = 'twc-add';
			break;
	}
	
	return do_action($view);
}

/**
 * returns the number of active widgets
 * 
 * @TODO Should use a static variable to remember the return value
 * @return integer $count
 */
function twc_count_active_widgets()
{
	//initializing variables
	global $wp_registered_widgets;
	$count = 0;
	
	foreach ($wp_registered_widgets as $widget_id => $class)
	{
		if (substr($widget_id,-2) == '-1') continue;
		if ((int)trim(substr($widget_id,-2)) == -1) continue;
		$count++;
	}
	
	return $count - twc_count_inactive_widgets();
}

/**
 * returns the number of inactive widgets
 * 
 * @TODO Should use a static variable to remember the return value
 * @return integer
 */
function twc_count_inactive_widgets()
{
	//initializing variables
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	return count($sidebars_widgets['wp_inactive_widgets']);
}

/**
 * Function is responsible for counting the number of widget
 * instances that will appear in a specific sidebar, on the current page.
 *
 * @TODO Need to use a static variable to remember this return value
 * 
 * @param unknown_type $sidebar_id
 * @param unknown_type $object_id
 */
function twc_count_page_sidebar_widgets( $sidebar_id, $object_id = null )
{
	//initializng variables
	global $sidebars_widgets;
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	$count = 0;
	
	if (is_null($object_id))
	{
		$object_id = twc_get_object_id();
	}
	
	//reasons to fail
	if (!isset($sidebars_widgets[$sidebar_id])) return false;
	
	foreach ((array)$sidebars_widgets[$sidebar_id] as $widget_id)
	{
		$widget = twc_get_widget_from_slug($widget_id);
		
		if (in_array($object_id, (array)$widget['p']['menu_item_object_id']))
			$count++;
	}
	return $count;
}

/**
 * Function is responsible for counting the number of widget
 * instances that will appear in a specific sidebar, on the current page.
 * 
 * @TODO Need to use a static variable to remember this return value
 *
 * @param unknown_type $sidebar_id
 * @param unknown_type $object_id
 */
function twc_count_sidebar_widgets( $sidebar_id )
{
	//initializng variables
	global $sidebars_widgets;
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	
	//reasons to fail
	if (!isset($sidebars_widgets[$sidebar_id])) return false;
	
	$widgets = $sidebars_widgets[$sidebar_id];
	return count($widgets);
}

/**
 * This function displays the widgets that are dynamic sidebar widgets
 * 
 * @TODO Test to see if this can be called directly
 *
 * @param unknown_type $sidebar_id
 */
function twc_default_sidebar( $index )
{
	//initializing variables
	global $wp_registered_sidebars, $wp_registered_widgets, $twc_default_sidebar_widgets, $twc_isDefault;
	
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
	
	//initializing variables
	$all_count = twc_count_page_sidebar_widgets($index);
	
	// fail if we have widgets on this page 
	// or if there are no defaults for this sidebar
	if ($all_count > 0) return false;
	if (!isset($twc_default_sidebar_widgets[$index])) return false;
	$twc_isDefault = true;
	
	foreach ((array)$twc_default_sidebar_widgets[$index] as $id => $widget)
	{
		if ( !isset($wp_registered_widgets[$id]) ) continue;
		
		$sidebar = twc_get_widgets_sidebar($index);
		$params = array_merge(
			array( array_merge( (array)$sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			(array) $wp_registered_widgets[$id]['params']
		);

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);
		
		$callback = $wp_registered_widgets[$id]['callback'];
		
		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}
	$twc_isDefault = false;
	
}

/**
 * This function will delete an instance completely
 *
 * @param unknown_type $widget_id
 */
function twc_delete_widget_instance( $widget_id, $delete_permanently = false )
{
	//initializing variables
	global $wp_registered_widget_updates, $wp_registered_widgets;
	$sidebar_id = twc_get_widgets_sidebar($widget_id);
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	
	$sidebar = isset($sidebars_widgets[$sidebar_id]) ? $sidebars_widgets[$sidebar_id] : array();
	$sidebar = array_diff( $sidebar, array($widget_id) );
	$sidebars_widgets[$sidebar_id] = $sidebar;
	
	if (!$delete_permanently)
	{
		$sidebars_widgets['wp_inactive_widgets'] = array_merge((array)$widget_id, (array) $sidebars_widgets['wp_inactive_widgets']);
	}
	else
	{
		//initializing variables
		$id_base = _get_widget_id_base($widget_id);
		$multi_number = str_replace($id_base.'-', '', $widget_id);
		
		$_POST = array();
		$_POST['multi_number'] = $multi_number;
		$_POST['the-widget-id'] = $widget_id;
		$_POST['delete_widget'] = '1';
		$wp_registered_widgets[$widget_id]['params'][0]['number'] = $multi_number;
		
		//this calls the widgets update function with the given params
		foreach ( (array) $wp_registered_widget_updates as $name => $control )
		{
			if ( $name != $id_base || !is_callable($control['callback']) )
				continue;
		
			ob_start();
				call_user_func_array( $control['callback'], $control['params'] );
			ob_get_clean();
			break;
		}
		
	}
	
	wp_set_sidebars_widgets($sidebars_widgets);
}

/**
 * Function calls the save widget_sidebar function with the proper
 * parameters to delete the relationship.
 *
 * @param unknown_type $widget_id
 * @param unknown_type $sidebar_id
 * @return unknown
 */
function twc_delete_widget_sidebar( $widget_id, $sidebar_id )
{
	return twc_save_widget_sidebar($widget_id, $sidebar_id, 0, true);
}

/**
 * function is responsible for hijacking the dynamic_sidebar function. 
 * This function will always return false to the dynamic sidebar
 *
 */
function twc_display_the_sidebar( $params )
{
	//initializing variables
	global $wp_registered_widgets, $twc_wp_registered_widgets, $twc_default_sidebar_widgets;
	
	if (!isset($twc_wp_registered_widgets))
	{
		$twc_wp_registered_widgets = $wp_registered_widgets;
	}
	
	//clean the registered_widgets global
	foreach ((array)$wp_registered_widgets as $widget_id => $widget)
	{
		if (is_a($wp_registered_widgets[$widget_id]['callback'][0], 'twcEmptyWidgetClass')) 
			break;
		
		// since we're running a loop already, then let's take this opportunity
		// to create the defaults widgets array\
		// @TODO This data needs to be sorted out just after the init function
		$_widget = twc_get_widget_from_slug($widget_id);
		if ($_widget['p']['twcp_default_sidebar'] == 'default')
		{
			$default_sidebar_id = twc_get_widgets_sidebar($widget_id);
			$twc_default_sidebar_widgets[$default_sidebar_id][$widget_id] = $widget;
		}
		
		//empty the registered_widgets array
		$wp_registered_widgets[$widget_id]['callback'] = array();
		$wp_registered_widgets[$widget_id]['callback'][0] = new twcEmptyWidgetClass();
		$wp_registered_widgets[$widget_id]['callback'][1] = 'twc_empty_callback';
	}
	
	return $params;
}

/**
 * function receives all of the widget data prior to display, and allows us
 * to return a false to hide the widget.
 * 
 * returning false will hide the widget
 * 
 * @TODO I need a function that will display the widget_display with only the widget_id param
 *
 * @param unknown_type $instance
 * @param unknown_type $widget
 * @param unknown_type $args
 * @return unknown
 */
function twc_display_the_widget($instance, $widget, $args)
{
	//initializing variables
	global $wp_query;
	$widget = twc_get_widget_from_slug($widget->id);
	
	//check to see if we're even going to load this widget
	$display = (is_array($widget['p']['menu_item_object_id']) 
		&& in_array(twc_get_object_id(), (array)$widget['p']['menu_item_object_id']));
	
	$display = apply_filters('twc_display_widget', $display, $widget);
	if (!$display) return false;
	
	//load the widget into a variable
	ob_start();
	$widget['callback'][0]->widget($args, $instance);
	$display = ob_get_clean();
	
	echo apply_filters('twc_widget_display', $display, $widget);
	return apply_filters('twc_wordpress_default_sidebar', false, $instance);
}

/**
 * function returns true if the widget is to be displayed
 *
 * @param boolean $current
 * @param array $widget
 * @return unknown
 */
function twc_display_if_excluded( $current, $widget )
{
	//initializing variables
	global $twc_isDefault;
	
	//check to see if we're even going to load this widget
	$isExclude = (isset($widget['p']['twcp_exclude_sidebar']) && $widget['p']['twcp_exclude_sidebar'] == 'exclude');
	
	if (!$twc_isDefault && ((!$current && !$isExclude) || ($current && $isExclude))) 
		return false;
	
	return true;
}

/**
 * function is responsible for returning true if the widget is a default
 *
 * @param unknown_type $display
 * @param unknown_type $widget
 * @return unknown
 */
function twc_display_if_default( $display, $widget )
{
	//initializing variables
	global $twc_isDefault;
	$isDefault = (isset($widget['p']['twcp_default_sidebar']) && $widget['p']['twcp_default_sidebar'] == 'default');
	
	//reasons to return
	if ($twc_isDefault && $isDefault) return true;
	
	return $display;
}

/**
 * The hijack is completed and this function is called with the correct sidebar_id
 * Now we can run our own sidebar.
 * 
 * @TODO Test to see if this can be run directly
 *
 * @param int|string $index Optional, default is 1. Name or ID of dynamic sidebar.
 * @return bool True, if widget sidebar was found and called. False if not found or not called.
 */
function twc_dynamic_sidebar( $index = 1 )
{
	//initializing variables
	global $wp_registered_sidebars, $wp_registered_widgets, $twc_isDefault;
	$twc_isDefault = false;
	
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

	//initializing variables
	$count = twc_count_sidebar_widgets($index);
	$sidebars_widgets = wp_get_sidebars_widgets();

	//reasons to return
	if ($count == 0) return false;
	if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
		return false;

	$sidebar = $wp_registered_sidebars[$index];

	$did_one = false;
	foreach ( (array) $sidebars_widgets[$index] as $id )
	{

		if ( !isset($wp_registered_widgets[$id]) ) continue;
		
		$sidebar = twc_get_widgets_sidebar($index);
		$params = array_merge(
			array( array_merge( (array)$sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			(array) $wp_registered_widgets[$id]['params']
		);

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);
		
		
		$callback = $wp_registered_widgets[$id]['callback'];

		
		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}

/**
 * Class is responsible for nothing.
 * This class is merely a filler class to receive the proper functionality
 * from the dynamic_sidebar function.
 *	
 */
class twcEmptyWidgetClass
{
	function twc_empty_callback( $sidebar_args )
	{
		//do nothing
	}
}

/**
 * Checks for the search type
 *
 * @return unknown
 */
function twc_filter_list_for()
{
	if (!isset($_REQUEST['twc_filter_widgets']) || empty($_REQUEST['twc_filter_widgets'])) return false;
	$_REQUEST['inactive'] = 'active';
	
	return $_REQUEST['twc_filter_widgets'];
}

/**
 * Get the current page
 * 
 * Function is responsible for loading the current screen object which defines the
 * current page as the admin widget page.
 * 
 * @return unknown
 */
function twc_get_current_screen()
{
	//initializing variables
	static $current_screen;
	
	if (isset($current_screen)) return $current_screen;
	
	//initializing variables
	$parts = pathinfo($_SERVER['PHP_SELF']);
	$parent_file = apply_filters("twc_parent_file", $parts['basename']); // 'themes.php' For plugins to move submenu tabs around.
	
	$current_screen = new stdClass();
	$current_screen->parent_file = $parent_file;
	$current_screen->parent_base = preg_replace('/\?.*$/', '', $parent_file);
	$current_screen->parent_base = str_replace('.php', '', $current_screen->parent_base);
	$current_screen->id = 'twc-widgets';
	$current_screen->base = 'widgets';
	
	if ($GLOBALS['TWCAUTH']&&!isset($_REQUEST['action']))
	{
		$current_screen->action = 'auth';
	}
	else
	{
		$current_screen->action = strtolower($_REQUEST['action']);
	}
	
	return $current_screen;
}

/**
 * returns the originally queried object id
 * 
 * @TODO Function should return this value as an array, which would include the object type
 *
 * @return unknown
 */
function twc_get_object_id()
{
	global $twc_menu_item_object_id;
	return $twc_menu_item_object_id;
}

/**
 * Returns the widget data based off of a slug
 * 
 * @TODO RENAME to twc_get_widget_by_id
 *
 * @param unknown_type $slug
 * @return unknown
 */
function twc_get_widget_from_slug( $widget_id )
{
	global $wp_registered_widgets, $wp_registered_widget_controls;
	
	if (!isset($wp_registered_widgets[$widget_id]) && !(isset($_REQUEST['addnew']) && $_REQUEST['addnew'])) 
		return false;
	
	//initializing identification
	$id_base = (isset($_REQUEST['base']) && !empty($_REQUEST['base']))?$_REQUEST['base']:(($widget_id)? _get_widget_id_base($widget_id):'');
	$number = (($widget_id)? (str_replace(array($id_base,'-'), '', $widget_id)):'');
	$number = (isset($_REQUEST['num'])&&!empty($_REQUEST['num']))? (int)trim($_REQUEST['num']):$number;
	
	
	if (!$widget = $wp_registered_widgets[$widget_id])
	{
		if (!$widget = $wp_registered_widgets[$id_base.'-1'])
		{
			if (!$widget = $wp_registered_widgets[$id_base.'-2'])
			{
				if (!$widget = $wp_registered_widgets[$widget_id])
				{
					$widget = $wp_registered_widgets[$widget_id];
				}
			}
		}
	}
	
	$widget['id_base'] = $id_base;
	$widget['p'] = array();
	$widget['multiwidget'] = false;
	$widget['number'] = false;
	if ($number)
	{
		$widget['multiwidget'] = true;
		$widget['number'] = $number;
	}
	
	if (is_callable($widget['callback']) || isset($_REQUEST['addnew']) && $_REQUEST['addnew'])
	{
		$widget['id'] = $widget_id;
		$widget['callback'] = $wp_registered_widget_controls[$widget['id']]['callback'];
		$widget['params'][0]['number'] = $number;
		
		if (is_object($widget['callback'][0]))
		{
			$widget['callback'][0]->number = $widget['number'];
			$widget['callback'][0]->id = $widget['id'];
		}
	}
	
	if (is_callable( array($widget['callback'][0], 'get_settings') ))
	{
		$params = $widget['callback'][0]->get_settings();
		$widget['p'] = $params[$widget['number']];
	}
	elseif (isset($wp_registered_widget_controls[$widget_id]['params'])
	&& !empty($wp_registered_widget_controls[$widget_id]['params']))
	{
		$widget['p'] = $params;
	}
	elseif (!$widget['multiwidget'])
	{
		$singles = get_option('twc_single_widget_data', array());
		if (isset($singles[$widget['id']])) $widget['p'] = $singles[$widget['id']];
	}
	
	return $widget; 
}

/**
 * Function is responsible for branding wordpress a little :)
 *
 * @param unknown_type $text
 * @return unknown
 */
function twc_gettext( $text )
{
	//initializing variables
	$current_screen = twc_get_current_screen(); 
	$base = $current_screen->parent_base;
	
	$lan = array(
		'themes' => array(
			'Screen Options' => '5Twenty Studios'
		),
	);
	
	if (isset($lan[$base]) && isset($lan[$base][$text]))
	{
		$text = $lan[$base][$text];
	}
	
	return $text;
}

/**
 * function returns the position and the sidebar for a given widget
 * 
 * @TODO Need to create a separate function that will be called to return the position value
 * @TODO This function should only return a sidebar as a value, if the type is sidebar
 * 
 * @param unknown_type $widget_id
 * @param unknown_type $type
 * @return unknown
 */
function twc_get_widgets_sidebar( $widget_id, $type = 'sidebar' )
{
	//initializing variables
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	static $flipped;
	
	if (!isset($flipped))
	{
		unset($sidebars_widgets['wp_inactive_widgets']);
		foreach ((array)$sidebars_widgets as $_sidebar => $widgets)
		{
			foreach ((array)$widgets as $position => $widgetid)
			{
				$flipped[$widgetid] = array(
						'sidebar' => $_sidebar,
						'position' => (int)trim($position),
					);
			}
		}
	}
	
	if (!isset($flipped[$widget_id])) return 'wp_inactive_widgets';
	if (!isset($flipped[$widget_id][$type])) return 'wp_inactive_widgets';
	if ($type == 'position' && !is_int($flipped[$widget_id][$type])) return 0;
	if ($type == 'sidebar' && empty($flipped[$widget_id][$type])) return 'wp_inactive_widgets';
	
	$sidebar_id = $flipped[$widget_id][$type];
	return $sidebar_id;
}

/**
 * this function adds the 'add new' button to the list page
 *
 */
function twc_h2_add_new()
{
	//initializing variables
	$current_screen = twc_get_current_screen();
	if ($current_screen->action != '') return;
	
	echo '<a href="widgets.php?action=add" class="button add-new-h2">Add New</a>';
}

/**
 * This does our checking to see if the current view is for active of 
 * inactive widgets
 *
 * @return unknown
 */
function twc_inactive_list()
{
	return (isset($_REQUEST['inactive']) && $_REQUEST['inactive'] == 'inactive');
}

/**
 * This initializes the twc system with the proper hooks.
 *
 * @return null
 */
function twc_initialize()
{
	add_action('widgets_init', 'init_registered_widgets', 1);
	add_action('sidebar_admin_setup', 'twc_init', 100);
	add_action('init', 'show_ajax', 100);
	add_action('admin_notices', 'twc_view_switch', 1);
	add_action('init', 'twc_add_javascript');
	add_action('twc_init', 'twc_admin_notices');
	add_action('twc_init', 'twc_view_widget_wrap', 20);
	add_action('twc_init', 'twc_destruct', 100);
	add_action('twc_display_admin', 'twc_view_auth');
	add_action('twc_display_admin', 'twc_register');
	add_action('twc-register', 'twc_register');
	add_filter('gettext', 'twc_gettext');
	add_filter('plugin_action_links_total-widget-control/index.php', 'twc_add_action_links');
	add_filter('plugin_row_meta', 'twc_plugin_row_meta', 10, 2);
	add_filter('init', 'twc_registration', 1000);
	add_filter('init', 'twc_receive_license', 1000);
	
	function twc_register(){ twc_show_view('twc-register'); }
	function twc_init(){ if (!twc_list_style_twc()) return; do_action('twc_init'); }
	function twc_destruct(){ _520(); exit(); }
	function twc_admin_notices(){ add_action('admin_notices', 'read_520_rss'); }
	function twc_view_screen_options(){ return twc_get_show_view('twc-screen-options'); }
	function twc_view_help_home(){ return twc_get_show_view('twc-help-home'); }
	function twc_view_help_edit(){ return twc_get_show_view('twc-help-edit'); }
	function twc_view_help_add(){ return twc_get_show_view('twc-help-add'); }
	function twc_view_widget_wrap(){ twc_show_view('twc-widget-wrap'); }
	function twc_view_auth(){ twc_show_view('twc-auth'); }
	if (!array_key_exists('TWCAUTH', $GLOBALS)) $GLOBALS['TWCAUTH'] = true;
	
}

/**
 * This function manages the view style. Upon first call it will check
 * which view is to be used.
 *
 * @return unknown
 */
function twc_list_style_twc()
{
	//initializing variables
	static $view;
	global $current_user;
	get_currentuserinfo();
	
	if (!isset($view) && $style = $_REQUEST['list_style']) //querystring
	{
		$view = $style;
	}
	elseif (!isset($view)) //db
	{
		$view = get_user_option( 'twc_list_style', $current_user->ID );
	}
	else //already set
	{
		return ($view == 'twc');
	}
	
	update_user_option($current_user->ID, 'twc_list_style', $view);
	return ($view == 'twc');
}

/**
 * function is responsible for adjusting the plugin meta links on the plugin page
 *
 * @param array $plugin_meta
 * @param string $plugin_file
 */
function twc_plugin_row_meta( $plugin_meta, $plugin_file )
{
	//reasons to fail
	if ('total-widget-control/index.php' != $plugin_file) return $plugin_meta;
	
	//initializing variables
	if (isset($plugin_meta[2])) unset($plugin_meta[2]);
	$plugin_meta[] = '<a href="http://community.5twentystudios.com/?kb" target="_blank">Documentation</a>';
	$plugin_meta[] = '<a href="http://community.5twentystudios.com/community/" target="_blank">Support Forum</a>';
	$plugin_meta[] = '<a href="http://community.5twentystudios.com/software-products/total-widget-control/extra-widgets/" target="_blank">Get More Widgets</a>';
	
	return $plugin_meta;
}

/**
 * Function returns a drop down of the current widgets position
 *
 * @param unknown_type $sidebar_id
 * @param unknown_type $default
 */
function twc_position_select_box( $sidebar_id, $default )
{
	//initializing variables
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	$list = $sidebars_widgets[$sidebar_id];
	
	$select = '<select id="'.$sidebar_id.'_position'.'" name="'.$sidebar_id.'_position'.'" class="twc_sidebar_select_box">';
	
	if (count($list) == 0)
	{
		$select .= "<option value='0'>0</option>";
	}
	
	for ($i = 1; $i <= count($list); $i++)
	{
		$selected = '';
		if ($default == ($i-1)) $selected = 'selected=selected';
		$select .= "<option $selected value='".($i-1)."'>$i</option>";
	}
	return $select.'</select>';
}

/**
 * function creates the wrapper folder and returns a list of files
 * within that folder.
 * 
 * @TODO Function should also copy the default wrappers to the created folder
 *
 * @return unknown
 */
function twc_read_wrapper_files()
{
	//initializing variables
	$path = TwcPath::clean(get_theme_path().'/widget-wrappers/');
	if (!is_dir($path))
	{
		TwcPath::create($path, 0777);
	}
	
	$files = TwcPath::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'));
	$headers = array(
		'wrapperTitle' => 'Wrapper Title',
		'description' => 'Description',
	);
	
	foreach ($files as $file)
	{
		$file_data[$file] = get_file_data($path.$file, $headers);
	}
	
	return $file_data;
}

/**
 * This takes care of the alternating row colors without all the messy code
 * in my table rows
 */
function twc_row_alternate()
{
	//initializing variables
	static $alternate;
	
	if (!isset($alternate))
	{ 
		$alternate = 0;
	}
	else
	{
		$alternate++;
	}
	
	if ($alternate)
	{
		$alternate = -1;
		echo 'alternate';
	}
}

/**
 * registers the inactive widgets sidebar
 * 
 * @return null
 */
function twc_register_placeholder_sidebar()
{
	// register the inactive_widgets area as sidebar
	register_sidebar(array(
		'name' => __('Inactive Widgets'),
		'id' => 'wp_inactive_widgets',
		'description' => '',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));
}

/**
 * function is responsible for pinging for a license and redirecting if necessary
 *
 * @return unknown
 */
function twc_registration()
{
	//loading libraries
	require_once ABSPATH.'wp-admin'.DS.'includes'.DS.'plugin.php';
	
	//initializing variables
	$current_screen = twc_get_current_screen();
	$first = get_option('twc_unique_registration_key', true);
	$uniqueID = get_option('twc_unique_registration_key', create_guid());
	update_option('twc_unique_registration_key', $uniqueID);
	
	//reasons to fail
	if ($current_screen->action != 'register') return false;
	if (isset($_REQUEST[$uniqueID]) || !isset($_REQUEST['license'])) return false;
	
	//initializing variables
	$headers = get_plugin_data( dirname(__file__).DS.'index.php' );
	$domain = 'http://'.str_replace('http://', '', $_SERVER['HTTP_HOST']);
	$file_path = plugin_dir_path(dirname(__file__)).$parts['host'];
	
	switch($_REQUEST['license'])
	{
		case '1': case '2': $type = 'twc-pro'; break;
		default: $type = 'twc-free'; break;
	}
	
	$path = "http://community.5twentystudios.com/?view=register-for-free&email=".
		get_bloginfo('admin_email')."&ver=".urlencode($headers['Version']).
		"&domain=".urlencode($domain)."&type=$type&unique=$uniqueID";
	
	if (ini_get('allow_url_fopen') && $result = @file_get_contents($path))
	{
		
	}
	else 
	{
		$curl = curl_init($path);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
	    $result = curl_exec($curl);
	    curl_close($curl);
	}
	if (trim($result))
	{
		$result = trim(str_replace("\r\n",'', $result));
		header('Location: '.$result);
		exit();
	}
	return false;
}

/**
 * function is responsible for saving the license file if its sent
 *
 * @return false
 */
function twc_receive_license()
{
	//initializing variables
	$uniqueID = get_option('twc_unique_registration_key', false);
	$domain = 'http://'.str_replace('http://', '', $_SERVER['HTTP_HOST']);
	$parts = parse_url($domain);
	$file_path = dirname(__file__).DS.$parts['host'];
	
	if (!isset($_REQUEST[$uniqueID])) return false;
	
	@file_put_contents($file_path, $_REQUEST[$uniqueID]);
	return false;
}

/**
 * Function appends the widget wrapper values to the instance fields
 *
 * 
 * @param array $fields
 * @return array
 */
function twc_save_default_sidebar( $fields )
{
	if (array_key_exists('twcp_default_sidebar', $_REQUEST))
		$fields['twcp_default_sidebar'] = $_REQUEST['twcp_default_sidebar'];
	
	if (array_key_exists('twcp_exclude_sidebar', $_REQUEST))
		$fields['twcp_exclude_sidebar'] = $_REQUEST['twcp_exclude_sidebar'];
	
	if (array_key_exists('twcp_inherit_sidebar', $_REQUEST))
		$fields['twcp_inherit_sidebar'] = $_REQUEST['twcp_inherit_sidebar'];
	
	if (array_key_exists('twcp_wrapper_file', $_REQUEST))
		$fields['twcp_wrapper_file'] = $_REQUEST['twcp_wrapper_file'];
	
	if (array_key_exists('twcp_widget_title', $_REQUEST))
		$fields['twcp_widget_title'] = $_REQUEST['twcp_widget_title'];
	
	return $fields;
}

/**
 * Function appends the menu item data to the instance fields
 * 
 * @TODO Need to also save the object type
 * @param unknown_type $fields
 * @return unknown
 */
function twc_save_menu_items( $fields )
{
	//reasons to fail
	if (!array_key_exists('menu-item', $_REQUEST)) return $fields;
	
	foreach ((array)$_REQUEST['menu-item'] as $item) 
		foreach ((array)$item as $menu_item_object_id => $id)
		{
			if (strlen($id) > 100) continue;
			if ($menu_item_object_id != 'menu-item-object-id' && 'menu-item-object' != $menu_item_object_id) 
				continue;
			$object_ids[$id] = $id;
		}
	
	$fields['menu_item_object_id'] = $object_ids;
	return $fields;
}

/**
 * Function accepts the widget id and the sidebar and saves the 
 * relationship.
 *
 * @param string $widget_id
 * @param string $sidebar_id
 */
function twc_save_widget_sidebar( $widget_id, $sidebar_id, $position = 0, $delete = false )
{
	if (!$widget_id || !current_user_can('activate_plugins'))
		return false;
	
	//initializing variables
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	
	// remove old position
	foreach ( $sidebars_widgets as $key => $sb )
	{
		$sidebars_widgets[$key] = array_diff( (array)$sb, array($widget_id) );
	}
	
	array_splice( $sidebars_widgets[$sidebar_id], $position, 0, $widget_id );
	wp_set_sidebars_widgets($sidebars_widgets);
}

/**
 * This function accepts fields for a given widget and saves those fields to the db.
 *
 * @param unknown_type $widget_id
 * @param unknown_type $fields
 */

function twc_save_widget_fields( $widget_id, $post )
{
	//initializing variables
	if (!$widget_id || !current_user_can('activate_plugins'))
	{
		wp_redirect('widgets.php?message=0');
		exit;
	}
	
	//initializing variables
	global $wp_registered_widget_updates;
	$fields = $post;
	$_POST = array();
	$widget = twc_get_widget_from_slug($widget_id);
	$id_base = _get_widget_id_base($widget['id']);
	$ignore = array('sidebar_slug','id_base','widget-width','widget-height',
	'widget_number','multi_number','action','redirect','editwidget','addnew','base','num',
	'view');
	
	
	if ($widget['multiwidget'])
	{
		//variable as constructed by wordpress
		if (isset($post['widget-'.$id_base])
		&& isset($post['widget-'.$id_base][$widget['number']]))
		{
			$fields = $post['widget-'.$id_base][$widget['number']];
		}
	}
	
	//filtering the fields
	foreach ($fields as $key => $value)
	{
		if (in_array($key, (array)$ignore))
		{
			unset($fields[$key]);
		}
	}
	
	if ($widget['multiwidget'])
	{
		$_POST['widget-'.$id_base][$widget['number']] = apply_filters('twc-save-widget-fields', $fields);
		$_POST['multi_number'] = $widget['number'];
	}
	else
	{
		$_POST = apply_filters('twc-save-widget-fields', $fields);
		
		//take the data handling into our own hands if this is not a multiwidget
		$singles = get_option('twc_single_widget_data', array());
		$singles[$widget['id']] = $_POST;
		update_option('twc_single_widget_data', $singles);
	}
	
	//this calls the widgets update function with the given params
	foreach ( (array) $wp_registered_widget_updates as $name => $control )
	{
		if ( $name != $id_base || !is_callable($control['callback']) )
			continue;
	
		ob_start();
			call_user_func_array( $control['callback'], $control['params'] );
		ob_get_clean();
		break;
	}
	return true;
}

/**
 * 
 *
 * @return unknown
 */
function twc_search_list_for()
{
	if (!isset($_REQUEST['twc_filter_widgets'])) return false;
	$_REQUEST['inactive'] = 'active';
	
	return $_REQUEST['twc_filter_widgets'];
}

/**
 * Save the original object id
 * 
 * @TODO I need to also locate and remember the object type
 *
 * @param unknown_type $wp
 */
function twc_set_object_id( &$wp )
{
	//initializing variables
	global $twc_menu_item_object_id, $wp_query;
	$twc_menu_item_object_id = $wp_query->queried_object_id;
	
	if (is_null($wp_query->queried_object_id))
	{
		$wp_query->get_queried_object();
		$twc_menu_item_object_id = $wp_query->queried_object_id;
	}
	
}

/**
 * Just prints a sidebar
 *
 * @param unknown_type $default
 * @return unknown
 */
function twc_sidebar_select_box( $default = 'wp_inactive_widgets', $widget = null, $format = false, $name = null )
{
	//initializing variables
	global $wp_registered_sidebars;
	$sidebars = $wp_registered_sidebars;
	$class = array('','selected="true"');
	$select_filter = '';
	
	if (!is_null($name))
	{
		$id = 'twc_sidebar_select_box_'.$widget['id'];
	}
	elseif ($format)
	{
		$id = 'twc_sidebar_select_box_'.$widget['id'];
		$name = 'sidebar';
	}
	elseif (!is_null($widget))
	{
		$id = 'twc_sidebar_select_box_'.$widget['id'];
		$name = 'widget-'.$widget['id_base'].'['.$widget['number'].'][sidebar_slug]';
	}
	else 
	{
		$select_filter = '<option value="">  -  Remove Filter  -  </option>';
		$default = twc_filter_list_for();
		$id = $name = 'twc_filter_widgets';
	}
	
	$select = '<select id="'.$id.'" name="'.$name.'" class="twc_sidebar_select_box">'.$select_filter;
	
	foreach ($sidebars as $slug => $sidebar)
	{
		$selected = 0;
		if ($default == $slug) $selected = 1;
		$select .= "<option {$class[$selected]} value='$slug'>{$sidebar['name']}</option>";
	}
	
	return $select.'</select>';
}

/**
 * function is responsible for putting the widget back together.
 * This function also keeps track of the widget loop within the dynamic_sidebar.
 * Once the dynamic_sidebar function is completed, we declare a do_action to
 * activate our own sidebar function.
 *
 * @param unknown_type $widget
 */
function twc_trigger_sidebar( $widget_shell )
{
	//initializing variables
	global $wp_registered_widgets, $twc_wp_registered_widgets;
	static $keeping_count;
	$sidebar_id = twc_get_widgets_sidebar($widget_shell['id']);
	$count = twc_count_sidebar_widgets($sidebar_id);
	
	if (!isset($keeping_count))
	{
		$keeping_count = 0;
	}
	$keeping_count++;
	
	if ($count == $keeping_count)
	{
		//ensures that we have a well formed widget variable before calling our sidebar
		$wp_registered_widgets = $twc_wp_registered_widgets;
		do_action('twc_dynamic_sidebar', $sidebar_id);
		
		$keeping_count = 0;
	}
	
}

/**
 * This function adds the view switches to the wordpress widget page
 *
 * @return unknown
 */
function twc_view_switch()
{
	//initializing variables
	$current_screen = twc_get_current_screen();
	
	//reasons to fail
	if ((!$GLOBALS['TWCAUTH']) && (twc_list_style_twc() 
	|| $current_screen->parent_base != 'widgets')) return false;
	
	echo '<div>'.twc_get_show_view('twc-view-switch').'</div>';
	
}

/**
 * Function sets the global $widget variable
 *
 * @return null
 */
function twc_widget_global()
{
	//initializingn variables
	global $widget;
	$widget_id = (isset($_REQUEST['widget_id']))? $_REQUEST['widget_id']: ((isset($_REQUEST['widget-id']))? $_REQUEST['widget-id'] : ((isset($_REQUEST['editwidget']))? $_REQUEST['editwidget']: false));
	
	//loading resources
	$widget = twc_get_widget_from_slug( $widget_id );
	
	//reasons to return
	if (!$widget) return;
	
}

/**
 * The default functions do not account for additional saved fields. 
 * so we do that here.
 * 
 * @TODO Can we merge the function twc_save_default_sidebar into this function?
 *
 * @param unknown_type $instance
 * @param unknown_type $new_instance
 * @param unknown_type $old_instance
 * @param unknown_type $this
 * @return unknown
function twc_widget_update_callback($instance, $new_instance, $old_instance, $this)
{
	//we're just worried about our custom fields
	$instance = wp_parse_args( $instance, $new_instance );
	
	return $instance;
}
 */

/**
 * Function returns the widgets grouped by sidebar
 *
 * @return unknown
 */
function twc_wp_get_sidebars_widgets()
{
	$sidebars_widgets = wp_get_sidebars_widgets();
	if ( empty( $sidebars_widgets ) )
		$sidebars_widgets = wp_get_widget_defaults();
	
	return $sidebars_widgets;
}

/**
 * wraps the widget display
 *
 * @param unknown_type $display
 * @param unknown_type $widget
 * @return unknown
 */
function twcp_widget_wrapper( $display, $widget )
{
	//initializing variables
	$path = TwcPath::clean(get_theme_path().'/widget-wrappers/');
	$hasWrapper = (!isset($widget['p']['twcp_wrapper_file']) || !trim($widget['p']['twcp_wrapper_file']));
	$wrapper = twc_find(array($path), @$widget['p']['twcp_wrapper_file']);
	
	//reasons to return
	if (!$hasWrapper || !$wrapper || !is_file($wrapper) || !file_exists($wrapper)) 
		return $display;
	
	ob_start();
	require $wrapper;
	return ob_get_clean();
}	



