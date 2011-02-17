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
<div id="menu-management">
	<div class="menu-edit">
		<div id="nav-menu-header">
			<div id="wrapper_head" class="submitbox">
				<div class="major-publishing-actions secondary-publishing">
					<h3><span>Widget Control Settings</span></h3>
				</div>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				
				<div class="twc-third">
					<label>Sidebar </label>
					<?php echo twc_sidebar_select_box($sidebar_id, $widget, true); ?>
					<div class="clear"></div>
				</div>
				
				<div class="twc-third">
					<label>Position </label>
					<?php echo twc_position_select_box($sidebar_id, $position); ?>
					<div class="clear"></div>
				</div>
				
				<div class="clear"></div>
			</div>
		</div>
	</div><!-- /.menu-edit -->
</div>
