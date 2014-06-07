<?php //Opening PHP tag

// Load Edit Registration Page
//require_once( get_stylesheet_directory_uri() . '/edit-registration.php' );

// Remove admin toolbar for all users
// TODO: Add this as an option to settings
add_filter('show_admin_bar', '__return_false');

function enqeue_scripts() {
	// Bootstrap Popover (and required Tooltip)
	wp_enqueue_script(
		'bootstrap-popover-tooltip',
		get_stylesheet_directory_uri() . '/js/bootstrap-popover-tooltip.js',
		array( 'jquery' ),
		3.1
	);
	wp_enqueue_style( 
		'bootstrap-popover-tooltip-css',
		get_stylesheet_directory_uri() . '/css/bootstrap-popover-tooltip.css',
		array(),
		3.1
	);
	// Custom JS
	wp_enqueue_script(
		'productcamp-custom',
		get_stylesheet_directory_uri() . '/js/productcamp-custom.js',
		array( 'jquery' ),
		1.0	);
}
add_action( 'wp_enqueue_scripts', 'enqeue_scripts' );

// Load WP Dashicons (used for author bio icon in content.php)
function load_dashicons_front_end() {
	wp_enqueue_style( 'dashicons' );
}
add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );

// Load sessions using WP_Query parameters
// Attributes are passed to WP_Query
//   Ex: [load_sessions p="101"] loads post 101 and 
//   [load_sessions category_name="session-block-1"] loads all posts in the category with slug session-block-1
add_shortcode('load_sessions', 'generate_posts');
function generate_posts($atts, $content){
	extract(shortcode_atts(array(
		'in_accordion' => 0) // in_accordion shaves off a bit of the author bio because it's smaller (default is false)
		, $atts));

	global $post;

	$posts = new WP_Query($atts);
	$out = '';

	if ($posts->have_posts())
		while ($posts->have_posts()):
			$posts->the_post();

			/* Build out the post according to content.php, but without images and attachments */
			$out .= '<article id="session-' . get_the_ID() . '" class="blog_post accordion">';
			$out .= '<div class="post-content">';
			$out .= '<header class="post-header">';
			$out .= '<h1 class="post-title">' . get_the_title() . '</h1>';
			$out .= '</header><!-- .entry-header -->';

				$categories = get_the_category();
				$session_time = 'TBD';
				$session_room = 'TBD';
				if($categories){
					foreach($categories as $category) {
						// Get parent
						$category_parent_id = $category->category_parent;
						if ( $category_parent_id != 0 ) {
							$category_parent = get_term( $category_parent_id, 'category' );
							if ($category_parent->slug == 'session-time') {
								$session_time = $category->name;
							} elseif ($category_parent->slug == 'session-room') {
								$session_room = $category->name;
							}
						}
					}
				}

			$out .= '<div class="entry-info">';
			$out .= '<table>';
			$out .= '<tbody>';
			$out .= '<tr>';
			$out .= '<td class="session-time">Time: ' . $session_time . '</td>';
			$out .= '<td class="session-avatar" rowspan="2">';
			$out .= get_avatar( get_the_author_meta( 'ID' ), 40 );
			$out .= '</td>';
			$out .= '<td class="session-bio" rowspan="2">';

			if ( get_the_author_meta('description') ) {
				$out .= '<span class="bio-large">';
				$out .= substr(get_the_author_meta('description'),0,80);
				$out .= '</span>';
				$out .= '<span class="bio-medium">';
				$out .= substr(get_the_author_meta('description'),0,55);
				$out .= '</span>';
				$out .= '<span class="bio-small">';
				
				if ( $in_accordion ) { /* Shave off a bit more because the accordion is smaller */
					$out .= substr(get_the_author_meta('description'),0,25);
				} else {
					$out .= substr(get_the_author_meta('description'),0,30);
				}
				
				$out .= '</span>';
				/* Placing individual read more's in the above spans (or making them divs) messes things up really bad. So we have to do one, rather than have each separate to specify a different data-placement. */
				$out .= '... <a href="#" class="full-bio" data-toggle="popover" data-placement="bottom" title="" data-content="' . addcslashes(get_the_author_meta('description'), '"') . '" data-original-title="About ' . addcslashes(get_the_author_meta('display_name'), '"') . '">Read more &raquo;</a></td>';
			} else {
				$out .= get_the_author_meta('display_name') . ' has not yet created a bio.';
			}
			
			$out .= '</tr>';
			$out .= '<tr>';
			$out .= '<td class="session-room">Room: ' . $session_room . '</td>';
			$out .= '</tr>';
			$out .= '<tr>';
			$out .= '<td class="session-leader">Session Leader:&nbsp;';
			$out .= get_the_author_meta('display_name');
			
			if ( get_the_author_meta('description') ) {
				$out .= '<a href="#" class="full-bio no-underline" data-toggle="popover" data-placement="bottom" title="" data-content="' . addcslashes(get_the_author_meta('description'), '"') . '" data-original-title="About ' . addcslashes(get_the_author_meta('display_name'), '"') . '">';
				$out .= '<div class="dashicons dashicons-external"></div>';
				$out .= '</a>';
			}

			$out .= '</td>';
			$out .= '</td>';
			$out .= '</tbody>';
			$out .= '</table>';
			$out .= '</div><!-- .entry-info -->';
			$out .= '<div class="entry-content">';

			if ( strlen(get_the_content()) > 310) {
				$out .= '<div id="entry-truncated-' . get_the_ID() . '" class="entry-truncated">';
				$out .= substr(get_the_content(),0,300);
				$out .= '<span class="read-more">... <a href="#" id="read-more-' . get_the_ID() . '" class="session-read-more">Read&nbsp;more&nbsp;&raquo;</a></span>';
				$out .= '</div>';
				$out .= '<div id="entry-full-' . get_the_ID() . '" class="entry-full entry-hidden">';
				$out .= get_the_content_with_formatting();
				$out .= '<span class="read-less"><a href="#" id="read-less-' . get_the_ID() . '" class="session-read-less">&laquo;&nbsp;Read&nbsp;less</a></span>';
				$out .= '</div>';
			} else {
				$out .= '<div id="entry-full-' . get_the_ID() . '" class="entry-full">';
				$out .= get_the_content_with_formatting();
				$out .= '</div>';
			}
				$out .= '</div><!-- .entry-content -->';
				$out .= '</div>';
				$out .= '</article><!-- #session-' . get_the_ID() . '-->';
			
    endwhile;
  else
    return 'Nothing found'; // no posts found

  wp_reset_query();
  return html_entity_decode($out);
}

// Used in place of the_content() so that text appears in correct location (the_content places output at top of page when run in a shortcode)
function get_the_content_with_formatting () {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}
	
?>