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

//reasons to fail
if (!isset($_REQUEST['hide_ID'])) return false;

$msg = get_option( 'twc_hide_messages', array() );
$msg[$_REQUEST['hide_ID']] = $_REQUEST['hide_ID'];
update_option( 'twc_hide_messages', $msg );
