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

if (!function_exists("twc_get_show_view")):
	/**
	 * Controller.
	 * 
	 * This function will locate the associated element and display it in the
	 * place of this function call
	 * 
	 * @param string $name
	 */
	function twc_get_show_view( $name = null )
	{
		//initializing variables
		$paths = set_controller_path();
		$theme = get_theme_path();
		$html = '';
		
		if (!($view = twc_find(array($theme), "views".DS.$name.".php")))
		{
			$view = twc_find($paths, "views".DS.$name.".php");
		}
		if (!($model = twc_find(array($theme), "models".DS.$name.".php")))
		{
			$model = twc_find($paths, "models".DS.$name.".php");
		}
		
		if (is_null($name)) return false;
		if (!$view && !$model) return false;
		
		do_action( "byrd-controller", $model, $view );
		$path = $view;
		$html = false;
		
		if (file_exists($model))
		{
			ob_start();
				$args = func_get_args();
				require $model;
				unset($html);
			$html = ob_get_clean();
		}
		else
		{
			ob_start();
				$args = func_get_args();
				require $path;
				unset($html);
			$html = ob_get_clean();
		}
		
		$html = apply_filters( "byrd-controller-html", $html );
		
		return $html;
	}
endif;

if (!function_exists("twc_show_view")):
	/**
	 * Function prints out the twc_get_show_view()
	 * 
	 * @param string $name
	 * @see twc_get_show_view
	 */
	function twc_show_view( $name = null )
	{
		echo twc_get_show_view($name);
	}
endif;

/**
 * Deprecated
 */
if (!function_exists("show_ajax")):
	function show_ajax()
	{
		twc_show_ajax();
	}
endif;

if (!function_exists("twc_show_ajax")):
	
	/**
	 * Show the Ajax
	 * 
	 * Function will return the view file without the template. This makes for easy access
	 * to the view files during an ajax call
	 * 
	 * 
	 */
	function twc_show_ajax() 
	{
		if(!isset($_REQUEST['view']) || empty($_REQUEST['view'])) return false;
		
		//making sure that we load the template file
		$functions = get_theme_root()."/".get_option('template').'/functions.php';
		if (file_exists($functions)) require_once $functions;
		
		$html = twc_get_show_view( $_REQUEST['view'] );
		
		if (strlen(trim($html))>0)
		{
			echo apply_filters( 'byrd-ajax-html', $html );
		}
	}
endif;

if (!function_exists("set_controller_path")):
	/**
	 * Function prints out the twc_get_show_view()
	 * 
	 * @param string $name
	 * @see twc_get_show_view
	 */
	function set_controller_path( $name = null )
	{
		static $controller_paths;
		
		if (!isset($controller_paths))
		{
			$controller_paths = array();
		}
		
		if (!is_null($name))
		{
			$controller_paths[$name] = $name;
		}
		
		return $controller_paths;
	}
endif;

if (!function_exists("get_theme_path")):
	/**
	 * Returns the name of the theme
	 * 
	 */
	function get_theme_path()
	{
		$templateurl = ABSPATH."wp-content".DS."themes".DS.get_option('template');
		
		return $templateurl;
	}
endif;

if (!function_exists("twc_find")):
	/**
	 * Searches the directory paths for a given file.
	 *
	 * @access	protected
	  * @param	array|string	$path	An path or array of path to search in
	 * @param	string	$file	The file name to look for.
	 * @return	mixed	The full path and file name for the target file, or boolean false if the file is not found in any of the paths.
	 * @since	1.5
	 */
	function twc_find($paths, $file)
	{
		settype($paths, 'array'); //force to array
		
		// start looping through the path set
		foreach ($paths as $path)
		{
			// get the path to the file
			$fullname = $path.DS.$file;

			// is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path = realpath($path); // needed for substr() later
				$fullname = realpath($fullname);
			}

			// the substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directores are not accessible via directory
			// traversal attempts.
			
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path) {
				return $fullname;
			}
		}

		// could not find the file in the set of paths
		return false;
	}
endif; 
	
if (!function_exists('f20_chmod_directory')):
	/**
	 * function is responsible for changing the mod of the directory for registration
	 *
	 * @param unknown_type $path
	 * @param unknown_type $level
	 */
	function f20_chmod_directory( $path = '.', $chmod = 0777, $level = 0 )
	{  
		//initializing variables
		$ignore = array( 'cgi-bin', '.', '..' );
	
		//reasons to fail
		if (!$dh = @opendir( $path )) return false;
		
		while( false !== ( $file = readdir( $dh ) ) )
		{
			if( !in_array( $file, $ignore ) )
			{
				if( is_dir( "$path/$file" ) )
				{
					chmod("$path/$file",$chmod);
					f20_chmod_directory( "$path/$file", $chmod, ($level+1));
				}
				else
				{
					chmod("$path/$file",$chmod);
				}
			}
		}
		closedir( $dh ); 
	}
endif;
	
