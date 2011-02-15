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
 * Plugin Name: Total Widget Control
 * Plugin URI: http://www.5twentystudios.com
 * Description: This plugin is deisgned to revolutionize the widget control system within Wordpress 3.0+. The goal here is to learn from the Joomla module control system and implement their design into WordPress. <a href="http://www.jonathonbyrd.com" target="_blank">Author Website</a>
 * Version: 1.0
 * Author: 5Twenty Studios
 * Author URI: http://www.5twentystudios.com
 * 
 * 
 * 
 * @TODO I need to add an initial setup page that will introduce new users to this system
 * @TODO This plugin should immediately be active for ever user as a default
 * @TODO I need to clean up the twc-nav-menu javascript to only include the js that I need
 * @TODO I need to build in my own set of default widgets and default wrappers
 * @TODO I need to activate shortcodes for widgets
 * @TODO I need to activate shortcodes for sidebars
 * 
 */


defined('ABSPATH') or die("Cannot access pages directly.");

/**
 * Initializing 
 * 
 * The directory separator is different between linux and microsoft servers.
 * Thankfully php sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 * 
 */
defined("DS") or define("DS", DIRECTORY_SEPARATOR);

/**
 * User Control Level
 * 
 * Allows the developer to hook into this system and set the access level for this plugin.
 * If the user does not have the capability to view this plguin, they may still be
 * able to view the default widget area. This will not cause problems with the script,
 * however the editing user will not be able to add or delete viewable pages to the 
 * widget.
 * 
 * @TODO need to set this to call get_option from the db
 * @TODO need to add this as a security check to every file
 */
defined("TWC_CURRENT_USER_CAN") or define("TWC_CURRENT_USER_CAN", "activate_plugins");

/**
 * The WordPress Version
 * 
 * Function should set the proper wordpress version, so that we can acurratly know 
 * which set of actions and filters to declare
 */
defined("TWC_WP_VERSION") or define("TWC_WP_VERSION", "3.0");

/**
 * Startup
 * 
 * This block of functions is only preloading a set of functions that I've prebuilt
 * and that I use throughout my websites.
 * 
 * @TODO Need to test this system while it's using the bootstrap file, currently it's being 
 * overridden by the 520 plugin
 * 
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @since 1.0
 */
require_once dirname(__file__).DS."bootstrap.php";
require_once dirname(__file__).DS."auth.php";

set_controller_path( get_theme_path() );
set_controller_path( dirname( __FILE__ ) );

/**
 * Loading Resources
 * 
 * These files make up this plugins uniqueness, Hack Away!
 * 
 */
require_once dirname(__file__).DS."total-widget-control.php";

/**
 * Initialize the Framework
 * 
 */
twc_initialize();



