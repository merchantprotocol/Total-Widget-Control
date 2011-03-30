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
if ( !is_520() ) wp_die('');

//initializing variables
$licenses = get_option('twc_licenses',array());
$license = $licenses[f20_get_domain()];
$licenseState = (!empty($license));
$headers = get_plugin_data( dirname(dirname(__file__)).DS.'index.php' );

//logic
if (isset($_REQUEST['clear'])) update_option('twc_error_log', '');

?>
<table border="0" cellpadding="0" cellspacing="0" style="width:550px;border-top:4px solid #BF2026;font:12px arial, sans-serif;margin:0 auto">
	<tbody>
	<tr>
		<td style="padding-top:5px">
			<h1 style="color:#000;font:bold 21px arial">
			<div style="width:100%;text-align:right;height:1px;overflow:visible;font-size:10px;color:#AAA;">
			<?php echo date('Y-m-d g:i a', time()); ?></div>
			<span style="color:#666;font-weight:bold"><?php echo f20_get_domain(); ?></span></h1>
		</td>
	</tr>
	<tr>
		<td style="padding-bottom:25px;font-size:13px">
		<h3>Error Log</h3>
		<div class="im">
			<ul style="list-style-type:none;padding-left:0;margin-bottom:10px">
				<li><strong>Website Admin:</strong> <a href="<?php bloginfo('siteurl'); ?>/wp-admin/" target="_blank"><?php bloginfo('siteurl'); ?>/wp-admin/</a></li>
				<li><strong>Website:</strong> <a href="<?php bloginfo('home'); ?>" target="_blank"><?php bloginfo('home'); ?></a></li>
				<li><strong>Administrator:</strong> <a href="mailto:<?php bloginfo('admin_email'); ?>"><?php echo bloginfo('admin_email'); ?></a></li>
				<li><br/></li>
				<li><strong>Plugin Folder:</strong> <?php echo trim(str_replace(DS.basename(dirname(__FILE__)), "", plugin_basename(dirname(__FILE__)))); ?></li>
				<li><strong>Version:</strong> <?php echo TWC_VERSION; ?></li>
				<li><strong>License:</strong> <?php echo TWC_LICENSE; ?></li>
				<li><strong>Active:</strong> <?php echo (($licenseState) ?'Active' :'Not Active'); ?></li>
				<li><strong>Auth File:</strong> <?php echo ((!twc_check_auth(1)) ?'Matches' :'No Match'); ?></li>
				<li><br/></li>
				<?php foreach ((array)$headers as $k => $v): ?>
				<li><strong><?php echo $k; ?>:</strong> <?php echo $v; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<p><?php echo get_option('twc_error_log', ''); ?></p>
		<p></p>
		</td>
	</tr>
	<tr>
		<td style="background-color:#000;text-align:center;padding:10px">
			<p>
			<a href="http://totalwidgetcontrol.com/" style="margin-right:20px;color:#BF2026;" target="_blank"><strong>
			Total Widget Control &raquo;</strong></a>
			<a href="http://www.5twentystudios.com" style="margin-right:20px;color:#BF2026;" target="_blank"><strong>
			5Twenty Studios &raquo;</strong></a>
			</p>
		</td>
	</tr>
	<tr>
		<td>	
			<table width="1" border="0" cellspacing="0" cellpadding="0">
				<tbody>
				<tr>
					<td><div style="min-height:15px;font-size:15px;line-height:15px">&nbsp;</div></td>
				</tr>
				</tbody>
			</table>
				<table width="5" border="0" cellspacing="0" cellpadding="0">
					<tbody>
					<tr>
						<td><div style="min-height:0px;font-size:0px;line-height:0px">&nbsp;</div></td>
					</tr>
					</tbody>
				</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="1" border="0" cellspacing="0" cellpadding="0">
				<tbody>
				<tr>
					<td><div style="min-height:15px;font-size:15px;line-height:15px">&nbsp;</div></td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:10px;border-top:1px solid #ccc;color:#666666;font-size:11px">
			<p></p>
			<p>5Twenty Studios values your privacy. At no time has 5Twenty Studios made your email address available to any other 5Twenty Studios user without your permission. &copy;2011, 5Twenty Studios Corporation.</p>
		</td>
	</tr>
	</tbody>
</table>