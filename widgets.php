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

/**
 * Gotta have the actions
 * 
 */
add_filter('twc_query_posts_view', 'twc_widget_query_view', 20, 3);
add_filter('widget_update_callback', 'twc_widget_query_update', 20, 2);

/**
 * Function is responsible for correcting the arrays that are to be saved.
 *
 * @param array $instance
 * @param array $new_instance
 * @param array $old_instance
 */
function twc_widget_query_update( $instance, $new_instance )
{
	global $widget;
	if ($widget['id_base'] != 'twc-widget-query-posts') return $instance;
	
	$request = $_REQUEST['widget-twc-widget-query-posts'][$widget['number']];
	foreach ((array)$request['tax_query_taxonomy'] as $key => $v)
	{
		unset($instance['tax_query_taxonomy']);
		$instance['tax_query']['taxonomy'] = $request['tax_query_taxonomy'][$key];
		
		unset($instance['tax_query_field']);
		$instance['tax_query']['field'] = $request['tax_query_field'][$key];
		
		unset($instance['tax_query_terms']);
		$instance['tax_query']['terms'] = $request['tax_query_terms'][$key];
		
		unset($instance['tax_query_operator']);
		$instance['tax_query']['operator'] = $request['tax_query_operator'][$key];
	}
	
	foreach ((array)$request['meta_query_key'] as $key => $v)
	{
		unset($instance['meta_query_key']);
		$instance['meta_query']['key'] = $request['meta_query_key'][$key];
		
		unset($instance['meta_query_value']);
		$instance['meta_query']['value'] = $request['meta_query_value'][$key];
		
		unset($instance['meta_query_compare']);
		$instance['meta_query']['compare'] = $request['meta_query_compare'][$key];
		
		unset($instance['meta_query_type']);
		$instance['meta_query']['type'] = $request['meta_query_type'][$key];
	}
	
	return $instance;
}

/**
 * Function is responsible for adjusting the view of this given widget
 * instance.
 *
 * @param unknown_type $widget
 * @param unknown_type $params
 */
function twc_widget_query_view( $template, $widget, $params )
{
	//initializing variables
	$theme = get_theme_path().DS."views";
	$templates = array();
	$templates[] = $template.'-'.$widget['number'];
	$templates[] = 'widget-query-'.$params['post_type'];
	$templates[] = $template;
	
	foreach ($templates as $name)
	{	
		if ($view = twc_find(array($theme), $name.".php")) break;
	}
	
	if (!$view) return $template;
	return $view;
}

/**
 * Query Posts Widget
 * 
 * register_multiwidget is a custom function from the total widget control. The
 * function allows you to skip all of the php programming and define a new widget
 * using a basic array.
 * 
 * This first widget is the query posts widget. It allows you to create complex
 * views for different areas of your website. Whether you're working with a list
 * of ecommerce products, or a grid of images, this function allows you to handle
 * it.
 */
