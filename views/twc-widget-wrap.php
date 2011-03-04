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
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-351858-35']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php require_once( './admin-footer.php' ); ?>