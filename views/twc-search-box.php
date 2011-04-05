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
<p class="search-box twcp">
	<label class="screen-reader-text" for="post-search-input"><?php _e('Search Widgets:','twc');?></label>
	<input type="text" id="post-search-input" name="twcp_search_input" value="<?php echo twc_search_list_for(); ?>">
	<input type="submit" value="<?php _e('Search Widgets','twc');?>" name="twcp_submit" class="button" onClick="javascript:this.form.submit();">
</p>