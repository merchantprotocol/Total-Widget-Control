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
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');


//initializing variables
$current_screen = twc_get_current_screen();

//controller
if ($current_screen->action != 'manual') return false;

//initializing variables
$uniqueID = get_option('twc_unique_registration_key', create_guid());
$parts=parse_url("http:/"."/".$_SERVER["SERVER_NAME"]);
$domain=$parts["host"];
$headers = get_plugin_data( dirname(dirname(__file__)).DS.'index.php' );

if (!empty($_FILES))
{
	$licenses = get_option('twc_licenses',array());
	$licenses[$parts['host']] = @file_get_contents($_FILES['license']['tmp_name']);
	update_option('twc_licenses',$licenses);
	update_option('twc_is_free',false);
	
	wp_redirect(get_option('url').'/wp-admin/widgets.php?list_style=twc');
	exit();
}


require $view;