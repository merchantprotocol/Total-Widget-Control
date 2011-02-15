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

//security check
if (!current_user_can('activate_plugins')) return;


//initializing variables
$twc_data = $_REQUEST['twc_data'];
twc_save_widget_sidebar($twc_data['widget_id'], $twc_data['sidebar_slug'], $twc_data['position']);