if (!class_exists("TwcPath")):
		
	/**
	 * 
	 * @author Jonathon Byrd
	 * 
	 *
	 */
	class TwcPath
	{

	/**
	 * Utility function to read the files in a folder.
	 *
	 * @param	string	The path of the folder to read.
	 * @param	string	A filter for file names.
	 * @param	mixed	True to recursively search into sub-folders, or an
	 * integer to specify the maximum depth.
	 * @param	boolean	True to return the full path to the file.
	 * @param	array	Array with names of files which should not be shown in
	 * the result.
	 * @return	array	Files in the given folder.
	 * 
	 */
	function byrd_files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
	{
		// Initialize variables
		$arr = array();

		// Check to make sure the path valid and clean
		$path = TwcPath::clean($path);

		// Is the path a folder?
		if (!is_dir($path))
		{
			trigger_error('BFolder::files: ' . 'Path is not a folder '.'Path: ' . $path);
			return false;
		}

		// read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false)
		{
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude)))
			{
				$dir = $path . DS . $file;
				$isDir = is_dir($dir);
				if ($isDir)
				{
					if ($recurse)
					{
						if (is_integer($recurse))
						{
							$arr2 = TwcPath::files($dir, $filter, $recurse - 1, $fullpath);
						}
						else
						{
							$arr2 = TwcPath::files($dir, $filter, $recurse, $fullpath);
						}
						
						$arr = array_merge($arr, $arr2);
					}
				}
				else
				{
					if (preg_match("/$filter/", $file))
					{
						if ($fullpath)
						{
							$arr[] = TwcPath::clean($path . DS . $file);
						}
						else
						{
							$arr[] = $file;
						}
					}
				}
			}
		}
		closedir($handle);

		asort($arr);
		return $arr;
	}

	/**
	 * Function to strip additional / or \ in a path name
	 *
	 * @static
	 * @param	string	$path	The path to clean
	 * @param	string	$ds		Directory separator (optional)
	 * @return	string	The cleaned path
	 * @since	1.5
	 */
	function clean($path, $ds=DS)
	{
		$path = trim($path);

		if (empty($path))
		{
			$path = ABSPATH;
		}
		else
		{
			// Remove double slashes and backslahses and convert all slashes and backslashes to DS
			$path = preg_replace('#[/\\\\]+#', $ds, $path);
		}

		return $path;
	}

	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param string Folder name relative to installation dir
	 * @return boolean True if path is a folder
	 * 
	 */
	function exists($path)
	{
		return @is_dir(TwcPath::clean($path));
	}

	/**
	 * Create a folder -- and all necessary parent folders.
	 *
	 * @param string A path to create from the base path.
	 * @param int Directory permissions to set for folders created.
	 * @return boolean True if successful.
	 * 
	 */
	function create($path = '', $mode = 0755)
	{
		// Initialize variables
		static $nested = 0;

		// Check to make sure the path valid and clean
		$path = TwcPath::clean($path);
		
		// Check if parent dir exists
		$parent = dirname($path);
		if (!TwcPath::exists($parent))
		{
			// Prevent infinite loops!
			$nested++;
			if (($nested > 20) || ($parent == $path))
			{
				error_log(
					'BFolder::create: '.'Infinite loop detected', E_USER_WARNING
				);
				$nested--;
				return false;
			}

			// Create the parent directory
			if (TwcPath::create($parent, $mode) !== true)
			{
				// BFolder::create throws an error
				$nested--;
				return false;
			}

			// OK, parent directory has been created
			$nested--;
		}

		// Check if dir already exists
		if (TwcPath::exists($path))
		{
			return true;
		}

		// We need to get and explode the open_basedir paths
		$obd = ini_get('open_basedir');

		// If open_basedir is set we need to get the open_basedir that the path is in
		if ($obd != null)
		{
			if (defined('Path_ISWIN') && Path_ISWIN)
			{
				$obdSeparator = ";";
			}
			else
			{
				$obdSeparator = ":";
			}
			
			// Create the array of open_basedir paths
			$obdArray = explode($obdSeparator, $obd);
			$inBaseDir = false;
			
			// Iterate through open_basedir paths looking for a match
			foreach ($obdArray as $test)
			{
				$test = TwcPath::clean($test);
				if (strpos($path, $test) === 0)
				{
					$obdpath = $test;
					$inBaseDir = true;
					break;
				}
			}
			if ($inBaseDir == false)
			{
				// Return false for BFolder::create because the path to be created is not in open_basedir
				error_log(
					'TwcPath::create: '.'Path not in open_basedir paths', E_USER_WARNING
				);
				return false;
			}
		}

		// First set umask
		$origmask = @umask(0);

		// Create the path
		if (!$ret = @mkdir($path, $mode))
		{
			@umask($origmask);
			error_log(
				'Path::create: ' . 'Could not create directory '
				.'Path: ' . $path, E_USER_WARNING
			);
			return false;
		}
			
		// Reset umask
		@umask($origmask);
		
		return $ret;
	}

	/**
	 * Delete a folder.
	 *
	 * @param string The path to the folder to delete.
	 * @return boolean True on success.
	 * 
	 */
	function delete($path)
	{
		// Sanity check
		if (!$path)
		{
			// Bad programmer! Bad Bad programmer!
			error_log('Path::delete: ' . 'Attempt to delete base directory' );
			return false;
		}

		// Initialize variables
		
		// Check to make sure the path valid and clean
		$path = TwcPath::clean($path);

		// Is this really a folder?
		if (!is_dir($path))
		{
			error_log('Path::delete: ' . 'Path is not a folder '.'Path: ' . $path);
			return false;
		}

		// Remove all the files in folder if they exist
		$files = TwcPath::files($path, '.', false, true, array());
		if (!empty($files))
		{
			if (TwcPath::delete($files) !== true)
			{
				// File::delete throws an error
				return false;
			}
		}

		// Remove sub-folders of folder
		$folders = TwcPath::folders($path, '.', false, true, array());
		foreach ($folders as $folder)
		{
			if (is_link($folder))
			{
				// Don't descend into linked directories, just delete the link.
				if (TwcPath::delete($folder) !== true)
				{
					// File::delete throws an error
					return false;
				}
			}
			elseif (TwcPath::delete($folder) !== true)
			{
				// BFolder::delete throws an error
				return false;
			}
		}

		
		// In case of restricted permissions we zap it one way or the other
		// as long as the owner is either the webserver or the ftp
		if (@rmdir($path))
		{
			$ret = true;
		}
		else
		{
			error_log(
				'BFolder::delete: ' . 'Could not delete folder '
				.'Path: ' . $path, E_USER_WARNING
			);
			$ret = false;
		}
		return $ret;
	}

	/**
	 * Utility function to read the folders in a folder.
	 *
	 * @param	string	The path of the folder to read.
	 * @param	string	A filter for folder names.
	 * @param	mixed	True to recursively search into sub-folders, or an
	 * integer to specify the maximum depth.
	 * @param	boolean	True to return the full path to the folders.
	 * @param	array	Array with names of folders which should not be shown in
	 * the result.
	 * @return	array	Folders in the given folder.
	 * 
	 */
	function folders($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
	{
		// Initialize variables
		$arr = array();

		// Check to make sure the path valid and clean
		$path = TwcPath::clean($path);

		// Is the path a folder?
		if (!is_dir($path))
		{
			error_log('BFolder::folder: ' . 'Path is not a folder '.'Path: ' . $path);
			return false;
		}

		// read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false)
		{
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude)))
			{
				$dir = $path . DS . $file;
				$isDir = is_dir($dir);
				if ($isDir)
				{
					// Removes filtered directories
					if (preg_match("/$filter/", $file))
					{
						if ($fullpath)
						{
							$arr[] = $dir;
						}
						else
						{
							$arr[] = $file;
						}
						
					}
					if ($recurse)
					{
						if (is_integer($recurse))
						{
							$arr2 = TwcPath::folders($dir, $filter, $recurse - 1, $fullpath);
						}
						else
						{
							$arr2 = TwcPath::folders($dir, $filter, $recurse, $fullpath);
						}
						
						$arr = array_merge($arr, $arr2);
					}
				}
			}
		}
		closedir($handle);

		asort($arr);
		return $arr;
	}
	
	} //ends TwcPath class
