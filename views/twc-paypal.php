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
global $twc_paypal;

?>
<div class="twc_auth">
	<h1><?php _e('Pro Purchase','twc'); ?></h1>
	<p><?php _e('We just checked and couldn\'t find a license for this domain. To purchase a license, click the link below.','twc'); ?></p>
	<p><a href="<?php echo $twc_paypal; ?>" target="_blank"><?php _e('Purchase through Paypal','twc'); ?></a></p>
</div>
