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
<td colspan="4">
	<div class="spam-undo-inside">
		<div style="background: transparent url('<?php bloginfo('url'); ?>/wp-admin/images/menu.png') no-repeat scroll -1px -33px;float: left;width: 28px;height: 28px;"></div>
		You have trashed <strong><?php echo $widget['name']; ?> : 
		<?php echo $widget['p']['title'];?></strong>. It has been moved to the inactive list. 
		<span class="trash">
			<a class="ontrash" onClick="javascript: return twc_delete_permanently('<?php echo $widget['id']; ?>');"
			title="Delete it permanently" href="#">Delete Permanently</a>
		</span>
		<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-feedback" title="" alt="" style="position:relative;margin-left: 3px;top: 4px;" />
	</div>
</td>