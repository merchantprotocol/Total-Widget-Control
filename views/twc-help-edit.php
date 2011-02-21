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
	<h4>Total Widget Control - Editing a Widget</h4>
	<p>This is where all the magic happens. Let's first take a little tour of this area. 
	It's best to start with the items that you're already familiar with, so direct your 
	attention to the <span id="twcWidgetsOptions" class="twcTooltip" 
	tooltip="This is the Widget Options form.">Widget Options</span> form. This form is 
	the standard widget form that you've always worked with, however, you can see that 
	it's a bit larger.</p>
	<p>Next, you'll notice the <span id="twcNavMenu" class="twcTooltip" 
	tooltip="Click the checkboxes to make your selection. When you're done, don't forget to save.">
	nav menu items</span> left/center. These are the nav menu links,
	just as you've seen them on the edit menu page. Here, you can check the pages that you 
	would like your widget to display on. Make sure to save.</p>
	<p>And finally, the <span id="twcWidgetControl" class="twcTooltip" 
	tooltip="These are your advanced options for widget display.">
	Widget Control Settings</span> box. The options found here, are the display options
	for the current widget. These items should be pretty self explanatory, however, if 
	you do have questions regarding the use of these options, please refer to the 
	online documentation.</p>
	
	<p><strong style="color:#555;"><?php echo __('Frequently Asked Questions:'); ?></strong></p>
	<p>As you hover over the following items, we will display information next to the 
	item in the page.</p>
	<ul class="hot-to-twc">
	<li id="qtipDisplayMetaBox">The meta box for my page type is not showing, what do i do? 
	<span class="sub-hidden"><img width="600px" src="<?php echo plugin_dir_url(dirname(__FILE__)); ?>images/help_file_one.png"/></span></li>
	<li id="qtipDefaultWidget" tooltip="Check this option to have this widget display when there are no other widgets set to display.">How do I create a widget that will display if there are no other widgets set to display for the page?</li>
	</ul>
</div>