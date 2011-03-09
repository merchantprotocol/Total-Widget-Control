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
global $widget;
$wrappers = twc_read_wrapper_files();

?>
<div class="postbox">
	<div class="handlediv" title="Click to toggle"><br></div>
	<h3 class="hndle">
		<span><?php _e('TWC Pro Settings', 'twc'); ?></span>
	</h3>
	<?php if (!function_exists('twc_widget_protitle')): ?>
	<div class="inside">
		<p>Upgrade to pro and you'll receive a lot more functionality!</p>
		<ol style="margin-left:30px;">
			<li>Dyamically set default sidebars</li>
			<li>Ability to recursivly display a widget to all child pages</li>
			<li>choose to exclude the widget from checked pages</li>
			<li>Display widgets by user role</li>
			<li>Enable and Disable widgets without loosing their sidebar locations</li>
			<li>Set Specific publishing dates for widgets</li>
			<li>Set administrative titles for every widget instance</li>
			<li>Create your own widget wrappers to style widget instances individually</li>
			<li>Get bulk action options in the widget list</li>
			<li>Filter, Pagination and Search the widget list</li>
		</ol>
		<?php twc_upgrade_button(); ?>
	</div>
	<?php endif; ?>
	<div class="inside twcp">
		<div class="twc-third">
			<ul id="resourceschecklist-most-recent" class="categorychecklist form-no-clear">
			<li>
				<label class="menu-item-title">
				<input type="hidden" name="twcp_default_sidebar" value="" />
				<input type="checkbox" class="menu-item-checkbox" name="twcp_default_sidebar" value="default"
					<?php echo ($widget['p']['twcp_default_sidebar'] == 'default')? 'checked="checked"' :''; ?>> 
				<?php _e('Make it a Default Widget', 'twc'); ?>
				</label>
			</li>
			<li>
				<label class="menu-item-title">
				<input type="hidden" name="twcp_inherit_sidebar" value="" />
				<input type="checkbox" class="menu-item-checkbox" name="twcp_inherit_sidebar" value="inherit"
					<?php echo ($widget['p']['twcp_inherit_sidebar'] == 'inherit')? 'checked="checked"' :''; ?>> 
				<?php _e('Recurse to Child Objects', 'twc'); ?>
				</label>
			</li>
			<li>
				<label class="menu-item-title">
				<input type="hidden" name="twcp_exclude_sidebar" value="" />
				<input type="checkbox" class="menu-item-checkbox" name="twcp_exclude_sidebar" value="exclude"
				<?php echo ($widget['p']['twcp_exclude_sidebar'] == 'exclude')? 'checked="checked"' :''; ?>> 
				<?php _e('Exclude Checked Pages', 'twc'); ?>
				</label>
			</li>
			</ul>
		</div>
		
		<div class="twc-third">
			<label><?php _e('Wrapper', 'twc'); ?> </label>
			<select name="twcp_wrapper_file">
			<option value=""> -- <?php _e('No Wrapper'); ?> -- </option>
			<?php 
			if (is_array($wrappers)) foreach ($wrappers as $file => $wrapper)
			{
				$selected = '';
				if (isset($widget['p']['twcp_wrapper_file'])
				&& $widget['p']['twcp_wrapper_file'] == $file)
					$selected = ' selected="selected" ';
								
				echo '<option '.$selected.' value="'.$file.'">'.(($wrapper['wrapperTitle'])?$wrapper['wrapperTitle']:$file).'</option>';
			}
			?>
			</select>
		</div>
		<div class="clear"></div>
	</div>
</div>