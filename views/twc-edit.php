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
global $widget;
 
?>
<div class="head_space"></div>
<div id="nav-menus-frame" class="widget-<?php echo $widget['id_base']; ?>">
	<div id="menu-settings-column" class="metabox-holder">
		<?php do_action('twc_nav_menu_list'); ?>
	</div>
	<div id="menu-management-liquid">
		<div id="poststuff" class="metabox-holder meta-box-sortables">
			<?php do_action('twc_widget_metabox'); ?>
		</div>
	</div>
</div>

<?php return; ?>

<div id="postexcerpt" class="postbox">
	<div class="handlediv" title="<?php _e('Click to toggle','twc'); ?>"><br></div>
	<h3 class="hndle">
		<span><?php echo $widget['name']; ?></span>
		<div class="clear"></div>
		<div style="font-weight:normal;font-size:11px;"><?php _e('ID', 'twc'); ?>: <?php echo $widget['id']; ?></div>
	</h3>
	<div class="inside">

	</div>
</div>