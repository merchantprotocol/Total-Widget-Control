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
<?php if ($current_screen->action != 'undo'): ?>
<tr id="tr_row_widget-<?php echo $widget['id']; ?>" valign="top" class="<?php twc_row_alternate(); ?> author-other status-publish iedit">
<?php endif; ?>

	<?php if ($twc_table_type == 'default') :?>
	<th scope="row" class="check-column"><input type="checkbox" name="twcp_bulk[]" value="<?php echo $widget['id']; ?>"></th>
	<?php endif; ?>
	
	<td class="widget-title column-title">
		<strong>
			<a class="row-title" href="<?php echo admin_url('widgets.php?action=edit&widget_id='.$widget['id']); ?>" title="<?php _e('Edit','twc'); ?> <?php echo $widget['name']; ?>">
				<?php echo $widget['name']; ?>
			</a>
			
			<span id="the_title-<?php echo $widget['id']; ?>">
				<?php echo apply_filters('twc_widget_title', ((isset($widget['p']['title'])) ?$widget['p']['title'] :''), $widget); ?>
			</span>
		</strong>
		<br/><?php echo $widget['callback'][0]->widget_options['description']; ?>
		<div class="row-actions">
			<span class="edit"><a title="<?php _e('Edit this item','twc'); ?>" href="<?php echo admin_url('widgets.php?action=edit&widget_id='.$widget['id']); ?>">
			<?php _e('Edit','twc');?></a> | </span>
			
			<?php if (!twc_inactive_list()): ?>
			<span class="inline hide-if-no-js">
				<a onClick="javascript: return twc_load_qedit('<?php echo $widget['id']; ?>');"
				href="#" class="editinline" title="<?php _e('Edit this item inline','twc');?>"><?php _e('Quick Edit','twc');?></a> | 
			</span>
			
			<span class="trash"><a class="ontrash" onClick="javascript: return twc_trash_widget('<?php echo $widget['id']; ?>');"
			title="<?php _e('Move this item to the Trash','twc');?>" href="#"><?php _e('Trash','twc');?></a> | </span>
			<?php else: ?>
			
			<span class="trash">
				<a class="ontrash" onClick="javascript: return twc_delete_permanently('<?php echo $widget['id']; ?>');"
				title="<?php _e('Delete it permanently','twc');?>" href="#"><?php _e('Delete Permanently','twc');?></a>
			</span>
			<?php endif; ?>
		</div>
	</td>
	<td class="" style="white-space: nowrap;overflow: visible;">
		<div style="position:relative;float:left;">
		<?php echo twc_sidebar_select_box($sidebar_id, $widget); ?>
		</div>
		
		<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" 
		class="ajax-feedback" style="position:relative;margin-left: 3px;top: 4px;" />
	</td>
	<td class="date column-date" style="text-align:center;">
		
		<input type="hidden" id="list_type-<?php echo $widget['id']; ?>" 
		value="<?php echo $list_type; ?>"/>
		
		<input type="hidden" name="position-<?php echo $widget['id']; ?>" 
		id="position-<?php echo $widget['id']; ?>" value="<?php echo $position; ?>" />
		
		<?php echo $position+1; ?>
	</td>	

<?php if ($current_screen->action != 'undo'): ?></tr><?php endif; ?>
