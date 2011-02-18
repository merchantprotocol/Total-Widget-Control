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
<div class="postbox">
	<h3 class="hndle"><span>Publish</span></h3>
	<div class="widget-publishing-actions">
	
		<div id="minor-publishing-actions">
			<div id="save-action">
				<input type="submit" name="save" id="save-post" class="button button-highlighted" id="twc-save" value="Save" />
			</div>
			<div id="preview-action">
				<a class="button twc-go-back" href="javascript:history.go(-1);">Go Back</a>
			</div>
			<div class="clear"></div>
		</div>
	
		<div id="misc-publishing-actions" class="twcp twcpi">
			<div class="misc-pub-section">
				<label for="post_status">Status:</label>
				<span id="post-status-display"><?php echo $twcp_status; ?></span>
				<a href="#" class="edit-post-status hide-if-no-js" tabindex="4">Edit</a>
				
				<div id="post-status-select" class="hide-if-js">
				<p>
					<input type="hidden" id="twcp_status_default" name="twcp_status_default" value="<?php echo $twcp_status; ?>" /> 
					<select name="twcp_status" id="twcp_status" tabindex="4">
						<option <?php echo (($twcp_status == 'enabled') ? 'selected="selected"':'');?> value="enabled">Enabled</option>
						<option <?php echo (($twcp_status == 'disabled') ? 'selected="selected"':'');?> value="disabled">Disabled</option>
					</select>
					<a href="#" class="twcp_toggler_status hide-if-no-js button">OK</a>
					<a href="#" class="twcp_toggler_status_cancel hide-if-no-js">Cancel</a>
				</p>
				</div>
			</div>
			<div class="misc-pub-section " id="visibility">
				Visibility: 
				<span id="post-visibility-display"><?php echo $twcp_visibility; ?></span>
				<a href="#" class="edit-visibility hide-if-no-js">Edit</a>
				
				<div id="post-visibility-select" class="hide-if-js">
				<p>
					<span class="checkspace">
						<input id="twcp_visible_parent" name="twcp_visible_parent" <?php echo ($twcp_visible_parent == 'parent') ?'checked="checked"':''; ?> type="checkbox" value="parent" tabindex="4"> 
						<label for="twcp_visible_parent" class="selectit">Visible to parent roles also.</label><br>
					</span>
					<input type="hidden" id="twcp_visibility_default" name="twcp_visibility_default" value="<?php echo $twcp_visibility; ?>" /> 
					<select name="twcp_visibility" id="twcp_visibility" tabindex="4">
						<?php foreach ((array)$options as $value => $text): ?>
							<option <?php echo (($twcp_visibility == $value) ? 'selected="selected"':'');?> value="<?php echo $value; ?>"><?php echo $text; ?></option>
						<?php endforeach; ?>
						<option <?php echo (($twcp_visibility == 'public') ? 'selected="selected"':'');?> value="public">Public</option>
					</select>
					<a href="#" class="twcp_toggler_visibility hide-if-no-js button">OK</a>
					<a href="#" class="twcp_toggler_visibility_cancel hide-if-no-js">Cancel</a>
				</p>
				</div>
			</div>

			<div class="misc-pub-section curtime misc-pub-section-last"> 
				<span id="timestamp"><?php echo $twcp_publish_time_format; ?></span>
				<a href="#" class="edit-timestamp hide-if-no-js" tabindex="4">Edit</a>
				<div id="timestampdiv" class="hide-if-js">
					<input type="hidden" id="twcp_mm_default" name="twcp_mm_default" value="<?php echo $twcp_mm; ?>" /> 
					<input type="hidden" id="twcp_jj_default" name="twcp_jj_default" value="<?php echo $twcp_jj; ?>" /> 
					<input type="hidden" id="twcp_YY_default" name="twcp_YY_default" value="<?php echo $twcp_YY; ?>" /> 
					<input type="hidden" id="twcp_hh_default" name="twcp_hh_default" value="<?php echo $twcp_hh; ?>" /> 
					<input type="hidden" id="twcp_ii_default" name="twcp_ii_default" value="<?php echo $twcp_ii; ?>" /> 
					<div class="timestamp-wrap">
					<p>
						<select id="mm" name="twcp_mm" tabindex="4">
								<option value="01" <?php echo (($mm == '01') ? 'selected="selected"':'');?>>Jan</option>
								<option value="02" <?php echo (($mm == '02') ? 'selected="selected"':'');?>>Feb</option>
								<option value="03" <?php echo (($mm == '03') ? 'selected="selected"':'');?>>Mar</option>
								<option value="04" <?php echo (($mm == '04') ? 'selected="selected"':'');?>>Apr</option>
								<option value="05" <?php echo (($mm == '05') ? 'selected="selected"':'');?>>May</option>
								<option value="06" <?php echo (($mm == '06') ? 'selected="selected"':'');?>>Jun</option>
								<option value="07" <?php echo (($mm == '07') ? 'selected="selected"':'');?>>Jul</option>
								<option value="08" <?php echo (($mm == '08') ? 'selected="selected"':'');?>>Aug</option>
								<option value="09" <?php echo (($mm == '09') ? 'selected="selected"':'');?>>Sep</option>
								<option value="10" <?php echo (($mm == '10') ? 'selected="selected"':'');?>>Oct</option>
								<option value="11" <?php echo (($mm == '11') ? 'selected="selected"':'');?>>Nov</option>
								<option value="12" <?php echo (($mm == '12') ? 'selected="selected"':'');?>>Dec</option>
						</select>
						<input type="text" id="jj" name="twcp_jj" value="<?php echo $twcp_jj; ?>" size="2" maxlength="2" tabindex="4" autocomplete="off">
						, <input type="text" id="aa" name="twcp_YY" value="<?php echo $twcp_YY; ?>" size="4" maxlength="4" tabindex="4" autocomplete="off"> 
						@ <input type="text" id="hh" name="twcp_hh" value="<?php echo $twcp_hh; ?>" size="2" maxlength="2" tabindex="4" autocomplete="off"> 
						: <input type="text" id="mn" name="twcp_ii" value="<?php echo $twcp_ii; ?>" size="2" maxlength="2" tabindex="4" autocomplete="off">
					</p>
					</div>
				
					<p>
					<a href="#" class="twcp_toggler_time hide-if-no-js button">OK</a>
					<a href="#" class="twcp_toggler_time_cancel hide-if-no-js">Cancel</a>
					</p>
				</div>
			</div>
		</div>


		<div id="major-publishing-actions">
			<div id="delete-action">
				<a class="submitdelete deletion" href="<?php bloginfo('url'); ?>/wp-admin/widgets.php?action=delete&widget_id=<?php echo $widget['id']; ?>">
				Move to Trash</a>
			</div>
			
			<div id="publishing-action">
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="ajax-feedback" class="ajax-feedback"/>
				<input type="submit" class="button-primary" onClick="javascript:jQuery('#twc-redirect').val('<?php bloginfo('url');?>/wp-admin/widgets.php'); return twc_save_widget_edit();" id="twc-save-continue" value="Save and Continue" />
			</div>
			<div class="clear"></div>
		</div>

			<?php /*?>
		<div class="widget-save">
			<input type="submit" class="button-secondary" onClick="javascript:jQuery('#twc-redirect').val('<?php bloginfo('url');?>/wp-admin/widgets.php'); return twc_save_widget_edit();" id="twc-trash" value="Trash" />
			<input type="submit" class="button-secondary" id="twc-save" value="Save" />
			<input type="submit" class="button-primary" onClick="javascript:jQuery('#twc-redirect').val('<?php bloginfo('url');?>/wp-admin/widgets.php'); return twc_save_widget_edit();" id="twc-save-continue" value="Save and Continue" />
			<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="ajax-feedback" class="ajax-feedback"/>	
		</div>
			<?php */?>
	</div>
</div>