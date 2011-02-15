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

//initializing
$widget_id = $_REQUEST['widget_id'];
twc_delete_widget_instance($widget_id);

wp_redirect('widgets.php');
exit();