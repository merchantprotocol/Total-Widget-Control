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
<!--[if IE]>
<script src="<?php echo plugin_dir_url(dirname(__file__)); ?>js/html5.js" type="text/javascript"></script>
<![endif]-->
<div class="twc_auth_wrapper">
	<div class="twc_auth_right">
		<div class="twc_auth_widget" style="text-align: center;">
			<img src="<?php echo plugin_dir_url(dirname(__file__)); ?>images/520logo.png" />
			<div class="clear"></div>
			5Twenty Studios, LLC
			Portland, OR <br/>
			Phone: (503) 268-1177 <br/>
			Fax: (954) 867-1177 <br/>
		</div>
		<div class="twc_auth_widget" style="text-align: center;padding-top:20px;">
			<a href="https://www.paypal.com/us/verified/pal=tylerbyrd%405twentystudios%2ecom" target="_blank" style="display:inline-block;">
			<img src="<?php echo plugin_dir_url(dirname(__file__)); ?>images/pp.png" border="0" alt="Official PayPal Seal">
			</a>
			<img src="<?php echo plugin_dir_url(dirname(__file__)); ?>images/Money-Back-Guarantee1.png" width="105px" style="padding-left:20px;" border="0" alt="Official Money Back Guarantee">
		</div>
		<div class="twc_auth_widget" style="text-align: center;padding-top:20px;">
			<a href="http://www.totalwidgetcontrol.com/" target="_blank" style="display:inline-block;">
			<img src="<?php echo plugin_dir_url(dirname(__file__)); ?>images/24-7-customer-support.png"/></a>
		</div>
	</div>

<div class="grid" id="griddler_ii">
<aside>
	<ul>
		<li><?php _e('Display By Page','twc'); ?></li>
		<li><?php _e('Admin Quick Edit','twc'); ?></li>
		<li><?php _e('Dynamic Defaults','twc'); ?></li>
		<li><?php _e('Display By Role','twc'); ?></li>
		<li><?php _e('Publish Status','twc'); ?></li>
		<li><?php _e('Publish Date','twc'); ?></li>
		<li><?php _e('Custom Titles','twc'); ?></li>
		<li><?php _e('Custom Styles','twc'); ?></li>
		<li><?php _e('Bulk Actions','twc'); ?></li>
		<li><?php _e('Filtering','twc'); ?></li>
		<li><?php _e('Searching','twc'); ?></li>
		<li><?php _e('Pagination','twc'); ?></li>
		<li><?php _e('Ad Free','twc'); ?></li>
		<li><?php _e('Branding Free','twc'); ?></li>
	</ul>
</aside>
<div class="sections col5">
<div>

<article class="first"> 
	<header> 
		<hgroup class="plan">
			<h1><?php _e('Lite','twc'); ?></h1>
		</hgroup>
		<hgroup class="price">
			<h2><?php _e('Free<em>License</em>','twc'); ?></h2>
		</hgroup> 
	</header> 
	<section>
	<ul>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
	</ul>
	</section> 
	<footer> 
		<a class="button" href="<?php echo admin_url('widgets.php?action=register&license=0'); ?>">
		<span>FREE</span></a>
	</footer>
</article>

<article>
	<header>
		<hgroup class="plan">
			<h1><?php _e('Professional','twc'); ?></h1>
		</hgroup> <hgroup class="price">
			<h2><?php _e('$9.99<em>License</em>','twc'); ?></h2>
			<h4 class="label">Best Value</h4>
		</hgroup>
	</header>
	<section>
	<ul>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li class="tooltip-holder"><span class="check"><?php _e('Dynamic','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('Dynamic Default Sidebars','twc'); ?></h3>
					<p><?php _e('When no widgets are set to display, then your default widgets
					will display. No more needing to hard code sidebars that will
					display when there are no active widgets.','twc'); ?></p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check"><?php _e('Any Role','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('Display Widgets by Role','twc'); ?></h3>
					<p><?php _e('Display ads to only guests and display user menus to anybody that\'s
					logged in, it\'s your choice now.','twc'); ?></p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check"><?php _e('Enable/Disable','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('Turn Widgets On and Off','twc'); ?></h3>
					<p><?php _e('Now, you can do this without losing any of your widget settings,
					or even the widgets sidebar and position data.','twc'); ?></p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check"><?php _e('Date and Time','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('Just Like Posts','twc'); ?></h3>
					<p><?php _e('By default, widgets display immediately, or you can choose the
					exact date and time for a widget to publish itself.','twc'); ?></p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check"><?php _e('For Admins Only','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('Custom Admin Titles','twc'); ?></h3>
					<p><?php _e('By default, titles are handled by the widget developers. 
					Unfortunately, these titles will also display to your users.</p>
					<p>With TWC Pro, you can now create your own titles that can be
					used to differentiate between the hundreds of widget instances
					that you\'re now able to manage with ease.','twc'); ?></p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check"><?php _e('Create Your Own','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('Style Widgets Instances!','twc'); ?></h3>
					<p><?php _e('This is what separates TWC from any other widget management 
					system that you\'ve ever considered. You can now create style 
					wrappers for your widgets that will allow you to change the complete
					look and feel of each widget instance.','twc'); ?></p>
					<p><?php _e('With ease!','twc'); ?></p>
				</div>
			</div>
		</li>
		<li><span class="check">Yes</span></li>
		<li class="tooltip-holder"><span class="check"><?php _e('By Sidebar','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('Makes Hunting Easier','twc'); ?></h3>
					<p><?php _e('Allows you to list widgets from a specific location on your site.','twc'); ?></p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check"><?php _e('By Title','twc'); ?></span>
			<div class="tooltip">
				<div>
					<h3><?php _e('So Nice!','twc'); ?></h3>
					<p><?php _e("Can't find the widget that you just created? EASY, just search.",'twc'); ?></p>
				</div>
			</div>
		</li>
		<li><span class="check">Yes</span></li>
		<li><span class="no">No</span></li>
		<li><span class="no">No</span></li>
	</ul>
	</section>
	<footer>
		<a class="button" href="<?php echo admin_url('widgets.php?action=register&license=1'); ?>">
		<span>$9.99</span></a>
	</footer>
</article>

<article class="last">
	<header>
		<hgroup class="plan">
			<h1><?php _e('Enterprise','twc'); ?></h1>
		</hgroup>
		<hgroup class="price">
			<h2><?php _e('$19.99<em>License</em>','twc'); ?></h2>
		</hgroup>
	</header>
	<section>
	<ul>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">Yes</span></li>
	</ul>
	</section>
	<footer>
	<a class="button" href="<?php echo admin_url('widgets.php?action=register&license=2'); ?>">
	<span>$19.99</span></a>
	</footer>
</article>

</div>
</div>
</div>

<div class="clear"></div>
</div> <!-- auth wrapper -->

<div class="twc_toc"><a href="http://community.5twentystudios.com/twc-terms-and-conditions/" target="_blank">
<?php _e('By choosing a plan, you agree to the Terms of Use.','twc'); ?></a></div>

<p style="padding-top:10px;border-top:1px solid #ccc;color:#666666;font-size:11px">
	<?php _e('5Twenty Studios values your privacy. At no time has 5Twenty Studios made your email address available to any other 5Twenty Studios user without your permission. &copy;2011, 5Twenty Studios Corporation.','twc'); ?>
</p>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-351858-35']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_setCustomVar', 1, 'WP Version', '<?php echo $wp_version; ?>', 2]);
  
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php require_once( './admin-footer.php' ); exit(); ?>