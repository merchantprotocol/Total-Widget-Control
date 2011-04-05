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

//initializing variables
extract($args[1]);
$view = apply_filters('twc_query_posts_view', $view, $widget, $params, $sidebar);

if ($params['display_allcontent']) global $more;
	
echo $sidebar['before_widget'];
if ($params['title'])
{
	echo $sidebar['before_title'].$params['title'].$sidebar['after_title'];
}

if ($params['bullet_list']) echo '<ul>';

//loading resources
$query_posts = new WP_Query();
$query_posts->query($params);

if ($query_posts->have_posts()): while ($query_posts->have_posts()):
	$query_posts->the_post();
	$post = $query_posts->post;
	require $view;
endwhile; endif;

if ($params['bullet_list']) echo '</ul>';

echo $sidebar['after_widget'];