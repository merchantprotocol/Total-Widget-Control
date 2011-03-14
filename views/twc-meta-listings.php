<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.5.2
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');

//initializing variables
global $twc_table_type, $twc_widgetlistings_type, $twc_rows;
$twc_widgetlistings_type = 'admin';
$twc_table_type = 'simple';

//initializing
twc_rows('on-page');

?>
<link rel='stylesheet' href='<?php echo plugin_dir_url(dirname(__file__)); ?>css/twc.css' type='text/css' media='all' /> 
<script src="<?php echo plugin_dir_url(dirname(__file__)); ?>js/twc.js"></script>

<table class="widefat post fixed twc_table twc_meta_listings_table" cellspacing="0">
	<thead>
	<?php twc_show_view('twc-table-header'); ?>
	</thead>
	
	<tfoot>
	<?php twc_show_view('twc-table-header'); ?>
	</tfoot>
		
	<tbody>
	<?php if ($twc_rows): ?>
		<?php twc_show_view('twc-table-row'); ?>
	<?php else: ?>
		<tr id="twc_no_rows"><td colspan="100"><br/><p><?php _e('There are no widgets set to display on this page. The defaults will display instead.','twc'); ?></p></td></tr>
	<?php endif; ?>
	</tbody>
</table>
