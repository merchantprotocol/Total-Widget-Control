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
	<h1>Registering.
	<img id="register_spinner" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"/>
	<span id="register_redirect" style="display:none;color:green;text-shadow:none;font-size:70%;font-weight:bold;">
	Redirecting...</span>
	<span class="register_error" style="display:none;color:red;text-shadow:none;font-size:70%;font-weight:bold;">
	Error.</span>
	</h1>
	<p>Your registration process is in progress, we're working to activate your software.
	Please be patient as we register your software and download your license. We will 
	redirect you as soon as the software is active, you can also navigate away from this page
	whenever you would like.</p>
	<p class="register_error" style="display:none;">Uh Oh! The license that was just downloaded contains a 
	programming error. Please take your error log file over to the support forum to figure 
	out if it's a compatibility error with your system. Otherwise, delete the license
	from your plugin folder and try again.</p>
</div>
<script>
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
				window.location.href = '<?php bloginfo('url'); ?>/wp-admin/widgets.php?list_style=twc&ver=<?php echo substr(create_guid(),0,4); ?>';
			}
			else
			{
				twc_register_check_license();
			}
		}
	});
}
jQuery(document).ready(function(){
	twc_register_check_license();
});
</script>