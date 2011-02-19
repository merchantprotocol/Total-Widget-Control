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
<div id="titlediv">
	<div id="titlewrap" class="twcp">
		<label class="hide-if-no-js" style="<?php if ($widget['p']['twcp_widget_title']) echo 'display:none;'; ?>" id="title-prompt-text" for="title">Enter admin title here</label>
		<input type="text" name="twcp_widget_title" size="30" tabindex="1" value="<?php echo $widget['p']['twcp_widget_title']; ?>" id="title" autocomplete="off">
	</div>
</div>

<div id="menu-management" class="widget-options">
	<div class="menu-edit">
		<div id="nav-menu-header">
			<div id="submitpost" class="submitbox">
				<div class="major-publishing-actions">
					<h3><span><?php echo $widget['name']; ?></span></h3>
					<label class="menu-name-label howto open-label" for="menu-name">
						<span>ID: <?php echo $widget['id']; ?></span> 
					</label>
					<br class="clear">
				</div>
			</div>
		</div>
					
		<div id="post-body">
			<div id="post-body-content">
				<p><?php  echo $widget['callback'][0]->widget_options['description']; ?></p>
				<?php
		
				echo $sidebar_args['before_widget'];
				if ( isset($control['callback']) )
				{
					$has_form = call_user_func_array( $control['callback'], $control['params'] );
				}
				else
				{
					echo "\t\t<p>" . __('There are no options for this widget.') . "</p>\n"; 
				}
				?>
				
				<input type="hidden" name="action" value="save" />
				<input type="hidden" id="twc-redirect" name="redirect" value="" />
				<input type="hidden" name="widget-id" class="widget-id" value="<?php echo $widget['id']; ?>" />
				
				<?php echo $sidebar_args['after_widget']; ?>
			</div><!-- /#post-body-content -->
		</div><!-- /#post-body -->

	</div><!-- /.menu-edit -->
</div><!-- /#menu-management -->
		