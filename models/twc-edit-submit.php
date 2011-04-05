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

//loading resources
require_once ABSPATH.WPINC.DS.'capabilities.php';

//initializing variables
global $widget, $wp_roles;
$defaults = array(
	'twcp_status' => 'enabled',
	'twcp_visibility' => 'public',
	'twcp_visible_parent' => '',
	'twcp_publish_time' => 'immediately',
);
foreach((array)$defaults as $property => $value)
{
	$defaults[$property] = (array_key_exists($property, (array)$widget['p'])) ?$widget['p'][$property] :$value;
}
extract($defaults);

//calculating the publishing time 
$twcp_publish_time_format = ($twcp_publish_time == 'immediately') 
	?__('Publish <b>immediately</b>') 
	:__('Publish on :').'<b>'.date('M d,Y @ h:i', $twcp_publish_time).'</b>';
if ($twcp_publish_time == 'immediately')
{
	extract(array(
		'twcp_mm' => date('m', time()),
		'twcp_jj' => date('j', time()),
		'twcp_YY' => date('Y', time()),
		'twcp_hh' => date('h', time()),
		'twcp_ii' => date('i', time()),
	));
}
else
{
	extract(array(
		'twcp_mm' => date('m', $twcp_publish_time),
		'twcp_jj' => date('j', $twcp_publish_time),
		'twcp_YY' => date('Y', $twcp_publish_time),
		'twcp_hh' => date('h', $twcp_publish_time),
		'twcp_ii' => date('i', $twcp_publish_time),
	));
}

$roles = $wp_roles->roles;
$options = array();

foreach ((array)$roles as $role => $set)
{
	$options[$role] = $set['name'];
}


require $view;

