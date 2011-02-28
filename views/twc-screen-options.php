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
$uniqueID = get_option('twc_unique_registration_key', create_guid());
$parts = parse_url("http:/"."/".$_SERVER["SERVER_NAME"]);
$domain = $parts["host"];
$headers = get_plugin_data( dirname(dirname(__file__)).DS.'index.php' );

?>
<iframe style="background:transparent;" src="http://community.5twentystudios.com/5twenty-studios-tab/" width="100%" height="200px"></iframe>
<ul class="hot-to-twc">
	<li><?php _e('Access the licensing screen here: <a href="'.get_bloginfo('url').'/wp-admin/widgets.php?action=auth">License Comparison</a>', 'twc'); ?></li>
	<li><?php _e('Sometimes you may need to clear the current license, <a href="'.get_bloginfo('url').'/wp-admin/widgets.php?twc_clear_license">Click here</a>', 'twc'); ?></li>
	<li><?php _e('Pro License holders can download their license here: ', 'twc'); ?><a href="http://community.5twentystudios.com/?view=download-license&uniqueID=<?php echo $uniqueID; ?>||<?php echo urlencode($domain); ?>&ver=twc-pro||<?php echo urlencode($headers['Version']); ?>">Click to download your pro license.</a></li>
	<li><?php _e('Enable a license manually here: <a href="'.get_bloginfo('url').'/wp-admin/widgets.php?action=manual">Manual License Activation</a>', 'twc'); ?></li>
</ul>
