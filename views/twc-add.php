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
<div class="head_space">
	<div class="back_button">
		<a class="button-secondary" id="twc-go-back" href="javascript:history.go(-1);"><?php _e('Go Back', 'twc'); ?></a>
	</div>
</div>
<div class="widget-liquid-left">
	<div id="widgets-left">
		<div id="available-widgets" class="widgets-holder-wrap">
			<div class="sidebar-name">
				<h3><?php _e('Available Widgets', 'twc'); ?></h3>
			</div>
			<div class="widget-holder">
				<div id="widget-list">
					<?php wp_list_widgets(); ?>
				</div>
				<br class="clear" />
			</div>
			<br class="clear" />
		</div>
	</div>
</div>