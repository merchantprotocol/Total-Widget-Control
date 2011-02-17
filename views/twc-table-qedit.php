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
<tr id="edit-<?php echo $widget_id; ?>" class="alternate inline-edit-row inline-edit-row-page quick-edit-row quick-edit-row-page inline-editor" style="width:100%;">
<td colspan="4">
	<?php echo $sidebar_args['before_widget']; ?>
	
	<div class="widget-inside">
		<div class="widget-content">
		<?php
		if ( isset($control['callback']) )
		{
			$has_form = call_user_func_array( $control['callback'], $control['params'] );
		}
		else
		{
			echo "\t\t<p>" . __('There are no options for this widget.') . "</p>\n"; 
		}
		?>
		</div>
		
		<input type="hidden" name="widget-<?php echo esc_attr($id_base); ?>[<?php echo esc_attr($widget_number); ?>][widget-id]" class="widget-id" value="<?php echo esc_attr($id_format); ?>" />
		<input type="hidden" name="widget-<?php echo esc_attr($id_base); ?>[<?php echo esc_attr($widget_number); ?>][id_base]" class="id_base" value="<?php echo esc_attr($id_base); ?>" />
		<input type="hidden" name="widget-<?php echo esc_attr($id_base); ?>[<?php echo esc_attr($widget_number); ?>][widget-width]" class="widget-width" value="<?php if (isset( $control['width'] )) echo esc_attr($control['width']); ?>" />
		<input type="hidden" name="widget-<?php echo esc_attr($id_base); ?>[<?php echo esc_attr($widget_number); ?>][widget-height]" class="widget-height" value="<?php if (isset( $control['height'] )) echo esc_attr($control['height']); ?>" />
		<input type="hidden" name="widget-<?php echo esc_attr($id_base); ?>[<?php echo esc_attr($widget_number); ?>][widget_number]" class="widget_number" value="<?php echo esc_attr($widget_number); ?>" />
		<input type="hidden" name="widget-<?php echo esc_attr($id_base); ?>[<?php echo esc_attr($widget_number); ?>][multi_number]" class="multi_number" value="<?php echo esc_attr($widget_number); ?>" />
		
		<div class="widget-control-actions">
			<div class="alignleft">
				<a class="widget-control-remove" onClick="javascript:return twc_trash_widget('<?php echo $widget_id; ?>');" 
				href="#remove"><?php _e('Deactivate'); ?></a> |
				
				<a class="widget-control-close" onClick="javascript:return twc_close_qedit('<?php echo $widget_id; ?>');" 
				href="#close"><?php _e('Close'); ?></a>
			</div>
			<div class="alignright<?php if ( 'noform' === $has_form ) echo ' widget-control-noform'; ?>">
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" 
				class="ajax-feedback"/>
				
				<input onClick="javascript: twc_save_widget_qedit('<?php echo $widget_id; ?>');" 
				type="button" name="submit" class="button-primary" value="<?php esc_attr_e('Save'); ?>" />
			</div>
			<br class="clear" />
		</div>
	</div>
	
	<?php echo $sidebar_args['after_widget']; ?>
	
</td>
</tr><?php _520(); ?>