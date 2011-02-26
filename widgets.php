<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.5twentystudios.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.5.5
 * 
 * @example 
<pre>
register_multiwidget(array(
	'id' => 'first-custom-widget',	// Must be slug compatible, and unique, it's used a lot
	'title' => __('aaaFirst Widget'),	
	'description' => __('This is my description'),	
	'classname' => 'st-custom-wi',	
	'show_view' => 'first_widget',	//This is the unique filename within the views folder, the theme is checked first, then defaults to the plugin
	'fields' => array(
	array(
		'name' => 'Text box',
		'desc' => 'Enter something here',
		'id' => 'text',
		'type' => 'text',
		'std' => 'Default value 1'
	),
	array(
		'type' => 'custom',
		'std' => '<hr/>'
	),
	array(
		'name' => 'Textarea',
		'desc' => 'Enter big text here',
		'id' => 'textarea',
		'type' => 'textarea',
		'std' => 'Default value 2'
	),
	array(
		'name' => 'Select box',
		'id' => 'select',
		'type' => 'select',
		'options' => array('Option 1', 'Option 2', 'Option 3')
	),
	array(
		'name' => 'Radio',
		'id' => 'radio',
		'type' => 'radio',
		'options' => array(
		array('name' => 'Name 1', 'value' => 'Value 1'),
		array('name' => 'Name 2', 'value' => 'Value 2')
		)
	),
	array(
		'name' => 'Checkbox',
		'id' => 'checkbox',
		'type' => 'checkbox'
	),
	
	)
));
</pre>
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");

register_multiwidget(array(
	'id' => 'twc-widget-query-posts',	// Must be slug compatible, and unique, it's used a lot
	'title' => __('Query Posts'),	
	'description' => __('Thanks to Total Widget Control, this creates a loop of wordpress content, you can also define the view.'),	
	'classname' => 'widget-query-posts',	
	'show_view' => 'widget-query-posts',	//This is the unique filename within the views folder, the theme is checked first, then defaults to the plugin
	'fields' => array(

	////////////////////////////////////////////////////////
	//
	//    Author Parameters 
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Author Parameters</h2><hr/>')
	),
	array(
		'name' => __('Author ID'),
		'desc' => __('author: (int) - use author id.'),
		'id' => 'author',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Author Name'),
		'desc' => __('author_name: (string) - use \'user_nicename\' (NOT name).'),
		'id' => 'author_name',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Category Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Category Parameters</h2><hr/>')
	),
	array(
		'name' => __('Category ID'),
		'desc' => __('cat: (int) - use category id. Display posts that have this category (and any children of that category), using category id'),
		'id' => 'cat',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Category Slug'),
		'desc' => __('category_name: (string) - use category slug (NOT name). Display posts that have this category (and any children of that category), using category slug'),
		'id' => 'category_name',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Multiple Category Handling'),
		'desc' => __('category__and: (array) - use category id. Display posts that are in multiple categories. This shows posts that are in both categories 2 and 6'),
		'id' => 'category__and',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Category In'),
		'desc' => __('category__in: (array) - use category id. This does not show posts from any children of these categories.'),
		'id' => 'category__in',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Category Not In'),
		'desc' => __('category__not_in: (array) - use category id. You can also exclude multiple categories this way.'),
		'id' => 'category__not_in',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Author Parameters 
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Tag Parameters</h2><hr/>')
	),
	array(
		'name' => __('Tag Slug'),
		'desc' => __('tag (string) - use tag slug. Display posts that have this tag, using tag slug.'),
		'id' => 'tag',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Tag ID'),
		'desc' => __('tag_id (int) - use tag id. Display posts that have this tag, using tag id.'),
		'id' => 'tag_id',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Multiple Tag Handling'),
		'desc' => __('tag__and (array) - use tag ids. Display posts that are tagged with:'),
		'id' => 'tag__and',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Explicitly Specify'),
		'desc' => __('tag__in (array) - use tag ids. explicitly specify by using tag__in.'),
		'id' => 'tag__in',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Exclude Tags'),
		'desc' => __('tag__not_in (array) - use tag ids. Display posts that do not have any.'),
		'id' => 'v',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Tag Slug AND'),
		'desc' => __('tag_slug__and (array) - use tag slugs. The tag_slug__in and tag_slug__and behave much the same, except match against the tag\'s slug.'),
		'id' => 'tag_slug__and',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Tag Slug IN'),
		'desc' => __('tag_slug__in (array) - use tag slugs.'),
		'id' => 'tag_slug__in',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Taxonomy Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Taxonomy Parameters</h2><hr/>')
	),
	array(
		'name' => __('Author ID'),
		'desc' => __('{tax} (string) - use taxonomy slug. Deprecated as of Version 3.1 in favor of \'tax_query\'.'),
		'id' => 'author',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Author Name'),
		'desc' => __('author_name: (string) - use \'user_nicename\' (NOT name).'),
		'id' => 'author_name',
		'type' => 'text',
		'std' => ''
	),
	
	
	
	)
));




