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

if ( ! function_exists( 'twc_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twc_posted_on( $params ) 
{
	if ($params['post_date'])
	{
		printf( __( '<span class="%1$s">Posted on</span> %2$s ', 'twentyten' ),
			'meta-prep meta-prep-author',
			sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
				get_permalink(),
				esc_attr( get_the_time() ),
				get_the_date()
			)
		);
	}
	if ($params['post_author'])
	{
		echo '<span class="meta-sep">by</span> '.sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		);
	}
}
endif;

if ( ! function_exists( 'twc_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twc_posted_in()
{
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

if ( ! function_exists( 'echo_first_attached_image' ) ) :
/**
 * Enter description here...
 *
 */
function echo_first_attached_image( $size = 'thumbnail' )
{
	global $post;
	$images =& get_children( 'post_parent='.$post->ID.'&post_type=attachment&post_mime_type=image' );
	
	if (empty($images)) return false;
	foreach ($images as $image)
	{
		wp_get_attachment_image($image->ID,$size);
		return;
	}
}
endif;

?>
<?php 
if ($params['bullet_list']) echo '<li>';
?>
<div id="post-<?php the_ID(); ?>" class="twc_query_posts">
	<?php if ($params['title_display']): ?>
		<?php if ($params['title_link']) echo '<a href="'.get_permalink($post->ID).'">'; ?>
			<<?php echo $params['title_tag']; ?> class="entry-title"><?php the_title(); ?>
			</<?php echo $params['title_tag']; ?>>
		<?php if ($params['title_link']) echo '</a>'; ?>
	<?php endif; ?>
	
	<?php if ($params['post_date'] || $params['post_author']): ?>
	<div class="entry-meta">
		<?php twc_posted_on($params); ?>
	</div><!-- .entry-meta -->
	<?php endif; ?>
	
	<?php if ($params['display_image'] || $params['display_content']): ?>
	<div class="entry-content">
		<?php 
		if ($params['display_image'])
		{
			
			if ($params['link_image'] == 'post') echo '<a href="'.get_permalink($post->ID).'">';
			if ( has_post_thumbnail() ) {
				if ($params['link_image'] == 'image') echo '<a href="'.get_permalink(get_post_thumbnail_id()).'">';
				the_post_thumbnail($params['image_size']);
			}
			else 
			{
				echo_first_attached_image($params['image_size']);
			}
			if ($params['link_image'] !== 'none') echo '</a>';
			
		} 
		if ($params['display_allcontent'])
		{
			$more = 1;
		}
		$readmore = '';
		if ($params['display_readme']) $readmore = 'Read More...';
		if ($params['display_content']) the_content($readmore);
		?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<?php if ( $params['display_authordesc'] && get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
	<div id="entry-author-info">
		<div id="author-avatar">
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
		</div><!-- #author-avatar -->
		<div id="author-description">
			<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
			<?php the_author_meta( 'description' ); ?>
			<div id="author-link">
				<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
					<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author() ); ?>
				</a>
			</div><!-- #author-link	-->
		</div><!-- #author-description -->
	</div><!-- #entry-author-info -->
	<?php endif; ?>

	<?php if ($params['display_category'] || $params['display_edit']): ?>
	<div class="entry-utility">
		<?php if ($params['display_category']) twc_posted_in(); ?>
		<?php if ($params['display_edit']) edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
	</div><!-- .entry-utility -->
	<?php endif; ?>
</div><!-- #post-## -->

<?php if ($params['display_comments']) comments_template( '', true ); ?>

<?php 
if ($params['bullet_list']) echo '</li>';
?>