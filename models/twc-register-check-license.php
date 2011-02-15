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

if (@file_get_contents( dirname(dirname(__file__)).DS.'license' ))
{
	die('1');
}
die('0');
/*
if (!function_exists('twc_widget_meta_navmenu'))
{
	require dirname(dirname(__file__)).DS.'auth.php';
	if (!function_exists('twc_widget_meta_navmenu'))
	{
		if (!file_exists(dirname(dirname(__file__)).DS.'license'))
		{
			if ($GLOBALS['TWCAUTH'])
			{
				die('0');
			}
		}
	}
	die('1');
}
*/