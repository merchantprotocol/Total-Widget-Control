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

//reasons to fail
if ($current_screen->action != 'manual') return false;

if (!empty($_FILES))
{
	$licenses = get_option('twc_licenses',array());
	$licenses[f20_get_domain()] = @file_get_contents($_FILES['license']['tmp_name']);
	update_option('twc_licenses',$licenses);
	
	wp_redirect( admin_url('widgets.php?list_style=twc') );
	exit();
}


require $view;