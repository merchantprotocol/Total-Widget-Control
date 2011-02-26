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
 * Description: This plugin is designed to revolutionize the widget control system within Wordpress 3.0+. The goal here is to learn from the Joomla module control system and implement their design into WordPress. <a href="http://www.jonathonbyrd.com" target="_blank">Author Website</a>
 * Version: 1.5.5
 * Author: 5Twenty Studios
 * Author URI: http://www.5twentystudios.com
 * 
 * 
 * 
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
 * Initialize Localization
 * 
 * @tutorial http://codex.wordpress.org/I18n_for_WordPress_Developers
 * function call loads the localization files from the current folder
 * 
 */
if (function_exists('load_theme_textdomain')) load_theme_textdomain('twc');

/**
 * Set Dates Default Timezone
 * 
 * The server has a timezone, mysql has a timezone, php has a timezone and wordpress 
 * it's own timezone. The following setting will synchronize the wordpress timezone
 * with the php timezone. This program uses the php timezone for publishing settings.
 * 
 */
if (function_exists('date_default_timezone_set')) date_default_timezone_set(get_site_option('timezone_string'));

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
require_once ABSPATH.WPINC.DS."pluggable.php";
require_once dirname(__file__).DS."bootstrap.php";
require_once dirname(__file__).DS."total-widget-control.php";
require_once dirname(__file__).DS."template-codes.php";
require_once dirname(__file__).DS."widgets.php";

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
defined("TWC_CURRENT_USER_CANNOT") or define("TWC_CURRENT_USER_CANNOT", (!current_user_can("edit_theme_options")) );

/**
 * Are Sortables Turned On
 * 
 * You probably shouldn't turn on this constant at all, it's still very much in
 * the development stage.
 */
defined("TWC_SORTABLES") or define("TWC_SORTABLES", FALSE);

/**
 * Is administrator
 * 
 * The value of this constant will determine if the user can modify widgets from
 * the front end of the website. We combine this with sortables even being turned 
 * on.
 */
defined("TWC_IS_SORTER") or define("TWC_IS_SORTER", (current_user_can("edit_theme_options") && TWC_SORTABLES));

/**
 * Initialize the Framework
 * 
 */
set_controller_path( dirname( __FILE__ ) );
require_once dirname(__file__).DS."auth.php";
twc_initialize();


