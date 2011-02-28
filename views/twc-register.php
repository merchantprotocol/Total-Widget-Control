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
	<a href="http://community.5twentystudios.com/?view=download-license&uniqueID=<?php echo $uniqueID; ?>||<?php echo urlencode($domain); ?>&ver=twc-pro||<?php echo urlencode($headers['Version']); ?>">Click to download your pro license.</a>
	</p>
</div>
<script>
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
				window.location.href = '<?php bloginfo('url'); ?>/wp-admin/widgets.php?list_style=twc&ver=<?php echo substr(create_guid(),0,4); ?>';
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
</script>

<?php require_once( './admin-footer.php' ); exit(); ?>