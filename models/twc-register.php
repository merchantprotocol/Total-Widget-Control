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

//initializing variables
$current_screen = twc_get_current_screen();

//reasons to fail
if ($current_screen->action != 'register') return false;

//initializing variables
$uniqueID = get_option('twc_unique_registration_key', create_guid());
$headers = get_plugin_data( dirname(dirname(__file__)).DS.'index.php' );

require $view;
