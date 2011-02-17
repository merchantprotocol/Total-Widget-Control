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
	
		<div id="misc-publishing-actions" class="twcp">
			<div class="misc-pub-section">
				<label for="post_status">Status:</label>
				<span id="post-status-display">Enabled</span>
				<a href="#" class="edit-post-status hide-if-no-js" tabindex="4">Edit</a>
				
				<div id="post-status-select" class="hide-if-js">
					<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="draft">
					<select name="post_status" id="post_status" tabindex="4">
						<option value="pending">Pending Review</option>
						<option selected="selected" value="draft">Draft</option>
					</select>
					<a href="#post_status" class="save-post-status hide-if-no-js button">OK</a>
					<a href="#post_status" class="cancel-post-status hide-if-no-js">Cancel</a>
				</div>
			</div>
			<div class="misc-pub-section " id="visibility">
				Visibility: 
				<span id="post-visibility-display">Public</span>
				<a href="#" class="edit-visibility hide-if-no-js">Edit</a>
				
				<div id="post-visibility-select" class="hide-if-js">
					<input type="checkbox" style="display:none" name="hidden_post_sticky" id="hidden-post-sticky" value="sticky">
					
					
					<input type="radio" name="visibility" id="visibility-radio-public" value="public" checked="checked"> <label for="visibility-radio-public" class="selectit">Public</label><br>
					<span id="sticky-span"><input id="sticky" name="sticky" type="checkbox" value="sticky" tabindex="4"> <label for="sticky" class="selectit">Stick this post to the front page</label><br></span>
					<input type="radio" name="visibility" id="visibility-radio-password" value="password"> <label for="visibility-radio-password" class="selectit">Password protected</label><br>
					<span id="password-span"><label for="post_password">Password:</label> <input type="text" name="post_password" id="post_password" value=""><br></span>
					<input type="radio" name="visibility" id="visibility-radio-private" value="private"> <label for="visibility-radio-private" class="selectit">Private</label><br>
					
					<p>
					 <a href="#visibility" class="save-post-visibility hide-if-no-js button">OK</a>
					 <a href="#visibility" class="cancel-post-visibility hide-if-no-js">Cancel</a>
					</p>
				</div>
			</div>

			<div class="misc-pub-section curtime misc-pub-section-last">
				<span id="timestamp">Publish <b>immediately</b></span>
				<a href="#" class="edit-timestamp hide-if-no-js" tabindex="4">Edit</a>
				<div id="timestampdiv" class="hide-if-js">
					<div class="timestamp-wrap">
						<select id="mm" name="mm" tabindex="4">
								<option value="01">Jan</option>
								<option value="02" selected="selected">Feb</option>
								<option value="03">Mar</option>
								<option value="04">Apr</option>
								<option value="05">May</option>
								<option value="06">Jun</option>
								<option value="07">Jul</option>
								<option value="08">Aug</option>
								<option value="09">Sep</option>
								<option value="10">Oct</option>
								<option value="11">Nov</option>
								<option value="12">Dec</option>
						</select>
						<input type="text" id="jj" name="jj" value="14" size="2" maxlength="2" tabindex="4" autocomplete="off">
						, <input type="text" id="aa" name="aa" value="2011" size="4" maxlength="4" tabindex="4" autocomplete="off"> 
						@ <input type="text" id="hh" name="hh" value="02" size="2" maxlength="2" tabindex="4" autocomplete="off"> 
						: <input type="text" id="mn" name="mn" value="45" size="2" maxlength="2" tabindex="4" autocomplete="off">
					</div>
				
					<p>
					<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button">OK</a>
					<a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js">Cancel</a>
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