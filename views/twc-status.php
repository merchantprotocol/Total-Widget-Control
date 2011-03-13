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
<ul class="subsubsub">
	<li><a href="<?php echo admin_url('widgets.php'); ?>" <?php echo ((!twc_inactive_list())? 'class="current"':''); ?>>
	<?php _e('Active','twc'); ?> 
	<span class="count">(<?php echo twc_count_active_widgets(); ?>)</span></a> |
	</li>
	<li id="twcInactiveWidgetsArea">
		<a href="<?php echo admin_url('widgets.php?inactive=inactive'); ?>" <?php echo ((twc_inactive_list())? 'class="current"':''); ?>>
			<?php _e('Trash','twc'); ?> 
			<span class="count">(<?php echo twc_count_inactive_widgets(); ?>)</span>
		</a>
	</li>
</ul>
