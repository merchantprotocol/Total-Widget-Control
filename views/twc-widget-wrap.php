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
global $wp_version, $is_iphone, $current_user;
$current_screen = twc_get_current_screen();
wp_enqueue_style( 'twc' );
wp_get_current_user();

$hook_suffix = $pagenow = 'widgets.php';
$title = 'Total Widget Control';
$user_identity = $current_user->user_login;

?>
<?php do_action('twc_before_wrapper'); ?>
<?php require_once( './admin-header.php' ); ?>

<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2><?php _e('Total Widget Control','twc'); ?>
	<?php do_action('twc_widget_wrap_h2'); ?>
	</h2>
	
	<?php do_action('twc_notifications'); ?>
	
	<form id="twc-widget-wrap" action="#" method="post" enctype="multipart/form-data">
	<?php do_action('twc_display_admin'); ?>
	</form>
	
	<div id="twc_debug_javascript"></div>
</div> 

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-351858-35']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_setCustomVar', 1, 'WP Version', '<?php echo $wp_version; ?>', 2]);
  _gaq.push(['_setCustomVar', 2, 'TWC Version', '<?php echo TWC_VERSION; ?>', 2]);
  _gaq.push(['_setCustomVar', 3, 'TWC License', '<?php echo TWC_LICENSE; ?>', 2]);
  _gaq.push(['_setCustomVar', 4, 'Sidebars Used', '<?php echo twc_count_sidebars()-1; ?>', 2]);
  _gaq.push(['_setCustomVar', 5, 'Widgets Used', '<?php echo twc_count_active_widgets(); ?>', 2]);
  _gaq.push(['_setCustomVar', 6, 'WP Multisites', '<?php echo (MULTISITE)?'true':'false'; ?>', 2]);
  
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php require_once( './admin-footer.php' ); ?>