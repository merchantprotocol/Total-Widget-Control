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
global $twc_table_type;

?>
<tr>
	<?php if ($twc_table_type == 'default') :?>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" class="twcp"></th>
	<?php endif; ?>
	<th scope="col" id="title" class="manage-column" style="width:60%"><?php _e('Title','twc'); ?></th>
	<th scope="col" id="sidebar" class="manage-column" style="width:30%;"><?php _e('Sidebar','twc'); ?></th>
	<th scope="col" id="date" class="manage-column" style="width:10%;text-align:center;white-space:nowrap;"><?php _e('Position','twc'); ?></th>
</tr>
