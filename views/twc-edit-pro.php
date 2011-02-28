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
			<?php twc_upgrade_button(); ?>	
	</div>
</div>



<?php return; ?>
<div id="menu-management" class="widget-control-settings postbox">
	<div class="menu-edit">
		<div id="nav-menu-header">
			<div id="wrapper_head" class="submitbox">
				<div class="major-publishing-actions secondary-publishing">
					<h3><span><?php _e('TWC Pro Settings', 'twc'); ?></span></h3>
				</div>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content" class="twcp">
				
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
			<?php twc_upgrade_button(); ?>	
		</div>
	</div><!-- /.menu-edit -->
</div>