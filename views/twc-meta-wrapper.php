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
	<div class="handlediv" title="<?php _e('Click to toggle','twc'); ?>"><br></div>
	<h3 class="hndle">
		<span><?php _e('Widget Display Settings','twc');?></span>
	</h3>
	<div class="inside">
		<div class="twc-80">
			<label><?php _e('Sidebar','twc');?> </label>
			<?php echo twc_sidebar_select_box($sidebar_id, $widget, true); ?>
			<div class="clear"></div>
		</div>
				
		<div class="twc-20">
			<label><?php _e('Position','twc');?> </label>
			<?php echo twc_position_select_box($sidebar_id, $position, 'sidebar'); ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	
		<table class="widefat post fixed twc_table menu" id="menu-to-edit" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox" class="twcp"></th>
				<th colspan="2">Objects</th>
				<th style="text-align:right;">Actions</th>
			</tr>
		</thead>
		<?php 
		$imploded = '';
		if (!empty($widget['p']['twc_menu_item']))
		{
			echo twc_get_show_view('twc-menu-item', $widget['p']['twc_menu_item']);
		}
		?>
		</table>
		
		<?php if (empty($widget['p']['twc_menu_item'])): ?>
		<div id="menu-instructions" class="post-body-plain">
			<p><?php _e('Select menu items (pages, categories, links) from the boxes at right to begin adding this widget to specific pages.','twc'); ?></p>
			<p id="default_display_notice"><?php _e('Since no pages have been selected, this widgets default is to display on all pages. This keeps consistency with the default WordPress functionality','twc'); ?></p>
			<br>
		</div>
		<?php endif; ?>
		
		<div class="clear"></div>
	</div>
</div>