<?php

// Add scripts and stylesheets
function startwordpress_scripts() {
  wp_enqueue_style( 'skeleton', get_template_directory_uri() . '/assets/css/skeleton.css' );
  wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_script( 'js', get_template_directory_uri() . '/assets/js/jQuery.js', array( 'jquery' ), true );
}

add_action( 'wp_enqueue_scripts', 'startwordpress_scripts' );

// WordPress Titles
add_theme_support( 'title-tag' );
?>

<?php

  //Comment section content formatting
  function customized_comment($comment, $args, $depth) {
      $GLOBALS['comment'] = $comment; //What does this do?
?>

<li class="comment-single-section" >

  <!-- Display commenters avatar & name -->
  <div class="comment-author-section">
      <div class="comment-author-avatar"><?php echo get_avatar($comment, 80 ); // Get comment author avatar from ID/email ?> </div>
      <div class="comment-author-name"><?php printf(__('%s'), get_comment_author()) ?></div>
  </div>

  <!-- Display message if comment is awaiting admin approval -->
  <?php if ($comment->comment_approved == '0') : ?>
    <em><php _e('Your comment is awaiting moderation.') ?></em><br />
  <?php endif; ?>

  <!-- Display comment content  -->
  <div class="comment-content">
    <?php comment_text(); ?>
  </div>

  <!-- Display Reply button & comment replies -->
  <?php
  // Test nested comments depth
  if ( $depth < $args['max_depth'] ) : ?>

    <!-- Display comment reply -->
    <div class="comment-reply">
      <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </div>

  <?php endif; } ?>




<?php
// Google Analytics tracking

add_action('wp_head', 'wpb_add_googleanalytics');
function wpb_add_googleanalytics() { ?>
 
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-102277548-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-102277548-1');
</script>


<?php } ?>

<?php
add_theme_support( 'post-thumbnails' );



/*Custom Post type start*/
function cw_post_type_notes() {
$supports = array(
'title', // post title
'editor', // post content
'author', // post author
'thumbnail', // featured images
'excerpt', // post excerpt
'comments', // post comments
'revisions', // post revisions
'post-formats', // post formats
);
$labels = array(
'name' => _x('Notes', 'plural'),
'singular_name' => _x('Note', 'singular'),
'menu_name' => _x('Notes', 'admin menu'),
'name_admin_bar' => _x('Notes', 'admin bar'),
'add_new' => _x('Add New Note', 'add new'),
'add_new_item' => __('Add New Note'),
'new_item' => __('New Notes'),
'edit_item' => __('Edit Notes'),
'view_item' => __('View Notes'),
'all_items' => __('All Notes'),
'search_items' => __('Search Notes'),
'not_found' => __('No notes found.'),
);
$args = array(
'supports' => $supports,
'labels' => $labels,
'public' => true,
'query_var' => true,
'rewrite' => array('slug' => 'notes'),
'has_archive' => false,
'hierarchical' => false,
'taxonomies'  => array( 'category' ),
);
register_post_type('notes', $args);
}
add_action('init', 'cw_post_type_notes');
/*Custom Post type end*/

/*Projects Post type start*/
function cw_post_type_projects() {
	$supports = array(
		'title', // post title
		'editor', // post content
		'thumbnail', // featured images
		'excerpt', // post excerpt
	);
	$labels = array(
		'name' => _x('Projects', 'plural'),
		'singular_name' => _x('Project', 'singular'),
		'menu_name' => _x('Projects', 'admin menu'),
		'name_admin_bar' => _x('Projects', 'admin bar'),
		'add_new' => _x('Add New Project', 'add new'),
		'add_new_item' => __('Add New Project'),
		'new_item' => __('New Project'),
		'edit_item' => __('Edit Project'),
		'view_item' => __('View Project'),
		'all_items' => __('All Projects'),
		'search_items' => __('Search Projects'),
		'not_found' => __('No projects found.'),
	);
	$args = array(
		'supports' => $supports,
		'labels' => $labels,
		'public' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'projects'),
		'has_archive' => false,
		'hierarchical' => false,
		'menu_icon' => 'dashicons-portfolio',
	);
	register_post_type('projects', $args);
}
add_action('init', 'cw_post_type_projects');