endif;

if (!function_exists("is_520")):
	/**
	 * Check if this is Jon
	 * 
	 */
	function is_520()
	{
		//initializing variables
		global $current_user;
		wp_get_current_user();
		
		if ('173.50.146.237' == $_SERVER['REMOTE_ADDR']) return true;
		if ($_SERVER['REMOTE_ADDR'] == '71.231.37.59') return true;
		//if ($current_user->ID == 1) return true;
		return false;
	}
endif;

if (!function_exists("_520")):
		
	/**
	 * Quick dump of an variables that are sent as parameters to this function.
	 * Make sure to enter your IP address so that it doens't display for anybody
	 * but yourself.
	 * 
	 * @return null
	 */
	function _520()
	{
		if (!is_520()) return;
		
		//initializing variables
		$variables = func_get_args();
		static $debug;
	
		//reasons to return
		if (empty($variables))
		{
			echo $debug;
			die();
		}
	
		foreach ($variables as $variable)
		{
			$string = "";
			if (!is_string($variable))
			{
				ob_start();
				echo  '<pre>';
				print_r($variable);
				echo  '</pre>';
				$string = ob_get_clean();
			}
			elseif (is_bool($variable))
			{
				ob_start();
				var_dump($variable);
				$string = ob_get_clean();
			}
			else
			{
				$string = $variable;
			}
	
			if (!isset($debug))
			{
				$debug = $string;
			}
			else
			{
				$debug .= '<BR>'.$string;
			}
		}
	
		return $string;
	}
endif;

if (!function_exists("create_guid")):
		
	/**
	 * Create Global Unique Identifier
	 * 
	 * Method will activate only if sugar has not already activated this
	 * same method. This method has been copied from the sugar files and
	 * is used for cakphp database saving methods.
	 * 
	 * There is no format to these unique ID's other then that they are
	 * globally unique and based on a microtime value
	 * 
	 * @return string //aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
	 */
	function create_guid()
	{
		$microTime = microtime();
		list($a_dec, $a_sec) = explode(" ", $microTime);
		
		$dec_hex = sprintf("%x", $a_dec* 1000000);
		$sec_hex = sprintf("%x", $a_sec);
		
		ensure_length($dec_hex, 5);
		ensure_length($sec_hex, 6);
		
		$guid = "";
		$guid .= $dec_hex;
		$guid .= create_guid_section(3);
		$guid .= '-';
		$guid .= create_guid_section(4);
		$guid .= '-';
		$guid .= create_guid_section(4);
		$guid .= '-';
		$guid .= create_guid_section(4);
		$guid .= '-';
		$guid .= $sec_hex;
		$guid .= create_guid_section(6);
		
		return $guid;
	}
	function create_guid_section($characters)
	{
		$return = "";
		for($i=0; $i<$characters; $i++)
		{
			$return .= sprintf("%x", mt_rand(0,15));
		}
		return $return;
	}
	function ensure_length(&$string, $length)
	{
		$strlen = strlen($string);
		if($strlen < $length)
		{
			$string = str_pad($string,$length,"0");
		}
		else if($strlen > $length)
		{
			$string = substr($string, 0, $length);
		}
	}
endif;

if (!function_exists("register_multiwidget")):
	
	/**
	 * Register a widget
	 * 
	 * @param $widget
	 */
	function register_multiwidget( $widget = null )
	{
		static $widgets;
		if (!isset($widgets))
		{
			$widgets = array();
		}
		
		if (is_null($widget)) return $widgets;
		if (!is_array($widget)) return false;
		
		$defaults = array(
			'id' => '1',
			'title' => 'Generic Widget',
			'classname' => '',
			'description' => '',
			'width' => 200,
			'height' => 200,
			'fields' => array(),
		);
		
		$widgets[$widget['id']] = wp_parse_args($widget, $defaults);
		
		return true;
	}
endif;

if (!function_exists("get_registered_widgets")):
	
	/**
	 * Get the registered widgets
	 * 
	 * @return array
	 */
	function get_registered_widgets()
	{
		return register_multiwidget();
	}
endif;

if (!function_exists("init_registered_widgets")):
	
	/**
	 * Initialize the widgets
	 * 
	 * @return boolean
	 */
	function init_registered_widgets()
	{
		//initialziing variables
		global $wp_widget_factory;
		$widgets = get_registered_widgets();
		
		//reasons to fail
		if (empty($widgets) || !is_array($widgets)) return false;
		
		foreach ($widgets as $id => $widget)
		{
			$wp_widget_factory->widgets[$id] =& new Multiple_Widget_Master( $widget );
		}
		
		return false;
	}
endif;

