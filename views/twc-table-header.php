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
if ( TWC_CURRENT_USER_CANNOT ) wp_die();

?>
	<tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
	<th scope="col" id="title" class="manage-column" style="width:60%">Title</th>
	<th scope="col" id="sidebar" class="manage-column" style="width:30%;">Sidebar</th>
	<th scope="col" id="date" class="manage-column" style="width:10%;text-align:center;">Position</th>
	</tr>
