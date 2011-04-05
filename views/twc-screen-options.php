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
<h4><?php _e('Total Widget Control','twc');?> - <?php _e('Settings','twc');?></h4>
<p><?php _e('This is the configurations area for the TWC. Any options to adjust the systems operation can be found here.','twc');?></p>
<ul class="hot-to-twc">
	<li><?php _e('Access the licensing screen here: <a href="'.admin_url('widgets.php?action=auth').'">License Comparison</a>', 'twc'); ?></li>
	<li><?php _e('Sometimes you may need to clear the current license, <a href="'.admin_url('widgets.php?twc_clear_license').'">Click here</a>', 'twc'); ?></li>
	<li><?php _e('Pro License holders can download their license here: ', 'twc'); ?><a href="<?php twc_get_license_link(); ?>"><?php _e('Click to download your pro license.','twc'); ?></a></li>
	<li><?php _e('Enable a license manually here: <a href="'.admin_url('widgets.php?action=manual').'">Manual License Activation</a>', 'twc'); ?></li>
</ul>
<hr class="twc-hr"/>
<div class="twc-share">
	<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.totalwidgetcontrol.com%2F&amp;layout=box_count&amp;show_faces=false&amp;width=50&amp;action=like&amp;colorscheme=light&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:65px;margin-bottom: -5px;" allowTransparency="true"></iframe>
	
	<a href="http://twitter.com/share" class="twitter-share-button" data-url="http://www.totalwidgetcontrol.com" data-text="<?php _e('The WordPress Total Widget Control Plugin is an amazing plugin!','twc'); ?>" data-count="vertical" data-via="twcpro">Tweet</a>
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	
	<a class="DiggThisButton DiggMedium" href="http://digg.com/submit?url=http%3A%2F%2Fwww.totalwidgetcontrol.com&bodytext=Total%20Widget%20Control%20allows%20you%20to%20customize%20any%20and%20every%20page%20of%20your%20WordPress%20website.">
		<img src="http://developers.diggstatic.com/sites/all/themes/about/img/digg-btn.jpg" alt="<?php _e('Digg Total Widget Control','twc'); ?>" title="<?php _e('Digg Total Widget Control','twc'); ?>" />
		<?php _e('Total Widget Control','twc'); ?>
	</a>
	<script type="text/javascript">
	(function() {
	var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
	s.type = 'text/javascript';
	s.async = true;
	s.src = 'http://widgets.digg.com/buttons.js';
	s1.parentNode.insertBefore(s, s1);
	})();
	</script>
</div>

<img class="twc-avatar" src="<?php echo plugin_dir_url(dirname(__file__)); ?>images/520logo.png" />
<h4 style="margin:0px;"><?php _e('TWC Support','twc');?></h4>
<p><?php _e('<a href="http://www.totalwidgetcontrol.com">This Plugin</a> is provided by 5Twenty Studios. Please support us, <br/>so that we can continue to support you.','twc');?></p>
