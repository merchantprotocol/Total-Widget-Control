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
<div class="twc_auth">
	<h1><?php _e('Manual License Upload','twc'); ?></h1>
	<p><?php _e('If you have already purchased a license for this domain, then you can click the link below to download it directly.','twc'); ?></p>
	<p><a href="<?php twc_get_license_link(); ?>"><?php _e('Click to download your Professional license.','twc'); ?></a></p>
	<p><a href="<?php twc_get_license_link('twc-ent'); ?>"><?php _e('Click to download your Enterprise license.','twc'); ?></a></p>
	<p><?php _e('Choose a license file to upload:','twc'); ?> 
	<input name="license" type="file" /><br />
	<input type="submit" value="Upload File" onClick="javascript:jQuery('#twc-widget-wrap').unbind('submit');jQuery('#twc-widget-wrap').submit();return true;" />
	</p>
</div>
