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
	<a href="/wp-admin/widgets.php?list_style=twc"><img class="current qtipTwc qtipTopMiddle" id="view-switch-list" 
	src="<?php bloginfo('url'); ?>/wp-includes/images/blank.gif" width="20" 
	height="20" title="Total Widget Control" alt="Total Widget Control"></a>
	
	<a href="/wp-admin/widgets.php?list_style=wp"><img id="view-switch-excerpt" 
	src="<?php bloginfo('url'); ?>/wp-includes/images/blank.gif" width="20" class="qtipTopMiddle"
	height="20" title="Core WordPress View" alt="Core WordPress View"></a>
</div>
