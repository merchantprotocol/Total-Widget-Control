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
<div class="postbox">
	<div class="handlediv" title="Click to toggle"><br></div>
	<h3 class="hndle">
		<span><?php _e('Widget Control Settings','twc');?></span>
	</h3>
	<div class="inside">

				<div class="twc-third">
					<label><?php _e('Sidebar','twc');?> </label>
					<?php echo twc_sidebar_select_box($sidebar_id, $widget, true); ?>
					<div class="clear"></div>
				</div>
				
				<div class="twc-third">
					<label><?php _e('Position','twc');?> </label>
					<?php echo twc_position_select_box($sidebar_id, $position); ?>
					<div class="clear"></div>
				</div>
				
				<div class="clear"></div>
	</div>
</div>

<?php return; ?>
<div id="menu-management" class="postbox">
	<div class="menu-edit">
		<div id="nav-menu-header">
			<div id="wrapper_head" class="submitbox">
				<div class="major-publishing-actions secondary-publishing">
					<h3><span><?php _e('Widget Control Settings','twc');?></span></h3>
				</div>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				
				<div class="twc-third">
					<label><?php _e('Sidebar','twc');?> </label>
					<?php echo twc_sidebar_select_box($sidebar_id, $widget, true); ?>
					<div class="clear"></div>
				</div>
				
				<div class="twc-third">
					<label><?php _e('Position','twc');?> </label>
					<?php echo twc_position_select_box($sidebar_id, $position); ?>
					<div class="clear"></div>
				</div>
				
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>

