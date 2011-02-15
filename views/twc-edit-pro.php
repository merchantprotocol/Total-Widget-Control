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

//initializing variables
global $widget;

?>
<div id="menu-management" class="widget-control-settings">
	<div class="menu-edit">
		<div id="nav-menu-header">
			<div id="wrapper_head" class="submitbox">
				<div class="major-publishing-actions secondary-publishing">
					<h3><span>TWC Pro Settings</span></h3>
				</div>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content" class="twcp">
				
				<div class="twc-third">
					<ul id="resourceschecklist-most-recent" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="twcp_default_sidebar" <?php echo ($widget['p']['twcp_default_sidebar'] == 'default')? 'checked="checked"' :''; ?> value="default"> 
						Make it a Default Widget
						</label>
					</li>
					<li>
						<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="twcp_inherit_sidebar" <?php echo ($widget['p']['twcp_inherit_sidebar'] == 'inherit')? 'checked="checked"' :''; ?> value="inherit"> 
						Recurse to Child Objects
						</label>
					</li>
					<li>
						<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="twcp_exclude_sidebar" <?php echo ($widget['p']['twcp_exclude_sidebar'] == 'exclude')? 'checked="checked"' :''; ?> value="exclude"> 
						Exclude Checked Pages
						</label>
					</li>
					</ul>
				</div>
				
				<div class="twc-third">
					<label>Wrapper </label>
					<select name="twcp_wrapper_file">
					<option value=""> -- No Wrapper -- </option>
					<?php 
									
					$wrappers = twc_read_wrapper_files();
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
	</div><!-- /.menu-edit -->
</div>