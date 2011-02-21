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
	<h4>Total Widget Control - Add new Widget Help</h4>
	<p>You are currently viewing the first step to adding a new widget instance. What you 
	see here, is a list of <span id="twcAvailableWidgets" class="twcTooltip" tooltip="This is your list of Available Widgets.">Available Widgets</span> that you may use on your website.
	To begin editing a new widget, simply click on the small text next to the widget
	title that says <span id="twcAdd" class="twcTooltip" tooltip="Click here to add this widget.">"Add."</span></p>
	<p><a href="http://community.5twentystudios.com/software-products/total-widget-control/extra-widgets/" target="_blank">Download more Widgets</a></p>
	
	<p><strong style="color:#555;"><?php echo __('Frequently Asked Questions:'); ?></strong></p>
	<ul class="hot-to-twc">
	<li>I don't see the "Add" button next to the widget titles. <a href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?widgets-access=on&action=add">Click here to enable accessibility mode.</a></li>
	</ul>
</div>