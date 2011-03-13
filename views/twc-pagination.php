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
global $twcp_pagi;
extract($twcp_pagi);
$inactive = (twc_inactive_list()) ?'&inactive=inactive':'&inactive=active';
$next = $page + 1;
$prev = $page - 1;
$under = $over = false;

?>
<div class="tablenav-pages twcp">
	<span class="displaying-num"><?php _e('Displaying','twc');?> <?php echo $start; ?>-<?php echo $stop; ?> <?php _e('of','twc');?> <?php echo $total; ?></span>
	
	<?php if ($page > 1 && $pages > 5): ?>
	<a class="next page-numbers" href="<?php echo admin_url('widgets.php?pa='.$prev.$inactive); ?>">&laquo;</a>
	<?php endif; ?>
	
	<?php for ($i=1; $i <= $pages; $i++): ?>
		<?php if ($i == $page): ?>
			<span class="page-numbers current"><?php echo $i; ?></span>
			
		<?php elseif ($i == 1 || $i == $pages): ?>
			<a class="page-numbers" href="<?php echo admin_url('widgets.php?pa='.$i.$inactive); ?>"><?php echo $i; ?></a>
			
		<?php elseif ($i > 1 && $i < ($page-2)): ?>
			<?php if (!$under): $under = true; ?>
			<span class="page-numbers dots">...</span>
			<?php endif;?>
			
		<?php elseif ($i < $total && $i > ($page+2)): ?>
			<?php if (!$over): $over = true; ?>
			<span class="page-numbers dots">...</span>
			<?php endif; ?>
			
		<?php else: ?>
			<a class="page-numbers" href="<?php echo admin_url('widgets.php?pa='.$i.$inactive); ?>"><?php echo $i; ?></a>
		<?php endif; ?>
		
	<?php endfor; ?>
	
	<?php if ($page < $pages): ?>
	<a class="next page-numbers" href="<?php echo admin_url('widgets.php?pa='.$next.$inactive); ?>">&raquo;</a>
	<?php endif; ?>
</div>
