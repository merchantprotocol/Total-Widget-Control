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


?>
<div class="twc_auth">
	<h1>Registering.
	<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"/>
	</h1>
	<p>Your registration process is in progress, we're working to activate your software.
	Please be patient as we register your software and download your license. We will 
	redirect you as soon as the software is active, you can also navigate away from this page
	whenever you would like.</p>
</div>
<script>
function twc_register_check_license()
{
    jQuery.ajax({
		url: '?view=twc-register-check-license&ver=<?php echo substr(create_guid(),0,4); ?>',
		success: function( result ){
    		//result = parseInt(result);
			result = result+0;
			if (result>0)
			{
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