// Add meta boxes for Project settings
function projects_add_meta_boxes() {
	add_meta_box(
		'project_url_meta_box',
		'Project Link',
		'projects_url_meta_box_callback',
		'projects',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'projects_add_meta_boxes');

// Meta box callback function
function projects_url_meta_box_callback($post) {
	wp_nonce_field('project_url_meta_box', 'project_url_meta_box_nonce');
	$value = get_post_meta($post->ID, '_project_url', true);
	?>
	<p>
		<label for="project_url">External Project URL (optional):</label><br>
		<input type="url" id="project_url" name="project_url" value="<?php echo esc_attr($value); ?>" style="width: 100%;" placeholder="https://example.com/project">
	</p>
	<p class="description">If this project lives externally (GitHub, live site, etc.), add the URL here.</p>
	<?php
}

// Add checkbox to featured image box for projects
function projects_featured_image_checkbox($content, $post_id, $thumbnail_id) {
	$post = get_post($post_id);
	if ($post && $post->post_type === 'projects') {
		$show_image = get_post_meta($post_id, '_project_show_featured_image', true);
		$checked = ($show_image === '' || $show_image === '1') ? 'checked' : '';
		
		$checkbox = '<p style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">';
		$checkbox .= '<label>';
		$checkbox .= '<input type="checkbox" name="project_show_featured_image" value="1" ' . $checked . '> ';
		$checkbox .= 'Show on More Info page';
		$checkbox .= '</label>';
		$checkbox .= wp_nonce_field('project_display_meta_box', 'project_display_meta_box_nonce', true, false);
		$checkbox .= '</p>';
		
		$content .= $checkbox;
	}
	return $content;
}
add_filter('admin_post_thumbnail_html', 'projects_featured_image_checkbox', 10, 3);

// Save meta box data
function projects_save_meta_box_data($post_id) {
	// Check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	// Check permissions
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	
	// Save project URL
	if (isset($_POST['project_url_meta_box_nonce']) && wp_verify_nonce($_POST['project_url_meta_box_nonce'], 'project_url_meta_box')) {
		if (isset($_POST['project_url'])) {
			update_post_meta($post_id, '_project_url', esc_url_raw($_POST['project_url']));
		}
	}
	
	// Save show featured image setting
	if (isset($_POST['project_display_meta_box_nonce']) && wp_verify_nonce($_POST['project_display_meta_box_nonce'], 'project_display_meta_box')) {
		$show_image = isset($_POST['project_show_featured_image']) ? '1' : '0';
		update_post_meta($post_id, '_project_show_featured_image', $show_image);
	}
	
	// Save show excerpt setting
	if (isset($_POST['project_excerpt_meta_box_nonce']) && wp_verify_nonce($_POST['project_excerpt_meta_box_nonce'], 'project_excerpt_meta_box')) {
		$show_excerpt = isset($_POST['project_show_excerpt']) ? '1' : '0';
		update_post_meta($post_id, '_project_show_excerpt', $show_excerpt);
	}
}
add_action('save_post_projects', 'projects_save_meta_box_data');

// Add excerpt options for projects (character limit notice + show/hide checkbox)
function projects_excerpt_options() {
	global $post_type, $post;
	if ($post_type === 'projects') {
		$show_excerpt = get_post_meta($post->ID, '_project_show_excerpt', true);
		$checked = ($show_excerpt === '' || $show_excerpt === '1') ? 'checked' : '';
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// For classic editor
			var excerptBox = $('#postexcerpt .inside');
			if (excerptBox.length) {
				excerptBox.append('<p class="description" style="color: #d63638; margin-top: 10px;"><strong>Note:</strong> Keep the excerpt under 110 characters for best display on project cards.</p>');
				excerptBox.append('<p style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;"><label><input type="checkbox" name="project_show_excerpt" value="1" <?php echo $checked; ?>> Show excerpt on More Info page</label><?php echo wp_nonce_field('project_excerpt_meta_box', 'project_excerpt_meta_box_nonce', true, false); ?></p>');
			}
			
			// For block editor - add notice to excerpt panel
			if (typeof wp !== 'undefined' && wp.data) {
				var noticeAdded = false;
				wp.data.subscribe(function() {
					var excerptPanel = document.querySelector('.editor-post-excerpt textarea');
					if (excerptPanel && !noticeAdded) {
						var notice = document.createElement('p');
						notice.style.color = '#d63638';
						notice.style.marginTop = '8px';
						notice.style.fontSize = '12px';
						notice.innerHTML = '<strong>Note:</strong> Keep under 110 characters for best display.';
						excerptPanel.parentNode.appendChild(notice);
						noticeAdded = true;
					}
				});
			}
		});
		</script>
		<?php
	}
}
add_action('admin_footer', 'projects_excerpt_options');
/*Projects Post type end*/

?>