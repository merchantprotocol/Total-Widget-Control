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
<div>
	<h4><?php _e('Total Widget Control', 'twc');?> - <?php _e('Add new Widget Help', 'twc'); ?></h4>
	<p><?php _e('You are currently viewing the first step to adding a new widget instance. What you see here, is a list of <span id="twcAvailableWidgets" class="twcTooltip" tooltip="This is your list of Available Widgets.">Available Widgets</span> that you may use on your website. To begin editing a new widget, simply click on the small text next to the widget title that says <span id="twcAdd" class="twcTooltip" tooltip="Click here to add this widget.">"Add."</span>', 'twc'); ?></p>
	<p><a href="http://community.5twentystudios.com/software-products/total-widget-control/extra-widgets/" target="_blank"><?php _e('Download more Widgets','twc'); ?></a></p>
	
	<p><strong style="color:#555;"><?php _e('Frequently Asked Questions:'); ?></strong></p>
	<ul class="hot-to-twc">
	<li><?php _e('I don\'t see the "Add" button next to the widget titles.','twc'); ?> 
	<a href="<?php echo admin_url('widgets.php?widgets-access=on&action=add'); ?>"><?php _e('Click here to enable accessibility mode.','twc'); ?></a></li>
	</ul>
</div>