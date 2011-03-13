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
global $widget;


?>
<td colspan="4">
	<div class="spam-undo-inside">
		<div style="background: transparent url('<?php echo admin_url('images/menu.png'); ?>') no-repeat scroll -1px -33px;float: left;width: 28px;height: 28px;"></div>
		<?php _e('You have activated','twc'); ?> <strong><?php echo $widget['name']; ?>
		<?php echo (isset($widget['p']['title'])) ?' : '.$widget['p']['title'].'.':'';?></strong> 
		<?php _e('It has been moved to the activated list.','twc'); ?> 
		<?php /*?>
		<span class="undo unspam">
			<a widget="<?php echo $widget['id']; ?>" onclick="javascript: return twc_deactivate(this);" href="#">
			Undo</a>
		</span>
		*/?>
		<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-feedback" title="" alt="" style="position:relative;margin-left:3px;top:4px;" />
	</div>
</td>