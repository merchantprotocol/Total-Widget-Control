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
global $wp_version;
$current_screen = twc_get_current_screen();
$uniqueID = get_option('twc_unique_registration_key', create_guid());
$first = get_option('twc_unique_registration_key', true);

//reasons to fail
if ($current_screen->action != 'register') return false;

switch($_REQUEST['license'])
{
	case '1':  
		$type = 'twc-pro'; 
		$price = '0.00'; //prices listed here are only used in ganalytics
		break;
	case '2':
		$type = 'twc-ent';  
		$price = '19.00';
		break;
	default: 
		$type = 'twc-free'; 
		$price = '9.00';
		break;
}
	
//initializing variables
$uniqueID = get_option('twc_unique_registration_key', create_guid());
$headers = get_plugin_data( dirname(dirname(__file__)).DS.'index.php' );

require $view;
