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

//controller
if ($current_screen->action != 'auth') return false;


?>
<div class="grid" id="griddler_ii" style="height: 529px;">
<div class="twc_toc"><a href="http://community.5twentystudios.com/twc-terms-and-conditions" target="_blank">
<?php _e('By choosing a plan, you agree to the Terms of Use.','twc'); ?></a></div>
<aside>
	<ul>
		<li>Display By Page</li>
		<li>Display By Role</li>
		<li>Publish Status</li>
		<li>Publish Date</li>
		<li>Default Sidebar</li>
		<li>Custom Titles</li>
		<li>Custom Styles</li>
		<li>Bulk Actions</li>
		<li>Filtering</li>
		<li>Searching</li>
		<li>Pagination</li>
		<li>Ad Free</li>
		<li>Branding Free</li>
	</ul>
</aside>
<div class="sections col5">
<div>

<article class="first"> 
	<header> 
		<hgroup class="plan">
			<h1>Personal</h1>
		</hgroup>
		<hgroup class="price">
			<h2>Free<em>Always</em></h2>
		</hgroup> 
	</header> 
	<section>
	<ul>
		<li><span>Any Page</span></li>
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
		<a class="button" href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?action=register&license=0">
		<span>really?</span></a>
	</footer>
</article>

<article>
	<header>
		<hgroup class="plan">
			<h1>Commercial</h1>
		</hgroup> <hgroup class="price">
			<h2>$9<em>License</em></h2>
			<h4 class="label">Best Value</h4>
		</hgroup>
	</header>
	<section>
	<ul>
		<li><span>Any Page</span></li>
		<li class="tooltip-holder"><span class="check">Any Role</span>
			<div class="tooltip">
				<div>
					<h3>Display Widgets by Role</h3>
					<p>Display ads to only guests and display user menus to anybody that's
					logged in, it's your choice now.</p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check">Enable/Disable</span>
			<div class="tooltip">
				<div>
					<h3>Turn Widgets On and Off</h3>
					<p>Now, you can do this without losing any of your widget settings,
					or even the widgets sidebar and position data.</p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check">Date and Time</span>
			<div class="tooltip">
				<div>
					<h3>Just Like Posts</h3>
					<p>By default, widgets display immediately, or you can choose the
					exact date and time for a widget to publish itself.</p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check">Dynamic</span>
			<div class="tooltip">
				<div>
					<h3>Dynamic Default Sidebars</h3>
					<p>When no widgets are set to display, then your default widgets
					will display. No more needing to hard code sidebars that will
					display when there are no active widgets.</p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check">For Admins Only</span>
			<div class="tooltip">
				<div>
					<h3>Custom Admin Titles</h3>
					<p>By default, titles are handled by the widget developers. 
					Unfortunately, these titles will also display to your users.</p>
					<p>With TWC Pro, you can now create your own titles that can be
					used to differentiate between the hundreds of widget instances
					that you're now able to manage with ease.</p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check">Create Your Own</span>
			<div class="tooltip">
				<div>
					<h3>Style Widgets Instances!</h3>
					<p>This is what separates TWC from any other widget management 
					system that you've ever considered. You can now create style 
					wrappers for your widgets that will allow you to change the complete
					look and feel of each widget instance.</p>
					<p>With ease!</p>
				</div>
			</div>
		</li>
		<li><span class="check">Yes</span></li>
		<li class="tooltip-holder"><span class="check">By Sidebar</span>
			<div class="tooltip">
				<div>
					<h3>Makes Hunting Easier</h3>
					<p>Allows you to list widgets from a specific location on your site.</p>
				</div>
			</div>
		</li>
		<li class="tooltip-holder"><span class="check">By Title</span>
			<div class="tooltip">
				<div>
					<h3>So Nice!</h3>
					<p>Can't find the widget that you just created? EASY, just search.</p>
				</div>
			</div>
		</li>
		<li><span class="check">Yes</span>
		</li>
		<li><span class="check">Yes</span></li>
		<li><span class="no">No</span></li>
	</ul>
	</section>
	<footer>
		<a class="button" href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?action=register&license=1">
		<span>GO PRO!</span></a>
	</footer>
</article>

<article class="last">
	<header>
		<hgroup class="plan">
			<h1>Enterprise</h1>
		</hgroup>
		<hgroup class="price">
			<h2>$147<em>License</em></h2>
		</hgroup>
	</header>
	<section>
	<ul>
		<li><span>Any Page</span></li>
		<li><span class="check">Any Role</span></li>
		<li><span class="check">Enable/Disable</span></li>
		<li><span class="check">Date and Time</span></li>
		<li><span class="check">Dynamic</span></li>
		<li><span class="check">For Admins Only</span></li>
		<li><span class="check">Create Your Own</span></li>
		<li><span class="check">Trash</span></li>
		<li><span class="check">By Sidebar</span></li>
		<li><span class="check">By Title</span></li>
		<li><span class="check">20 Per Page</span></li>
		<li><span class="check">Yes</span></li>
		<li><span class="check">No</span></li>
	</ul>
	</section>
	<footer>
	<a class="button" href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?action=register&license=2">
	<span>Enterprise</span></a>
	</footer>
</article>

</div>
</div>
</div>


<?php return; ?>
<div class="twc_auth">
	<h1><?php _e('Total Widget Control Registration', 'twc'); ?></h1>
	<iframe src="http://community.5twentystudios.com/twc-terms-and-conditions/"></iframe>
	
	<div class="buy_option">
		<label>
		<input type="radio" name="license" value="1" checked="checked"/>
		<span><?php _e("I'd like to purchase a pro license, for only $9.", 'twc'); ?></span>
		</label>
	</div>
	<div class="buy_option">
		<label>
		<input type="radio" name="license" value="2"/>
		<span><?php _e("I've already purchased a pro license.", 'twc'); ?></span>
		</label>
	</div>
	<div class="buy_option">
		<label>
		<input type="radio" name="license" value="0"/>
		<span><?php _e("I'll just try the free version, thanks!", 'twc'); ?></span>
		</label>
	</div>
	
	<input type="button" name="action" class="button-secondary" value="<?php _e("I Don't Agree", 'twc'); ?>" 
	onClick="javascript:window.location.href='<?php bloginfo('url'); ?>/wp-admin/widgets.php?list_style=wp';" />
	
	<input type="hidden" name="action" value="register"/>
	<input type="submit" class="button-primary" value="<?php _e("I Agree", 'twc'); ?>/>
	<div class="clear"></div>
</div>
<script>
jQuery(document).ready(function(){
	jQuery('#twc-widget-wrap').unbind('submit');
});
</script>
