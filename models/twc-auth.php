<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.6.4
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');

//initializing variables
$current_screen = twc_get_current_screen();
global $wp_version;

//reasons to fail
if ($current_screen->action != 'auth') return false;

//validating the auth
if (function_exists('twc_check_auth')) twc_check_auth();

twc_error_log("Viewing compare and purchase page.");
require $view;