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

//reasons to fail
if (twc_inactive_list()) return false;

?>
<?php echo twc_sidebar_filter_box(); ?>
<input onClick="javascript:this.form.submit();" type="submit" id="post-query-submit" value="<?php _e('Filter', 'twc'); ?>" class="button-secondary" name="twcp_submit">
