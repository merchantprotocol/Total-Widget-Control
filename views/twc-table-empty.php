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
global $twc_rows;

//reasons to fail
if (!empty($twc_rows)) return false;

?>
<tr>
<td colspan="100"><p>You currently do not have any widgets set to any sidebars. Try checking in the trash for inactive widgets.</p></td>
</tr>