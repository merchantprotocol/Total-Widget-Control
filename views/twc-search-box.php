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



if (twc_inactive_list()) return false;

?>
<p class="search-box twcp">
	<label class="screen-reader-text" for="post-search-input">Search Widgets:</label>
	<input type="text" id="post-search-input" name="twcp_search_input" value="">
	<input type="submit" value="Search Widgets" name="twcp_submit" class="button">
</p>
