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

?>
<select name="twcp_bulk_action">
	<option value="-1" selected="selected">Bulk Actions</option>
	<option value="edit">Edit</option>
	<option value="trash">Move to Trash</option>
</select>
<input type="submit" value="Apply" name="twcp_dobulk" id="doaction" class="button-secondary action">