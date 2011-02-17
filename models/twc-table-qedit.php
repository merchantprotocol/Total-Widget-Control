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
if ( TWC_CURRENT_USER_CANNOT ) wp_die();

//loading libraries
require_once ABSPATH.'/wp-admin/includes/widgets.php';

//initializing variables
global $wp_registered_widgets, $wp_registered_widget_controls, $sidebars_widgets;
$sidebars_widgets = twc_wp_get_sidebars_widgets();

$widget_id = $_REQUEST['widget_id'];
$widget = twc_get_widget_from_slug($widget_id);

$sidebar_id = isset($sidebar_args['id']) ? $sidebar_args['id'] : false;
$key = $sidebar_id ? array_search( $widget_id, $sidebars_widgets[$sidebar_id] ) : '-1'; // position of widget in sidebar
$control = isset($wp_registered_widget_controls[$widget_id]) ? $wp_registered_widget_controls[$widget_id] : array();


$id_format = $widget['id'];
$widget_number = isset($control['params'][0]['number']) ? $control['params'][0]['number'] : '';
$id_base = isset($control['id_base']) ? $control['id_base'] : $widget_id;
$multi_number = isset($sidebar_args['_multi_num']) ? $sidebar_args['_multi_num'] : '';
$add_new = isset($sidebar_args['_add']) ? $sidebar_args['_add'] : '';

$query_arg = array( 'editwidget' => $widget['id'] );
if ( $add_new ) {
	$query_arg['addnew'] = 1;
	if ( $multi_number ) {
		$query_arg['num'] = $multi_number;
		$query_arg['base'] = $id_base;
	}
} else {
	$query_arg['sidebar'] = $sidebar_id;
	$query_arg['key'] = $key;
}

// We aren't showing a widget control, we're outputing a template for a mult-widget control
if ( isset($sidebar_args['_display']) && 'template' == $sidebar_args['_display'] && $widget_number ) {
	// number == -1 implies a template where id numbers are replaced by a generic '__i__'
	$control['params'][0]['number'] = -1;
	// with id_base widget id's are constructed like {$id_base}-{$id_number}
	if ( isset($control['id_base']) )
		$id_format = $control['id_base'] . '-__i__';
}

$wp_registered_widgets[$widget_id]['callback'] = $wp_registered_widgets[$widget_id]['_callback'];
unset($wp_registered_widgets[$widget_id]['_callback']);

$widget_title = esc_html( strip_tags( $sidebar_args['widget_name'] ) );
$has_form = 'noform';


$list_type = 'active';
if (!twc_inactive_list()) $list_type = 'inactive';

require $view;