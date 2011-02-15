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



?>
<?php if ($current_screen->action != 'undo'): ?>
<tr id="tr_row_widget-<?php echo $widget['id']; ?>" valign="top"
class="<?php twc_row_alternate(); ?> author-other status-publish iedit">
<?php endif; ?>

	<th scope="row" class="check-column"><input type="checkbox" name="twcp_bulk[]" value="<?php echo $widget['id']; ?>"></th>
	<td class="widget-title column-title">
		<strong>
			<a class="row-title" href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?action=edit&widget_id=<?php echo $widget['id']; ?>" title="Edit <?php echo $widget['name']; ?>">
				<?php echo $widget['name']; ?>
			</a>
			
			<span id="the_title-<?php echo $widget['id']; ?>">
				<?php echo apply_filters('twc_widget_title', ((isset($widget['p']['title'])) ?$widget['p']['title'] :'')); ?>
			</span>
		</strong>
		<br/><?php echo $widget['callback'][0]->widget_options['description']; ?>
		<div class="row-actions">
			<span class="edit"><a title="Edit this item"
			href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?action=edit&widget_id=<?php echo $widget['id']; ?>">
			Edit</a> | </span>
			
			<?php if (!twc_inactive_list()): ?>
			<span class="inline hide-if-no-js">
				<a onClick="javascript: return twc_load_qedit('<?php echo $widget['id']; ?>');"
				href="#" class="editinline" title="Edit this item inline">Quick&nbsp;Edit</a> | 
			</span>
			
			<span class="trash"><a class="ontrash" onClick="javascript: return twc_trash_widget('<?php echo $widget['id']; ?>');"
			title="Move this item to the Trash" href="#">Trash</a> | </span>
			<?php else: ?>
			
			<span class="trash">
				<a class="ontrash" onClick="javascript: return twc_delete_permanently('<?php echo $widget['id']; ?>');"
				title="Delete it permanently" href="#">Delete Permanently</a>
			</span>
			<?php endif; ?>
			
		<?php /*?>
			<span class="approve"><a href="comment.php?action=approvecomment&amp;p=138&amp;c=2&amp;_wpnonce=ef58137324" class="dim:the-comment-list:comment-2:unapproved:e7e7d3:e7e7d3:new=approved vim-a" title="Enable this widget">Enable</a> | </span>
			<span class="unapprove"><a href="comment.php?action=unapprovecomment&amp;p=138&amp;c=2&amp;_wpnonce=ef58137324" class="dim:the-comment-list:comment-2:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u" title="Disable this widget">Disable</a> | </span>
			<span class="view"><a href="<?php bloginfo('url'); ?>" title="View <?php echo $widget['name']; ?>" rel="permalink">View</a></span>
		<?php */?>
		</div>
	</td>
	<td class="" style="white-space: nowrap;">
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
