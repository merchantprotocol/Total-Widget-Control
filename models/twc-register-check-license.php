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

ob_end_clean();

$parts=parse_url("http:/"."/".$_SERVER["SERVER_NAME"]);
if (@file_get_contents( dirname(dirname(__file__)).DS.$parts['host'] ))
{
	die('1');
}
die('0');