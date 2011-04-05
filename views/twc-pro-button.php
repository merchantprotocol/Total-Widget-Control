<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.5.11
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');

?>
<div id="contextual-pro-wrap" class="contextual-wrap hidden">
	<div>
		<h4><?php _e('Total Widget Control - Help Section','twc'); ?></h4>
		<p><?php _e('Welcome to the 5Twenty Studios Total Widget Control Plugin, for Wordpress 3.0+. This help section is designed to be a quick resource for new administrators. If you have questions and your answers are not found in this window, please visit our community links below.','twc'); ?></p>
		<p><a href="http://community.5twentystudios.com/?kb" target="_blank"><?php _e('TWC Documentation','twc'); ?></a></p>
		<p><a href="http://community.5twentystudios.com/community/" target="_blank"><?php _e('Support Forums','twc'); ?></a></p>
		<p><strong style="color:#555;"><?php _e('Frequently Asked Questions:','twc'); ?></strong></p>
		<p><?php _e('As you hover over the following items, we will display information next to the item in the page.','twc'); ?></p>
		<ul class="hot-to-twc">
		<li id="qtipNewWidget" tooltip="<?php _e('Click here to add a new widget','twc'); ?>"><?php _e('How do I add a new widget?','twc'); ?></li>
		<li id="qtipInactiveWidgets" tooltip="<?php _e('Inactive widgets are stored in the Trash, which is just another name for inactive widget.','twc'); ?>'"><?php _e('Where are my inactive widgets?','twc'); ?></li>
		<li id="qtipSwitchView" tooltip="<?php _e('Click here to switch back to the Wordpress Widget Screen','twc'); ?>'"><?php _e('How do I switch back to the old widget screen?','twc'); ?></li>
		<li id="qtipEditWidget" tooltip="<?php _e('This is the name of the Widget, click here to edit this instance','twc'); ?>"><?php _e('How do I edit a widget?','twc'); ?></li>
		<li id="qtipChooseSidebar" tooltip="<?php _e('As soon as you change the sidebar value, it will update itself.','twc'); ?>"><?php _e('How do I change where on the page my widget will display?','twc'); ?></li>
		</ul>
	</div>
</div>