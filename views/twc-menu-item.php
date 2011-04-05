<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.6.0
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');


?>
<tr id="object-<?php echo $menu_item['menu-item-object-id']; ?>" valign="top" class="<?php twc_row_alternate(); ?> author-other status-publish iedit">

	<th scope="row" class="check-column"><input type="checkbox" name="twc_object_bulk[]" value="<?php echo $widget['id']; ?>"></th>
	
	<td class="widget-title column-title" colspan="2">
	<?php if ( array_key_exists('menu-item-title', $menu_item) ): ?>
		<strong>
			<span id="the_title-<?php echo $widget['id']; ?>">
				<?php echo $menu_item['menu-item-object']; ?>	
			</span>	
			<a class="row-title" href="<?php echo admin_url('widgets.php?action=edit&widget_id='.$widget['id']); ?>" title="<?php _e('Edit','twc'); ?> <?php echo $widget['name']; ?>">
				<?php echo $menu_item['menu-item-title']; ?>
			</a>
		</strong>
		<div class="row-actions">
			<?php if (!empty($menu_item['menu-item-url'])) :?>
			<a href="<?php echo $menu_item['menu-item-url']; ?>" target="_blank"><?php echo $menu_item['menu-item-url']; ?></a>
			<?php endif; ?>
		</div>
	<?php else: ?>
		<strong>
			<a href="#" onClick="javascript: twc_ongoing_selection('<?php echo $widget['id']; ?>', '<?php echo $menu_item['menu-item-object-id']; ?>'); return false;">Click To Fix This Object</a>
		</strong>
		<br/> This object needs to be fixed. Click above to reselect the checkbox, then click the "add widget to object" button.
		
	<?php endif; ?>
	</td>
	
	<td style="text-align:right;">
		<div class="row-actions">
			<span class="trash">
				<a id="trash-menu-item-<?php echo $menu_item['menu-item-object-id']; ?>" onClick="javascript: twc.delete_menu_item('<?php echo $widget['id']; ?>', '<?php echo $menu_item['menu-item-object-id']; ?>'); return false;"
				title="<?php _e('Remove','twc');?>" href="#"><?php _e('Remove','twc');?></a>
			</span>
		</div>
	</td>

</tr>