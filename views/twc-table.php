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
<?php byrd_show_view('twc-status'); ?>
<?php byrd_show_view('twc-search-box'); ?>
<div class="tablenav">
	<div class="alignleft actions twcp">
		<?php byrd_show_view('twc-bulk-actions'); ?>
		<?php byrd_show_view('twc-filters'); ?>
	</div>
	<?php byrd_show_view('twc-pagination'); ?>
	<?php byrd_show_view('twc-view-switch'); ?>
	<div class="clear"></div>
</div>
<div class="clear"></div>

<table class="widefat post fixed" cellspacing="0">
	<thead>
	<?php byrd_show_view('twc-table-header'); ?>
	</thead>

	<tfoot>
	<?php byrd_show_view('twc-table-header'); ?>
	</tfoot>

	<tbody>
	<?php byrd_show_view('twc-table-row'); ?>
	</tbody>
</table>

<div class="tablenav">
	<?php byrd_show_view('twc-pagination'); ?>
	
	<div class="alignleft actions">
		<?php byrd_show_view('twc-bulk-actions'); ?>
		<br class="clear">
	</div>
	<br class="clear">
</div>

<input type="hidden" name="inactive" value="<?php echo $_REQUEST['inactive']; ?>" />
