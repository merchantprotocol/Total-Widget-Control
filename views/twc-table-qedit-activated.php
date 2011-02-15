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

//initializing variables
$widget_id = $_REQUEST['widget_id'];
$widget = twc_get_widget_from_slug( $widget_id );


?>
<td colspan="4">
	<div class="spam-undo-inside">
		<div style="background: transparent url('<?php bloginfo('url'); ?>/wp-admin/images/menu.png') no-repeat scroll -1px -33px;float: left;width: 28px;height: 28px;"></div>
		You have activated <strong><?php echo $widget['name']; ?> : 
		<?php echo $widget['p']['title'];?></strong>. It has been moved to the activated list. 
		<?php /*?>
		<span class="undo unspam">
			<a widget="<?php echo $widget['id']; ?>" onclick="javascript: return twc_deactivate(this);" href="#">
			Undo</a>
		</span>
		*/?>
		<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-feedback" title="" alt="" style="position:relative;margin-left: 3px;top: 4px;" />
	</div>
</td>