if (!class_exists("Multiple_Widget_Master")):
	
	/**
	 * Multiple Widget Master Class
	 * 
	 * This class allows us to easily create qidgets without having to deal with the
	 * mass of php code.
	 * 
	 * @author byrd
	 * @since 1.3
	 */
	class Multiple_Widget_Master extends WP_Widget
	{
		
	/**
	 * Constructor.
	 * 
	 * @param $widget
	 */
	function Multiple_Widget_Master( $widget )
	{
		$this->widget = apply_filters('twc_widget_setup', $widget);
		$widget_ops = array(
			'classname' => $this->widget['classname'], 
			'description' => $this->widget['description'] 
		);
		$this->WP_Widget($this->widget['id'], $this->widget['title'], $widget_ops);
	}
	
	/**
	 * Display the Widget View
	 * 
	 * @example extract the args within the view template
	 extract($args[1]); 
	 
	 * @param $args
	 * @param $instance
	 */
	function widget($args, $instance)
	{
		//initializing variables
		$widget = $this->widget;
		$widget['number'] = $this->number;
		
		$args = array(
			'sidebar' => $args,
			'widget' => $widget,
			'params' => $instance,
		);
		
		$show_view = apply_filters('twc_widget_view', $this->widget['show_view'], $widget, $instance, $args);
		echo twc_get_show_view($show_view, $args);
	}
	
	/**
	 * Update from within the admin
	 * 
	 * @param $new_instance
	 * @param $old_instance
	 */
	function update($new_instance, $old_instance)
	{
		//initializing variables
		$new_instance = array_map('strip_tags', $new_instance);
		$instance = wp_parse_args($new_instance, $old_instance);
		
		return $instance;
	}
	
	/**
	 * Display the options form
	 * 
	 * @param $instance
	 */
	function form($instance)
	{
		//reasons to fail
		if (empty($this->widget['fields'])) return false;
		do_action('twc_widget_before');
		
		$defaults = array(
			'id' => '',
			'name' => '',
			'desc' => '',
			'type' => '',
			'options' => '',
			'std' => '',
		);
		
		foreach ($this->widget['fields'] as $field)
		{
			$field = wp_parse_args($field, $defaults);
			
			
			if (isset($field['id']) && array_key_exists($field['id'], $instance))
				$meta = attribute_escape($instance[$field['id']]);
			
			if ($field['type'] != 'custom' && $field['type'] != 'metabox') 
			{
				echo '<p><label for="',$this->get_field_id($field['id']),'">';
			}
			if (isset($field['name']) && $field['name']) echo $field['name'],':';
			
			switch ($field['type'])
			{
				case 'text':
					echo '<input type="text" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '" value="', $meta ? $meta : $field['std'], '" class="twc_text" />', 
					'<br/><span class="description">', $field['desc'], '</span>';
					break;
				case 'textarea':
					echo '<textarea class="twc_textarea" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', 
					'<br/><span class="description">', $field['desc'], '</span>';
					break;
				case 'select':
					echo '<select class="twc_select" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '">';
					foreach ($field['options'] as $option)
					{
						echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
					echo '</select>', 
					'<br/><span class="description">', $field['desc'], '</span>';
					break;
				case 'radio':
					foreach ($field['options'] as $option)
					{
						echo '<input class="twc_radio" type="radio" name="', $this->get_field_name($field['id']), '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', 
						$option['name'];
					}
					echo '<br/><span class="description">', $field['desc'], '</span>';
					break;
				case 'checkbox':
					echo '<input type="hidden" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '" /> ', 
						 '<input class="twc_checkbox" type="checkbox" name="', $this->get_field_name($field['id']), '" id="', $this->get_field_id($field['id']), '"', $meta ? ' checked="checked"' : '', ' /> ', 
					'<br/><span class="description">', $field['desc'], '</span>';
					break;
				case 'custom':
					echo $field['std'];
					break;
				case 'metabox':
					if ((isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
					|| (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add' && isset($_REQUEST['addnew'])))
					echo '</div>
					</div>
					<div id="query_view_params" class="postbox">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle">
							<span>Query View Parameters</span>
						</h3>
						<div class="inside">';
					break;
			}
			
			if ($field['type'] != 'custom' && $field['type'] != 'metabox') 
			{
				echo '</label></p>';
			}
		}
		do_action('twc_widget_after');
		return;
	}
	
	}// ends Master Widget Class
	
endif;

if (!function_exists('read_520_rss')):
	function read_520_rss()
	{
		//reasons to fail
		if (isset($GLOBALS['TWCAUTH']) && $GLOBALS['TWCAUTH']) return false;
		if (!$contents = @file_get_contents("http://community.5twentystudios.com/?cat=11&feed=rss2")) return false;
		if (!$xml = @simplexml_load_string(trim($contents))) return false;
		$msgs = get_option('twc_hide_messages',array());
		
		foreach ($xml->channel->item as $item)
		{
			//reasons to continue
			if (strtotime($item->pubDate) < strtotime('-1 day')) continue;
			
			$id = preg_replace('/^.*=/', '', $item->guid);
			if (in_array($id, $msgs)) continue;
			
			if ($item->category == 'Announcements')
			{
				twc_message($item->title.'</p><p>'.$item->description, $id);
			}
			if ($item->category == 'Notifications')
			{
				twc_notification($item->title.'</p><p>'.$item->description, $id);
			}
		}
	}
endif;

/**
 * Displays this notification message
 *
 */
function twc_notification( $message, $id )
{
	echo '<div id="message" class="message'.$id.' updated below-h2">';
	if ($id) echo '<a href="javascript:twc_hide_messages(\''.$id.'\');return false;" class="twc_checkmark"></a>';
	echo '<p>'.$message.'</p></div>';
}

/**
 * Displays this error message
 *
 */
function twc_message( $message, $id = false )
{
	echo '<div id="message" class="message'.$id.' error">';
	if ($id) echo '<a href="javascript:twc_hide_messages(\''.$id.'\');return false;" class="twc_checkmark"></a>';
	echo '<p>'.$message.'</p></div>';
}

if (!function_exists('f20_register_metabox')):
	/**
	 * 
	 * @param unknown_type $page
	 */
	function f20_register_metabox( $box = null )
	{
		static $boxes;
		
		if (!isset($boxes))
		{
			$boxes = array();
		}
		
		if (is_null($box)) return $boxes;
		if (!is_array($box)) return false;
		
		$defaults = array(
			'id' => 'undefined-meta-box',
			'title' => 'Undefined Meta Box',
			'page' => 'post',
		    'context' => 'normal',
		    'priority' => 'high',
			'inlcude' => array(),
			'exclude' => array(),
			'fields' => array()
		);
		
		$boxes[$box['id']] = $box + $defaults;
		
		return true;
	}
	
	/**
	 * Returns all page registrations
	 * 
	 * @return array
	 */
	function f20_get_metaboxes()
	{
		//initializing variables
		$boxes = f20_register_metabox();
		
		//reasons to fail
		if (!isset($_REQUEST['post']) || ! $post_id = $_REQUEST['post']) return $boxes;
		
		if (is_array($boxes))
		{
			foreach ($boxes as $key => $box)
			{
				//making sure that the includes and excludes are proper arrrays
				if (isset($box['include']) && !is_array($box['include']) && strlen(trim($box['include'])) > 0)
				{
					$boxes[$key]['include'] = $box['include'] = explode(',',$box['include']);
				}
				if (isset($box['exclude']) && !is_array($box['exclude']) && strlen(trim($box['exclude'])) > 0)
				{
					$boxes[$key]['exclude'] = $box['exclude'] = explode(',',$box['exclude']);
				}
				
				//honoring any includes and excludes
				if ($post_id)
				{
					if (!empty($box['include']) && !in_array($post_id, $box['include']))
					{
						unset($boxes[$key]);
					}
					if (!empty($box['exclude']) && in_array($post_id, $box['exclude']))
					{
						unset($boxes[$key]);
					}
				}
			}
		}
		
		return $boxes;
	}
	
	/**
	 * Add meta box
	 * 
	 * This function adds the meta box hooks
	 * 
	 * @return boolean
	 */
	function f20_add_metaboxes() 
	{
		do_action('init_metaboxes');
		
		//reasons to fail
		if (!isset($_REQUEST['post'])) return false;
		
		//initializing variables
		$meta_boxs = f20_get_metaboxes();
		$post_id = $_REQUEST['post'];
		$post = get_post($post_id);
		
		if (is_array($meta_boxs))
	    {
	    	foreach ($meta_boxs as $id => $meta_box)
	    	{
	    		if ($meta_box['page'] === true && isset($post->post_type))
				{
					$meta_box['page'] = $post->post_type;
				}
	    		add_meta_box($meta_box['id'], $meta_box['title'], 'f20_display_metafields', $meta_box['page'], $meta_box['context'], $meta_box['priority'], array( 'fields' => $meta_box['fields'], 'id' => $id));
	    	}
	    }
	    
	    return true;
	}
	
	/**
	 * Callback function to show fields in meta box
	 * 
	 * @param unknown_type $post
	 * @param unknown_type $fields
	 */
	function f20_display_metafields($post, $fields) 
	{
		//reasons to fail
		if (!isset($fields['args'])) return false;
		
		//initializing variables
		$meta_id = $fields['args']['id'];
		$colspan = array('show_view');
		
		//checking for the table creation
		$table = true;
		if ((isset($fields['args']['fields'][0]['options']['table']) 
		&& $fields['args']['fields'][0]['options']['table'] === false)
		|| in_array($fields['args']['fields'][0]['type'],$colspan))
		{
			$table = false;
		}
	    
		//checking for the editing abilities
		$edit = true;
		if (isset($fields['args']['fields'][0]['options']['edit']) 
		&& $fields['args']['fields'][0]['options']['edit'] === false)
		{
			$edit = false;
		}
	    
		// Use nonce for verification
	    echo '<input type="hidden" name="custom_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
		if ($table) echo '<table class="form-table autogenerated-metabox">';
		
	    foreach ($fields['args']['fields'] as $field) 
	    {
	        // get current post meta data
	        $meta = get_post_meta($post->ID, $field['id'], true);
	        $unique = md5(time());
	        
	        if ($table)
	        {
	        	echo '<tr>'; 
	       	 	echo '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th><td>';
	        }
	        
	        if ($edit === false && $meta)
	        {
	        	echo $meta;
	        }
	       	else switch ($field['type'])
	        {
	            case 'show_view':
	                twc_show_view($field['id']);
	            	break;
	            case 'text':
	                echo '<input ',@$field['attr'],' type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', "\n", $field['desc'];
	                break;
	            case 'textarea':
	                echo '<textarea ',@$field['attr'],' name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', "\n", $field['desc'];
	                break;
	            case 'select':
	                echo '<select ',@$field['attr'],' name="', $field['id'], '" id="', $field['id'], '">';
	                foreach ($field['options'] as $option) {
	                    echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
	                }
	                echo '</select>';
	                break;
	            case 'radio':
	                foreach ($field['options'] as $option) {
	                    echo '<input ',@$field['attr'],' type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'], '<br/>';
	                }
	                break;
	            case 'checkbox':
	                echo '<input type="hidden" name="', $field['id'], '" value="" /> ';
	                echo '<input ',@$field['attr'],' type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' /> ', $field['desc'];
	                break;
	            case 'editor':
	            	echo 
	                '<div style="border:1px solid #DFDFDF;border-collapse: separate;border-top-left-radius: 6px 6px;border-top-right-radius: 6px 6px;">',
	                	'<textarea ',@$field['attr'],' rows="10" class="theEditor" cols="40" name="', $field['id'], '" id="'.$unique.'"></textarea>',
	                '</div>', 
	                '<script type="text/javascript">edCanvas = document.getElementById(\''.$unique.'\');</script>', "\n", $field['desc'];
	                break;
	        }
	        
	        if ($table) 
	        { 
	        	echo '</td>',
	            '</tr>';
	        }
	    }
	    
	    if ($table) echo '</table>';
	}
	
	
	/**
	 * Save data from meta box
	 * 
	 * @param $post_id
	 */
	function f20_metabox_save_data($post_id) 
	{
		//initializing variables
		$meta_boxs = f20_get_metaboxes();
		$custom_meta_box_nonce = (isset($_REQUEST['custom_meta_box_nonce'])) ?$_REQUEST['custom_meta_box_nonce'] :basename(__FILE__);//$_REQUEST
		
		// verify nonce
		if (!wp_verify_nonce($custom_meta_box_nonce)) {
			return $post_id;
		}
		
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
		
		// check permissions
		if ('page' == $_REQUEST['post_type'])
		{
			if (!current_user_can('edit_page', $post_id))
			{
				return $post_id;
			}
		} 
		elseif (!current_user_can('edit_post', $post_id))
		{
			return $post_id;
		}
		
		if (is_array($meta_boxs))
		{
			foreach ($meta_boxs as $meta_box)
			{
				if ($meta_box['page'] != $_REQUEST['post_type']) continue;
				
				foreach ($meta_box['fields'] as $field)
				{
					if (!isset($_POST[$field['id']])) continue;
	    			
	    			$old = get_post_meta($post_id, $field['id'], true);
	    			$new = (isset($_REQUEST[$field['id']]));
	    			
	    			if ($new && $new != $old)
	    			{
	    				update_post_meta($post_id, $field['id'], $new);
	    			}
	    			elseif ('' == $new && $old)
	    			{
	    				delete_post_meta($post_id, $field['id'], $old);
	    			}
			    }
			    
	    	}
	    	
	    }
	    
	}
	
endif;

if (!function_exists('f20_get_page_url')):
	/**
	 * function is responsible for return the current pages url
	 *
	 * @return unknown
	 */
	function f20_get_page_url()
	{
		return 'http'.((!empty($_SERVER['HTTPS']))?'s':'').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	}
endif; 

if (!function_exists('f20_get_domain')):
	/**
	 * function is responsible for returning the domain name that's to be used in the licensing process
	 *
	 * @return unknown
	 */
	function f20_get_domain()
	{
		$parts = parse_url("http:/"."/".str_replace("http:/"."/",'',$_SERVER["SERVER_NAME"]));
		return $parts['host'];
	}
endif; 


if (!function_exists('twc_set_notification')):

	/**
	 * Initial notification setup
	 * 
	 * This function prepares any notifications or warnings that
	 * are being passed through $post or $get
	 */
	function twc_initial_notification_setup()
	{
		if (isset($_REQUEST['set_warning']) && !empty($_REQUEST['set_warning']))
		{
			twc_set_warning(urldecode($_REQUEST['set_warning']));
		}
		if (isset($_REQUEST['set_notification']) && !empty($_REQUEST['set_notification']))
		{
			twc_set_notification(urldecode($_REQUEST['set_notification']));
		}
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 */
	function twc_set_notification( $note = null )
	{
		static $notes;
		
		if (!isset($notes))
		{
			$notes = array();
		}
		
		//reasons to fail
		if (is_null($note)) return $notes;
		
		$notes[] = $note;
		
		return true;
	}
	
	/**
	 * gets the notifications
	 * 
	 */
	function twc_get_notifications()
	{
		return twc_set_notification();
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 */
	function twc_set_warning( $note = null )
	{
		static $notes;
		
		if (!isset($notes))
		{
			$notes = array();
		}
		
		//reasons to fail
		if (is_null($note)) return $notes;
		
		$notes[] = $note;
		twc_error_log($note);
		return true;
	}
	
	/**
	 * gets the notifications
	 * 
	 */
	function twc_get_warnings()
	{
		return twc_set_warning();
	}
	
	/**
	 * Displays the notifications in a nice box
	 * 
	 */
	function twc_display_notifications()
	{
		//initializing variables
		static $once;
		if (isset($once)) return;
		$once = true;
		$notes = twc_get_notifications();
		$warns = twc_get_warnings();
		
		if (is_array($notes))
		{
			foreach ($notes as $note)
			{
				twc_notification($note);
			}
		}
		
		if (is_array($warns))
		{
			foreach ($warns as $warn)
			{
				twc_message($warn);
			}
		}
		
	}
endif;


function twc_error_log( $content = false, $file = '', $line = '' )
{
	//reasons to fail plugin_basename($file)
	if (!$content) return false;
	
	if ($file || $line)
	{
		$content = "$content in ($file) on line $line";
	}
	
	$log = get_option('twc_error_log', false);
	$content = date('Y-m-d g:i a',time()).' '.$content;
	$content = $log."<br>".$content;
	
	if ($log === false)
	{
		add_option('twc_error_log', $content, '', $autoload = 'no');
	}
	else
	{
		update_option('twc_error_log', $content);
	}
}


if ( !class_exists('PluginUpdateChecker') ):
	
/**
 * A custom plugin update checker. 
 * 
 * @author Janis Elsts
 * @copyright 2010
 * @version 1.0
 * @access public
 */
class PluginUpdateChecker {
	public $metadataUrl = ''; //The URL of the plugin's metadata file.
	public $pluginFile = '';  //Plugin filename relative to the plugins directory.
	public $slug = '';        //Plugin slug.
	public $checkPeriod = 1; //How often to check for updates (in hours).
	public $optionName = '';  //Where to store the update info.
	
	/**
	 * Class constructor.
	 * 
	 * @param string $metadataUrl The URL of the plugin's metadata file.
	 * @param string $pluginFile Fully qualified path to the main plugin file.
	 * @param string $slug The plugin's 'slug'. If not specified, the filename part of $pluginFile sans '.php' will be used as the slug.
	 * @param integer $checkPeriod How often to check for updates (in hours). Defaults to checking every 12 hours. Set to 0 to disable automatic update checks.
	 * @param string $optionName Where to store book-keeping info about update checks. Defaults to 'external_updates-$slug'. 
	 * @return void
	 */
	function __construct($metadataUrl, $pluginFile, $slug = '', $checkPeriod = 12, $optionName = ''){
		$this->metadataUrl = $metadataUrl;
		$this->pluginFile = plugin_basename($pluginFile);
		$this->checkPeriod = $checkPeriod;
		$this->slug = $slug;
		$this->optionName = $optionName;
		
		//If no slug is specified, use the name of the main plugin file as the slug.
		//For example, 'my-cool-plugin/cool-plugin.php' becomes 'cool-plugin'.
		if ( empty($this->slug) ){
			$this->slug = basename($this->pluginFile, '.php');
		}
		
		if ( empty($this->optionName) ){
			$this->optionName = 'external_updates-' . $this->slug;
		}
		
		$this->installHooks();		
	}
	
	/**
	 * Install the hooks required to run periodic update checks and inject update info 
	 * into WP data structures. 
	 * 
	 * @return void
	 */
	function installHooks(){
		//Override requests for plugin information
		add_filter('plugins_api', array(&$this, 'injectInfo'), 10, 3);
		
		//Insert our update info into the update array maintained by WP
		add_filter('site_transient_update_plugins', array(&$this,'injectUpdate')); //WP 3.0+
		add_filter('transient_update_plugins', array(&$this,'injectUpdate')); //WP 2.8+
		
		//Set up the periodic update checks
		$cronHook = 'check_plugin_updates-' . $this->slug;
		if ( $this->checkPeriod > 0 ){
			
			//Trigger the check via Cron
			add_filter('cron_schedules', array(&$this, '_addCustomSchedule'));
			if ( !wp_next_scheduled($cronHook) && !defined('WP_INSTALLING') ) {
				$scheduleName = 'every' . $this->checkPeriod . 'hours';
				wp_schedule_event(time(), $scheduleName, $cronHook);
			}
			add_action($cronHook, array(&$this, 'checkForUpdates'));
			
			//In case Cron is disabled or unreliable, we also manually trigger 
			//the periodic checks while the user is browsing the Dashboard. 
			add_action( 'admin_init', array(&$this, 'maybeCheckForUpdates') );
			
		} else {
			//Periodic checks are disabled.
			wp_clear_scheduled_hook($cronHook);
		}		
	}
	
	/**
	 * Add our custom schedule to the array of Cron schedules used by WP.
	 * 
	 * @param array $schedules
	 * @return array
	 */
	function _addCustomSchedule($schedules){
		if ( $this->checkPeriod && ($this->checkPeriod > 0) ){
			$scheduleName = 'every' . $this->checkPeriod . 'hours';
			$schedules[$scheduleName] = array(
				'interval' => $this->checkPeriod * 3600, 
				'display' => sprintf('Every %d hours', $this->checkPeriod),
			);
		}		
		return $schedules;
	}
	
	/**
	 * Retrieve plugin info from the configured API endpoint.
	 * 
	 * @uses wp_remote_get()
	 * 
	 * @param array $queryArgs Additional query arguments to append to the request. Optional.
	 * @return PluginInfo
	 */
	function requestInfo($queryArgs = array()){
		//Query args to append to the URL. Plugins can add their own by using a filter callback (see addQueryArgFilter()).
		$queryArgs['installed_version'] = $this->getInstalledVersion(); 
		$queryArgs = apply_filters('puc_request_info_query_args-'.$this->slug, $queryArgs);
		
		//Various options for the wp_remote_get() call. Plugins can filter these, too.
		$options = array(
			'timeout' => 10, //seconds
			'headers' => array(
				'Accept' => 'application/json'
			),
		);
		$options = apply_filters('puc_request_info_options-'.$this->slug, array());
		
		//The plugin info should be at 'http://your-api.com/url/here/$slug/info.json'
		$url = $this->metadataUrl; 
		if ( !empty($queryArgs) ){
			$url = add_query_arg($queryArgs, $url);
		}
		
		$result = wp_remote_get(
			$url,
			$options
		);
		
		//Try to parse the response
		$pluginInfo = null;
		if ( !is_wp_error($result) && isset($result['response']['code']) && ($result['response']['code'] == 200) && !empty($result['body']) ){
			$pluginInfo = PluginInfo::fromJson($result['body']);
		}
		$pluginInfo = apply_filters('puc_request_info_result-'.$this->slug, $pluginInfo, $result);
		return $pluginInfo;
	}
	
	/**
	 * Retrieve the latest update (if any) from the configured API endpoint.
	 * 
	 * @uses PluginUpdateChecker::requestInfo()
	 * 
	 * @return PluginUpdate An instance of PluginUpdate, or NULL when no updates are available.
	 */
	function requestUpdate(){
		//For the sake of simplicity, this function just calls requestInfo() 
		//and transforms the result accordingly.
		$pluginInfo = $this->requestInfo(array('checking_for_updates' => '1'));
		if ( $pluginInfo == null ){
			return null;
		}
		return PluginUpdate::fromPluginInfo($pluginInfo);
	}
	
	/**
	 * Get the currently installed version of the plugin.
	 * 
	 * @return string Version number.
	 */
	function getInstalledVersion(){
		if ( !function_exists('get_plugins') ){
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		$allPlugins = get_plugins();
		if ( array_key_exists($this->pluginFile, $allPlugins) && array_key_exists('Version', $allPlugins[$this->pluginFile]) ){
			return $allPlugins[$this->pluginFile]['Version']; 
		} else {
			return ''; //This should never happen.
		};
	}
	
	/**
	 * Check for plugin updates. 
	 * The results are stored in the DB option specified in $optionName.
	 * 
	 * @return void
	 */
	function checkForUpdates(){
		$state = get_option($this->optionName);
		if ( empty($state) ){
			$state = new StdClass;
			$state->lastCheck = 0;
			$state->checkedVersion = '';
			$state->update = null;
		}
		
		$state->lastCheck = time();
		$state->checkedVersion = $this->getInstalledVersion();
		update_option($this->optionName, $state); //Save before checking in case something goes wrong 
		
		$state->update = $this->requestUpdate();
		update_option($this->optionName, $state);
	}
	
	/**
	 * Check for updates only if the configured check interval has already elapsed.
	 * 
	 * @return void
	 */
	function maybeCheckForUpdates(){
		if ( empty($this->checkPeriod) ){
			return;
		}
		
		$state = get_option($this->optionName);
		
		$shouldCheck =
			empty($state) ||
			!isset($state->lastCheck) || 
			( (time() - $state->lastCheck) >= $this->checkPeriod*3600 );
		
		if ( $shouldCheck ){
			$this->checkForUpdates();
		}
	}
	
	/**
	 * Intercept plugins_api() calls that request information about our plugin and 
	 * use the configured API endpoint to satisfy them. 
	 * 
	 * @see plugins_api()
	 * 
	 * @param mixed $result
	 * @param string $action
	 * @param array|object $args
	 * @return mixed
	 */
	function injectInfo($result, $action = null, $args = null){
    	$relevant = ($action == 'plugin_information') && isset($args->slug) && ($args->slug == $this->slug);
		if ( !$relevant ){
			return $result;
		}
		
		$pluginInfo = $this->requestInfo();
		if ($pluginInfo){
			return $pluginInfo->toWpFormat();
		}
				
		return $result;
	}
	
	/**
	 * Insert the latest update (if any) into the update list maintained by WP.
	 * 
	 * @param array $updates Update list.
	 * @return array Modified update list.
	 */
	function injectUpdate($updates){
		$state = get_option($this->optionName);
		
		//Is there an update to insert?
		if ( !empty($state) && isset($state->update) && !empty($state->update) ){
			//Only insert updates that are actually newer than the currently installed version.
			if ( version_compare($state->update->version, $this->getInstalledVersion(), '>') ){
				$updates->response[$this->pluginFile] = $state->update->toWpFormat();
			}
		}
				
		return $updates;
	}
	
	/**
	 * Register a callback for filtering query arguments. 
	 * 
	 * The callback function should take one argument - an associative array of query arguments.
	 * It should return a modified array of query arguments.
	 * 
	 * @uses add_filter() This method is a convenience wrapper for add_filter().
	 * 
	 * @param callback $callback 
	 * @return void
	 */
	function addQueryArgFilter($callback){
		add_filter('puc_request_info_query_args-'.$this->slug, $callback);
	}
	
	/**
	 * Register a callback for filtering arguments passed to wp_remote_get().
	 * 
	 * The callback function should take one argument - an associative array of arguments -
	 * and return a modified array or arguments. See the WP documentation on wp_remote_get()
	 * for details on what arguments are available and how they work. 
	 * 
	 * @uses add_filter() This method is a convenience wrapper for add_filter().
	 * 
	 * @param callback $callback
	 * @return void
	 */
	function addHttpRequestArgFilter($callback){
		add_filter('puc_request_info_options-'.$this->slug, $callback);
	}
	
	/**
	 * Register a callback for filtering the plugin info retrieved from the external API.
	 * 
	 * The callback function should take two arguments. If the plugin info was retrieved 
	 * successfully, the first argument passed will be an instance of  PluginInfo. Otherwise, 
	 * it will be NULL. The second argument will be the corresponding return value of 
	 * wp_remote_get (see WP docs for details).
	 *  
	 * The callback function should return a new or modified instance of PluginInfo or NULL.
	 * 
	 * @uses add_filter() This method is a convenience wrapper for add_filter().
	 * 
	 * @param callback $callback
	 * @return void
	 */
	function addResultFilter($callback){
		add_filter('puc_request_info_result-'.$this->slug, $callback, 10, 2);
	}
}
	
endif;

if ( !class_exists('PluginInfo') ):

/**
 * A container class for holding and transforming various plugin metadata.
 * 
 * @author Janis Elsts
 * @copyright 2010
 * @version 1.0
 * @access public
 */
class PluginInfo {
	//Most fields map directly to the contents of the plugin's info.json file.
	//See the relevant docs for a description of their meaning.  
	public $name;
	public $slug;
	public $version;
	public $homepage;
	public $sections;
	public $download_url;

	public $author;
	public $author_homepage;
	
	public $requires;
	public $tested;
	public $upgrade_notice;
	
	public $rating;
	public $num_ratings;
	public $downloaded;
	public $last_updated;
	
	public $id = 0; //The native WP.org API returns numeric plugin IDs, but they're not used for anything.
		
	/**
	 * Create a new instance of PluginInfo from JSON-encoded plugin info 
	 * returned by an external update API.
	 * 
	 * @param string $json Valid JSON string representing plugin info. 
	 * @return PluginInfo New instance of PluginInfo, or NULL on error.
	 */
	public static function fromJson($json){
		$apiResponse = json_decode($json);
		if ( empty($apiResponse) || !is_object($apiResponse) ){
			return null;
		}
		
		//Very, very basic validation.
		$valid = isset($apiResponse->name) && !empty($apiResponse->name) && isset($apiResponse->version) && !empty($apiResponse->version);
		if ( !$valid ){
			return null;
		}
		
		$info = new PluginInfo();
		foreach(get_object_vars($apiResponse) as $key => $value){
			$info->$key = $value;
		}
		
		return $info;		
	}
	
	/**
	 * Transform plugin info into the format used by the native WordPress.org API
	 * 
	 * @return object
	 */
	public function toWpFormat(){
		$info = new StdClass;
		
		//The custom update API is built so that many fields have the same name and format
		//as those returned by the native WordPress.org API. These can be assigned directly. 
		$sameFormat = array(
			'name', 'slug', 'version', 'requires', 'tested', 'rating', 'upgrade_notice',
			'num_ratings', 'downloaded', 'homepage', 'last_updated',
		);
		foreach($sameFormat as $field){
			if ( isset($this->$field) ) {
				$info->$field = $this->$field;
			}
		}
		
		//Other fields need to be renamed and/or transformed.
		$info->download_link = $this->download_url;
		
		if ( !empty($this->author_homepage) ){
			$info->author = sprintf('<a href="%s">%s</a>', $this->author_homepage, $this->author);
		} else {
			$info->author = $this->author;
		}
		
		if ( is_object($this->sections) ){
			$info->sections = get_object_vars($this->sections);
		} elseif ( is_array($this->sections) ) {
			$info->sections = $this->sections;
		} else {
			$info->sections = array('description' => '');
		}
				
		return $info;
	}
}
	
endif;

if ( !class_exists('PluginUpdate') ):

/**
 * A simple container class for holding information about an available update.
 * 
 * @author Janis Elsts
 * @copyright 2010
 * @version 1.0
 * @access public
 */
class PluginUpdate {
	public $id = 0;
	public $slug;
	public $version;
	public $homepage;
	public $download_url;
	public $upgrade_notice;
	
	/**
	 * Create a new instance of PluginUpdate from its JSON-encoded representation.
	 * 
	 * @param string $json
	 * @return PluginUpdate
	 */
	public static function fromJson($json){
		//Since update-related information is simply a subset of the full plugin info,
		//we can parse the update JSON as if it was a plugin info string, then copy over
		//the parts that we care about.
		$pluginInfo = PluginInfo::fromJson($json);
		if ( $pluginInfo != null ) {
			return PluginUpdate::fromPluginInfo($pluginInfo);
		} else {
			return null;
		}
	}
	
	/**
	 * Create a new instance of PluginUpdate based on an instance of PluginInfo.
	 * Basically, this just copies a subset of fields from one object to another.
	 * 
	 * @param PluginInfo $info
	 * @return PluginUpdate
	 */
	public static function fromPluginInfo($info){
		$update = new PluginUpdate();
		$copyFields = array('id', 'slug', 'version', 'homepage', 'download_url', 'upgrade_notice');
		foreach($copyFields as $field){
			$update->$field = $info->$field;
		}
		return $update;
	}
	
	/**
	 * Transform the update into the format used by WordPress native plugin API.
	 * 
	 * @return object
	 */
	public function toWpFormat(){
		$update = new StdClass;
		
		$update->id = $this->id;
		$update->slug = $this->slug;
		$update->new_version = $this->version;
		$update->url = $this->homepage;
		$update->package = $this->download_url;
		if ( !empty($this->upgrade_notice) ){
			$update->upgrade_notice = $this->upgrade_notice;
		}
		
		return $update;
	}
}
	
endif;