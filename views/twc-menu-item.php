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
<li class="menu-item-li <?php twc_row_alternate(); ?>">
	<div class="menu-item-name"><?php echo $menu_item['menu-item-object'].' '.$menu_item['menu-item-title']; ?></div>
	<div class="menu-item-actions">
		<span><a href="">Remove</a></span>
	</div>
</li>