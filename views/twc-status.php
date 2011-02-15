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
<ul class="subsubsub">
	<li><a href="/wp-admin/widgets.php" <?php echo ((!twc_inactive_list())? 'class="current"':''); ?>>Active 
	<span class="count">(<?php echo twc_count_active_widgets(); ?>)</span></a> |
	</li>
	<li id="twcInactiveWidgetsArea"><a href="/wp-admin/widgets.php?inactive=inactive" <?php echo ((twc_inactive_list())? 'class="current"':''); ?>>Trash 
	<span class="count">(<?php echo twc_count_inactive_widgets(); ?>)</span></a>
	</li>
</ul>
