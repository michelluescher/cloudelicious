<?php
define('THEMEROOT',get_stylesheet_directory_uri());
define('IMAGES',get_stylesheet_directory_uri().'/assets/images');
define('JS',get_stylesheet_directory_uri().'/assets/js');

/* child theme function to fetch files from the parent theme */
require trailingslashit(get_stylesheet_directory()) . '/functions/author-functions.php';

add_image_size( 'thumb45', 45, 45, true );

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles');
function my_theme_enqueue_styles() { 
    $parent_style = 'parent-style';  
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'oj_theme_enqueue_styles',9999 );
function oj_theme_enqueue_styles() {
    wp_register_script('OjScriptJs', JS.'/script.js', array('jquery'),false , true ); 
    wp_enqueue_script('OjScriptJs');
    $parent_style = 'parent-style'; 
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}


/* code block to add author's info below each post exerpt on home page */
add_filter( 'generate_post_author_output', 'tu_add_author_gravatar' );
function tu_add_author_gravatar() {
	printf( ' <span class="byline">%1$s</span>',
		sprintf( '<span class="author vcard" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="author">%5$s <a class="url fn n" href="%2$s" title="%3$s" rel="author" itemprop="url"><span class="author-name" itemprop="name">%1$s %4$s</span></a></span>',
			__( 'by','generatepress'),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'generatepress' ), get_the_author() ) ),
			esc_html( get_the_author() ),
			get_avatar( get_the_author_meta( 'ID' ) )
		)
	);
}

function dateDiffInDays($date1, $date2){ 
    // Calulating the difference in timestamps 
    $diff = strtotime($date2) - strtotime($date1); 
      
    // 1 day = 24 hours 
    // 24 * 60 * 60 = 86400 seconds 
    return abs(round($diff / 86400)); 
} 

/* Function to remove the default post meta from below the post feature image on home page and to add the existing format below the featured image*/

if ( ! function_exists( 'generate_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @since 0.1
	 */
	function generate_posted_on() {
		$date = apply_filters( 'generate_post_date', true );		
		$tags = apply_filters( 'generate_show_tags', true );
		$comments = apply_filters( 'generate_show_comments', true );

		$time_string = '<time class="entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="updated" datetime="%3$s" itemprop="dateModified">%4$s</time>' . $time_string;
		}
		if(!is_single()){
		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);
		}
		else{
		$time_string = sprintf( $time_string,
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);
		}


		// If our date is enabled, show it.
		if ( $date ) {
			echo apply_filters( 'generate_post_date_output', sprintf( '<span class="posted-on">%1$s</span>', // WPCS: XSS ok, sanitization ok.
				sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
					esc_url( get_permalink() ),
					esc_attr( get_the_time() ),
					$time_string
				)
			), $time_string );
		}
		$modified = get_the_modified_date('d-m-Y');

        $today =  date('d-m-Y');    

		$dateDiff = dateDiffInDays($modified, $today); 
		if($dateDiff < 90):
			echo '<span class="oj-post-updated">';
		    echo '<img src="'.IMAGES.'/counterclockwise-rotation.png">';
		    if(is_single()):
		    	echo '<span>recently updated</span>';
			endif;
			echo '</span>';
		endif;


		if (! post_password_required() && ( comments_open() || get_comments_number() ) && $comments ) {
			$comment_number = get_comments_number();
			echo '<span class="comments-link">';
				comments_popup_link( __( $comment_number , 'generatepress' ), __( '1 Comment', 'generatepress' ), __( '% Comments', 'generatepress' ) );
			echo '</span>';
		}

		$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'generatepress' ) );
		if ( $tags_list && $tags ) {
			echo apply_filters( 'generate_tag_list_output', sprintf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>', // WPCS: XSS ok, sanitization ok.
				esc_html_x( 'Tags', 'Used before tag names.', 'generatepress' ),
				$tags_list
			) );
		}
	}
}

// Function to remove default post meta (tags and leave a comment) from below the post on home page 

if ( ! function_exists( 'generate_entry_meta' ) ) {
	/**
	 * Prints HTML with meta information for the categories, tags.
	 *
	 * @since 1.2.5
	 */
	function generate_entry_meta() {
		$categories = apply_filters( 'generate_show_categories', true );	
		$author = apply_filters( 'generate_post_author', true );

		$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'generatepress' ) );
		if ( $categories_list && $categories ) {
			echo apply_filters( 'generate_category_list_output', sprintf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>', // WPCS: XSS ok, sanitization ok.
				esc_html_x( 'Categories', 'Used before category names.', 'generatepress' ),
				$categories_list
			) );
		}

		// If our author is enabled, show it.
		if ( $author ) {
			echo apply_filters( 'generate_post_author_output', sprintf( ' <span class="byline">%1$s</span>', // WPCS: XSS ok, sanitization ok.
				sprintf( '<span class="author vcard" itemtype="https://schema.org/Person" itemscope="itemscope" itemprop="author">%1$s <a class="url fn n" href="%2$s" title="%3$s" rel="author" itemprop="url"><span class="author-name" itemprop="name">%4$s</span></a></span>',
					__( 'by', 'generatepress' ),
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					/* translators: 1: Author name */
					esc_attr( sprintf( __( 'View all posts by %s', 'generatepress' ), get_the_author() ) ),
					esc_html( get_the_author() )
				)
			) );
		}
	}
}

