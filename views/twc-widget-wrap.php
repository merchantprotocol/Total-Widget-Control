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
$current_screen = twc_get_current_screen();
wp_enqueue_style( 'twc' );
	

?>
<?php do_action('twc_before_wrapper'); ?>
<?php require_once( './admin-header.php' ); ?>

<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2><?php _e('Total Widget Control','twc'); ?>
	<?php do_action('twc_widget_wrap_h2'); ?>
	</h2>

	<form id="twc-widget-wrap" action="#" method="post" enctype="multipart/form-data">
	<?php do_action('twc_display_admin'); ?>
	</form>
	
</div>
<?php require_once( './admin-footer.php' ); ?>