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
<div class="view-switch">
	<a href="<?php echo admin_url('widgets.php?list_style=twc'); ?>"><img class="current qtipTwc qtipTopMiddle" id="view-switch-list" 
	src="<?php echo plugin_dir_url(dirname(__file__)); ?>images/blank.gif" width="20" 
	height="20" title="<?php _e('Total Widget Control','twc'); ?>" alt="<?php _e('Total Widget Control','twc'); ?>"></a>
	
	<a href="<?php echo admin_url('widgets.php?list_style=wp'); ?>"><img id="view-switch-excerpt" 
	src="<?php echo plugin_dir_url(dirname(__file__)); ?>images/blank.gif" width="20" class="qtipTopMiddle"
	height="20" title="<?php _e('Core WordPress View','twc'); ?>" alt="<?php _e('Core WordPress View','twc'); ?>"></a>
</div>
