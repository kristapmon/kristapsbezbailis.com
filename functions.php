<?php

// Add scripts and stylesheets
function startwordpress_scripts() {
  wp_enqueue_style( 'skeleton', get_template_directory_uri() . '/css/skeleton.css' );
  wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_script( 'js', get_template_directory_uri() . '/js/jQuery.js', array( 'jquery' ), true );
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

?>