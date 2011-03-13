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
<?php twc_show_view('twc-status'); ?>
<?php twc_show_view('twc-search-box'); ?>
<div class="tablenav">
	<div class="alignleft actions twcp">
		<?php twc_show_view('twc-bulk-actions'); ?>
		<?php twc_show_view('twc-filters'); ?>
	</div>
	<?php twc_show_view('twc-pagination'); ?>
	<?php twc_show_view('twc-view-switch'); ?>
	<div class="clear"></div>
</div>
<div class="clear"></div>

<table class="widefat post fixed twc_table" cellspacing="0">
	<thead>
	<?php twc_show_view('twc-table-header'); ?>
	</thead>

	<tfoot>
	<?php twc_show_view('twc-table-header'); ?>
	</tfoot>

	<tbody>
	<?php do_action('twc-table-rows'); ?>
	</tbody>
</table>

<div class="tablenav">
	<?php twc_show_view('twc-pagination'); ?>
	
	<div class="alignleft actions">
		<?php twc_show_view('twc-bulk-actions'); ?>
		<br class="clear">
	</div>
	<br class="clear">
</div>

<input type="hidden" name="inactive" value="<?php echo $_REQUEST['inactive']; ?>" />