register_multiwidget(array(
	'id' => 'twc-widget-query-posts',	// Must be slug compatible, and unique, it's used a lot
	'title' => __('Query Posts'),	
	'description' => __('Query the database for content and then display a custom view for it. Template over-rides are supported.'),	
	'classname' => 'widget-query-posts',	
	'show_view' => 'widget-query-posts',	//This is the unique filename within the views folder, the theme is checked first, then defaults to the plugin
	'fields' => array(
	
	array(
		'type' => 'custom',
		'std' => __('<p>Need help? Checkout the <a target="_blank" href="http://codex.wordpress.org/Function_Reference/query_posts">query posts codex</a></p><br/>')
	),
	array(
		'name' => __('Title'),
		'id' => 'title',
		'type' => 'text',
		'std' => ''
	),

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
		'id' => 'tag__not_in',
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
		'name' => __('Taxonomy ID'),
		'desc' => __('{tax} (string) - use taxonomy slug. Deprecated as of Version 3.1 in favor of \'tax_query\'.'),
		'id' => 'tax',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Tax Query'),
		'desc' => __(''),
		'id' => 'tax_query',
		'type' => 'custom',
		'std' => '<div class="twc_tax_query">'
	),
	array(
		'name' => __('Taxonomy'),
		'desc' => __('taxonomy (string) - Taxonomy.'),
		'id' => 'tax_query_taxonomy][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Field'),
		'desc' => __('field (string) - Select taxonomy term by (\'id\' or \'slug\')'),
		'id' => 'tax_query_field][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Terms'),
		'desc' => __('terms (int/string/array) - Taxonomy term(s).'),
		'id' => 'tax_query_terms][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Operator'),
		'desc' => __("operator (string) - Operator to test. Possible values are 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'."),
		'id' => 'tax_query_operator][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'type' => 'custom',
		'std' => '</div>'
	),
	
	////////////////////////////////////////////////////////
	//
	//    Post & Page Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Post & Page Parameters</h2><hr/>')
	),
	array(
		'name' => __('Post ID'),
		'desc' => __('p (int) - use post id.'),
		'id' => 'p',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Post Slug'),
		'desc' => __('name (string) - use post slug.'),
		'id' => 'name',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Page ID'),
		'desc' => __('page_id (int) - use page id.'),
		'id' => 'page_id',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Page Slug'),
		'desc' => __('pagename (string) - use page slug.'),
		'id' => 'pagename',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Post Parent ID'),
		'desc' => __('post_parent (int) - use page id. Return just the child Pages.'),
		'id' => 'post_parent',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Include Post IDs'),
		'desc' => __('post__in (array) - use post ids. Specify posts to retrieve.'),
		'id' => 'post__in',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Exclude Post IDs'),
		'desc' => __('post__not_in (array) - use post ids. Specify post NOT to retrieve.'),
		'id' => 'post__not_in',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Type & Status Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Type & Status Parameters</h2><hr/>')
	),
	array(
		'name' => __('Post Type'),
		'desc' => __('post_type (string / array) - use post types. Retrieves posts by Post Types, default value is \'post\';'),
		'id' => 'post_type',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Post Status'),
		'desc' => __('post_status (string / array) - use post status. Retrieves posts by Post Status, default value is \'publish\';'),
		'id' => 'post_status',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Pagination Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Pagination Parameters</h2><hr/>')
	),
	array(
		'name' => __('Posts Per Page'),
		'desc' => __("posts_per_page (int) - number of post to show per page (available with Version 2.1). Use 'posts_per_page'=>-1 to show all posts. Note if the query is in a feed, wordpress overwrites this parameter with the stored 'posts_per_rss' option. To reimpose the limit, try using the 'post_limits' filter."),
		'id' => 'posts_per_page',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('No Paging'),
		'desc' => __("nopaging (bool) - show all posts or use pagination. Default value is 'false', use paging."),
		'id' => 'nopaging',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Paged'),
		'desc' => __("paged (int) - number of page. Show the posts that would normally show up just on page X when using the \"Older Entries\" link."),
		'id' => 'paged',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Offset Parameter
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Offset Parameter</h2><hr/>')
	),
	array(
		'name' => __('Offset'),
		'desc' => __("offset (int) - number of post to displace or pass over."),
		'id' => 'offset',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Order & Orderby Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Order & Orderby Parameters</h2><hr/>')
	),
	array(
		'name' => __('Order'),
		'desc' => __("order (string) - Designates the ascending or descending order of the 'orderby' parameter."),
		'id' => 'order',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Order By'),
		'desc' => __("orderby (string) - Sort retrieved posts by:"),
		'id' => 'orderby',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Sticky Post Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Sticky Post Parameters</h2><hr/>')
	),
	array(
		'name' => __('Caller Get Posts'),
		'desc' => __("caller_get_posts (bool) - ignore sticky posts or not. Deprecated as of Version 3.1 in favor of 'ignore_sticky_posts'."),
		'id' => 'caller_get_posts',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Ignore Sticky Posts'),
		'desc' => __("ignore_sticky_posts (bool) - ignore sticky posts or not. Default value is 0, don't ignore. Ignore/exclude sticky posts being included at the beginning of posts returned, but the sticky post will still be returned in the natural order of that list of posts returned."),
		'id' => 'ignore_sticky_posts',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Time Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Time Parameters</h2><hr/>')
	),
	array(
		'name' => __('Year'),
		'desc' => __("year (int) - 4 digit year (e.g. 2011)."),
		'id' => 'year',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Month Number'),
		'desc' => __("monthnum (int) - Month number (from 1 to 12)."),
		'id' => 'monthnum',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Week Number'),
		'desc' => __("w (int) - Week of the year (from 0 to 53). Uses the MySQL WEEK command Mode=1."),
		'id' => 'w',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Day of the Month'),
		'desc' => __("day (int) - Day of the month (from 1 to 31)."),
		'id' => 'day',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Hour'),
		'desc' => __("hour (int) - Hour (from 0 to 23)."),
		'id' => 'hour',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Minute'),
		'desc' => __("minute (int) - Minute (from 0 to 60)."),
		'id' => 'minute',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Second'),
		'desc' => __("second (int) - Second (0 to 60)."),
		'id' => 'second',
		'type' => 'text',
		'std' => ''
	),
	
	////////////////////////////////////////////////////////
	//
	//    Custom Field Parameters
	//
	array(
		'type' => 'custom',
		'std' => __('<h2>Custom Field Parameters</h2><hr/>')
	),
	array(
		'name' => __('Meta Key'),
		'desc' => __("meta_key (string) - Custom field key. Deprecated as of Version 3.1 in favor of 'meta_query'."),
		'id' => 'meta_key',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Meta Value'),
		'desc' => __("meta_value (string) - Custom field value. Deprecated as of Version 3.1 in favor of 'meta_query'."),
		'id' => 'meta_value',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Meta Compare'),
		'desc' => __("meta_compare (string) - Operator to test the 'meta_value'. Possible values are '!=', '>', '>=', '<', or '<='. Default value is '='. Deprecated as of Version 3.1 in favor of 'meta_query'."),
		'id' => 'meta_compare',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Meta Query'),
		'type' => 'custom',
		'std' => '<div class="twc_meta_query">'
	),
	array(
		'name' => __('Key'),
		'desc' => __("key (string) - Custom field key."),
		'id' => 'meta_query_key][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Value'),
		'desc' => __("value (string) - Custom field value."),
		'id' => 'meta_query_value][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Compare'),
		'desc' => __("compare (string) - Operator to test. Possible values are 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'."),
		'id' => 'meta_query_compare][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'name' => __('Type'),
		'desc' => __("type (string) - Custom field type. Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'. Default value is 'CHAR'."),
		'id' => 'meta_query_type][',
		'type' => 'text',
		'std' => ''
	),
	array(
		'type' => 'custom',
		'std' => '</div>'
	),
	
	////////////////////////////////////////////////////////
	//
	//    Query View Parameters
	//
	array(
		'type' => 'metabox',
	),
	array(
		'name' => __('Display as bullet list'),
		'desc' => __(''),
		'id' => 'bullet_list',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Display post Title'),
		'desc' => __(''),
		'id' => 'title_display',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Title is a link'),
		'desc' => __(''),
		'id' => 'title_link',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Title Tag'),
		'desc' => __(''),
		'id' => 'title_tag',
		'type' => 'select',
		'options' => array('H1', 'H2', 'H3','H4','H5','H6','STRONG')
	),
	array(
		'name' => __('Display the Date'),
		'desc' => __(''),
		'id' => 'post_date',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Display the Author'),
		'desc' => __(''),
		'id' => 'post_author',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Display post Image'),
		'desc' => __('Displays only if an image is attached.'),
		'id' => 'display_image',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Image Size'),
		'desc' => __(''),
		'id' => 'image_size',
		'type' => 'select',
		'options' => array('large','medium','small','thumbnail')
	),
	array(
		'name' => __('Link Image to'),
		'desc' => __(''),
		'id' => 'link_image',
		'type' => 'select',
		'options' => array('none','image','post')
	),
	array(
		'name' => __('Display the content'),
		'desc' => __(''),
		'id' => 'display_content',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Display all content'),
		'desc' => __(''),
		'id' => 'display_allcontent',
		'type' => 'checkbox',
	),
	array(
		'name' => __('Display readme link'),
		'desc' => __(''),
		'id' => 'display_readme',
		'type' => 'checkbox',
	),
	array(
		'name' => __('Display author description'),
		'desc' => __(''),
		'id' => 'display_authordesc',
		'type' => 'checkbox',
	),
	array(
		'name' => __('Display posted in'),
		'desc' => __(''),
		'id' => 'display_category',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Display the edit link'),
		'desc' => __(''),
		'id' => 'display_edit',
		'type' => 'checkbox',
		'std' => true
	),
	array(
		'name' => __('Display comments'),
		'desc' => __('Displays if comments are enabled for the post.'),
		'id' => 'display_comments',
		'type' => 'checkbox',
		'std' => true
	),
	
	)
));

