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
<div class="twc_auth">
	<h1><?php _e('Registering.','twc');?>
	<img id="register_spinner" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"/>
	<span id="register_redirect" style="display:none;color:green;text-shadow:none;font-size:70%;font-weight:bold;">
	<?php _e('Redirecting...','twc'); ?></span>
	<span class="register_error" style="display:none;color:red;text-shadow:none;font-size:70%;font-weight:bold;">
	<?php _e('Error.','twc'); ?></span>
	</h1>
	<p><?php _e('This process will attempt to download a license for you, then activate the software before redirecting you. Please be patient.','twc'); ?></p>
	<p class="register_error" style="display:none;">
	<?php _e('Looks like we\'re having problems communicating with the license server. You can click this link to download a license manually, then upload the license to the TWC plugin directory.', 'twc'); ?>
	<a href="<?php twc_get_license_link(); ?>"><?php _e('Click to download your pro license.','twc'); ?></a>
	</p>
</div>

<script type="text/javascript">

var twcp_count = 0;
function twc_register_check_license()
{
	jQuery('.register_error').ajaxError(function(event, request, settings){
		jQuery('#register_spinner').css('display','none');
		jQuery('.register_error').css('display','inline-block');
	 });
    var register = jQuery.ajax({
		url: '?view=twc-register-check-license&ver=<?php echo substr(create_guid(),0,4); ?>',
		success: function( result ){
    		result = result+0;
			if (result>0)
			{
				jQuery('#register_spinner').css('display','none');
				jQuery('#register_redirect').css('display','inline-block');
				window.location.href = '<?php echo admin_url('widgets.php?list_style=twc&ver='.substr(create_guid(),0,4)); ?>';
			}
			else
			{
				twcp_count = twcp_count+1;
				if (twcp_count > 30)
				{
					jQuery('#register_spinner').css('display','none');
					jQuery('.register_error').css('display','inline-block');
					return;
				}
				twc_register_check_license();
			}
		}
	});
}
jQuery(document).ready(function(){
	twc_register_check_license();
});

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-351858-35']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_setCustomVar', 1, 'WP Version', '<?php echo $wp_version; ?>', 2]);

  <?php if ($first): ?>
  _gaq.push(['_addTrans',
             '<?php echo $uniqueID; ?>',           // order ID - required
             '<?php echo f20_get_domain(); ?>',  // affiliation or store name
             '<?php echo $price; ?>',          // total - required
           ]);
  _gaq.push(['_addItem',
             '<?php echo $uniqueID; ?>',           // order ID - required
             '<?php echo $type; ?>',           // SKU/code - required
             '<?php echo $type; ?>',        // product name
             'Plugin Activation',   // category or variation
             '<?php echo $price; ?>',          // unit price - required
             '1'               // quantity - required
           ]);
           _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
  <?php endif; ?>
  
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php require_once( './admin-footer.php' ); exit(); ?>