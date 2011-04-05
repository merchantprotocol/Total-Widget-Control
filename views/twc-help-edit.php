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
	<h4><?php _e('Total Widget Control','twc'); ?> - <?php _e('Editing a Widget','twc'); ?></h4>
	<p><?php _e('This is where all the magic happens. Let\'s first take a little tour of this area. It\'s best to start with the items that you\'re already familiar with, so direct your attention to the <span id="twcWidgetsOptions" class="twcTooltip" tooltip="This is the Widget Options form.">Widget Options</span> form. This form is the standard widget form that you\'ve always worked with, however, you can see that it\'s a bit larger.','twc'); ?></p>
	<p><?php _e('Next, you\'ll notice the <span id="twcNavMenu" class="twcTooltip" tooltip="Check the boxes next to your pages, then click the Add Widget to Pages button. Changes are saved immediately.">nav menu items</span> on the right, under your publishing options. These are the nav menu links, just as you\'ve seen them on the menu editing page. Here, you can check the pages that you would like your widget to display on.','twc'); ?></p>
	
	<p><strong style="color:#555;"><?php _e('Frequently Asked Questions:'); ?></strong></p>
	<p><?php _e('As you hover over the following items, we will display information next to the item in the page.','twc'); ?></p>
	<ul class="hot-to-twc">
		<li id="qtipDisplayMetaBox"><?php _e('The meta box for my page type is not showing, what do i do?','twc'); ?>
		<span class="sub-hidden"><img width="600px" src="<?php echo plugin_dir_url(dirname(__FILE__)); ?>images/help_file_one.png"/></span></li>
		<li id="qtipDefaultWidget" tooltip="<?php _e('Check this option to have this widget display when there are no other widgets set to display.','twc'); ?>">
		<?php _e('How do I create a widget that will display if there are no other widgets set to display for the page?','twc'); ?></li>
		<li><a href="http://totalwidgetcontrol.com/questions-answers/"><?php _e('Ask the developers a question, click here.', 'twc'); ?></a></li>
	</ul>
</div>