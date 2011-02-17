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
if ( TWC_CURRENT_USER_CANNOT ) wp_die();

//initializing variables
$current_screen = twc_get_current_screen();

//controller
if ($current_screen->action != 'auth') return false;


?>
<div class="twc_auth">
	<h1>Total Widget Control Registration</h1>
	<iframe src="http://community.5twentystudios.com/twc-terms-and-conditions/"></iframe>
	
	<input type="submit" name="action" class="button-secondary" value="I Don't Agree" 
	onClick="javascript:window.location.href='<?php bloginfo('url'); ?>/wp-admin/widgets.php?list_style=wp';" />
	
	<input type="submit" name="action" class="button-primary" value="I Agree" 
	onClick="javascript:window.location.href='<?php bloginfo('url'); ?>/wp-admin/widgets.php?action=register';" />
</div>