// code for adding the widget line alongside widget title

if ( ! function_exists( 'generate_widgets_init' ) ) {
	add_action( 'widgets_init', 'generate_widgets_init' );
	/**
	 * Register widgetized area and update sidebar with default widgets
	 */
	function generate_widgets_init() {
		$widgets = array(
			'sidebar-1' => __( 'Right Sidebar', 'generatepress' ),
			'sidebar-2' => __( 'Left Sidebar', 'generatepress' ),
			'header' => __( 'Header', 'generatepress' ),
			'footer-1' => __( 'Footer Widget 1', 'generatepress' ),
			'footer-2' => __( 'Footer Widget 2', 'generatepress' ),
			'footer-3' => __( 'Footer Widget 3', 'generatepress' ),
			'footer-4' => __( 'Footer Widget 4', 'generatepress' ),
			'footer-5' => __( 'Footer Widget 5', 'generatepress' ),
			'footer-bar' => __( 'Footer Bar','generatepress' ),
			'top-bar' => __( 'Top Bar','generatepress' ),
		);

		foreach ( $widgets as $id => $name ) {
			register_sidebar( array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<aside id="%1$s" class="widget inner-padding %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => apply_filters( 'generate_start_widget_title', '<div class="widget-title-sec"><h2 class="widget-title">' ),
				'after_title'   => apply_filters( 'generate_end_widget_title', '</h2><span class="widget-line"></span></div>' ),
			) );
		}
	}
}

/* code for the posts pagination icon*/

add_filter( 'generate_next_link_text', function() {
    return '>';
} );
add_filter( 'generate_previous_link_text', function() {
    return '<';
} );

/* manually adding 2 fonts used in the theme */

add_filter( 'generate_typography_default_fonts', function( $fonts ) {
    $fonts[] = 'Raleway';
    $fonts[] = 'Poppins';
    return $fonts;
} );
/* Code to Remove google fonts from being loaded automatically*/

add_action( 'wp_enqueue_scripts','tu_remove_google_fonts', 10 );
function tu_remove_google_fonts() {
    wp_dequeue_style( 'generate-fonts' );
}
if ( ! function_exists( 'generate_post_meta' ) ) {
	add_action( 'generate_before_entry_title', 'generate_post_meta' );
	/**
	 * Build the post meta.
	 *
	 * @since 1.3.29
	 */
	function generate_post_meta() {
		$post_types = apply_filters( 'generate_entry_meta_post_types', array(
			'post',
		) );

		if ( in_array( get_post_type(), $post_types ) ) : ?>
			<div class="entry-meta">
				<?php generate_posted_on(); ?>
			</div><!-- .entry-meta -->
		<?php endif;
	}
}

if ( ! function_exists( 'generate_footer_meta' ) ) {

	add_action( 'generate_after_entry_content', 'generate_footer_meta' );

	/**
	 * Build the footer post meta.
	 *
	 * @since 1.3.30
	 */
	function generate_footer_meta() {
		$post_types = apply_filters( 'generate_footer_meta_post_types', array(
			'post',
		) );

		if ( in_array( get_post_type(), $post_types ) ) : ?>
			<footer class="entry-meta">
				<?php
				if(! is_single()){
					generate_entry_meta();
				}				

				// if ( is_single() ) {
				// 	generate_content_nav( 'nav-below' );
				// }
				?>
			</footer><!-- .entry-meta -->
		<?php endif;
	}
}

function wpdev_before_after($content) {
	$tags = apply_filters( 'generate_show_tags', true );
	$tags_list = get_the_tag_list( '', _x( ' ', 'Used between list items, there is a space after the comma.', 'generatepress' ) );
	if ( $tags_list && $tags ) {
	$aftercontent = apply_filters( 'generate_tag_list_output', sprintf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>', // WPCS: XSS ok, sanitization ok.
		esc_html_x( 'Tags', 'Used before tag names.', 'generatepress' ),
		$tags_list
	) );
	
	$my_user = get_the_author_meta( 'ID' );

	$aftercontent .='<div class="page-header clearfix '.$my_user.'"><a href="'.get_author_posts_url($my_user).'"><h3 class="page-title">'.get_avatar( $my_user ).'<span class="vcard">'.get_the_author_meta('first_name').' '.get_the_author_meta('last_name').'</span></h3></a><div class="author-info">'.my_job_title($my_user).get_the_author_meta( 'description' ).oj_author_social($my_user).'</div></div>';
	}
    $fullcontent = $content . $aftercontent;
    
    return $fullcontent;
}
add_filter('the_content', 'wpdev_before_after',1 );

function set_widget_tag_cloud_args($args) {
  $my_args = array('orderby'=>'count', 'order'=>'RAND' );
  $args = wp_parse_args( $args, $my_args );
return $args;
}
add_filter('widget_tag_cloud_args','set_widget_tag_cloud_args');
// display featured post thumbnails in WordPress feeds
function wcs_post_thumbnails_in_feeds( $content ) {
    global $post;
    if( has_post_thumbnail( $post->ID ) ) {
        $content = '<p>' . get_the_post_thumbnail( $post->ID ) . '</p>' . $content;
    }
    return $content;
}
add_filter( 'the_excerpt_rss', 'wcs_post_thumbnails_in_feeds' );
add_filter( 'the_content_feed', 'wcs_post_thumbnails_in_feeds' );
?>