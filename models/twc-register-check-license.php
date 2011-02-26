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

ob_end_clean();

$parts=parse_url("http:/"."/".$_SERVER["SERVER_NAME"]);
$licenses = get_option('twc_licenses',array());

if (isset($licenses[$parts['host']]))
{
	die('1');
}
die('0');