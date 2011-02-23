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

//initializing variables
$current_screen = twc_get_current_screen();

//controller
if ($current_screen->action != 'auth') return false;


?>
<div class="twc_auth">
	<h1><?php _e('Total Widget Control Registration', 'twc'); ?></h1>
	<iframe src="http://community.5twentystudios.com/twc-terms-and-conditions/"></iframe>
	
	<div class="buy_option">
		<label>
		<input type="radio" name="license" value="1" checked="checked"/>
		<span><?php _e("I'd like to purchase a pro license, for only $9.", 'twc'); ?></span>
		</label>
	</div>
	<div class="buy_option">
		<label>
		<input type="radio" name="license" value="2"/>
		<span><?php _e("I've already purchased a pro license.", 'twc'); ?></span>
		</label>
	</div>
	<div class="buy_option">
		<label>
		<input type="radio" name="license" value="0"/>
		<span><?php _e("I'll just try the free version, thanks!", 'twc'); ?></span>
		</label>
	</div>
	
	<input type="button" name="action" class="button-secondary" value="<?php _e("I Don't Agree", 'twc'); ?>" 
	onClick="javascript:window.location.href='<?php bloginfo('url'); ?>/wp-admin/widgets.php?list_style=wp';" />
	
	<input type="hidden" name="action" value="register"/>
	<input type="submit" class="button-primary" value="<?php _e("I Agree", 'twc'); ?>/>
	<div class="clear"></div>
</div>
<script>
jQuery(document).ready(function(){
	jQuery('#twc-widget-wrap').unbind('submit');
});
</script>
