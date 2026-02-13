<?php

// Add scripts and stylesheets
function startwordpress_scripts() {
  wp_enqueue_style( 'skeleton', get_template_directory_uri() . '/assets/css/skeleton.css' );
  wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css' );
  wp_enqueue_style( 'phosphor-icons', 'https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css' );
  wp_enqueue_style( 'remix-icon', 'https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css' );
  wp_enqueue_script( 'js', get_template_directory_uri() . '/assets/js/jQuery.js', array( 'jquery' ), true );
}

add_action( 'wp_enqueue_scripts', 'startwordpress_scripts' );

// WordPress Titles
add_theme_support( 'title-tag' );

// Register Navigation Menu
function theme_register_nav_menus() {
    register_nav_menus( array(
        'header-menu' => __( 'Header Menu', 'kristapsbezbailis' ),
    ) );
}
add_action( 'after_setup_theme', 'theme_register_nav_menus' );
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

/*Page Icon Selector start*/

// Add meta box for page icon selection
function page_icon_meta_box() {
    add_meta_box(
        'page_icon_meta_box',
        'Page Icon',
        'page_icon_meta_box_callback',
        'page',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'page_icon_meta_box');

// Meta box callback - UI for selecting icons
function page_icon_meta_box_callback($post) {
    wp_nonce_field('page_icon_meta_box', 'page_icon_meta_box_nonce');
    
    $icon_type = get_post_meta($post->ID, '_page_icon_type', true);
    $icon_value = get_post_meta($post->ID, '_page_icon_value', true);
    $show_in_headline = get_post_meta($post->ID, '_page_icon_show_in_headline', true);
    
    // Default to emoji if not set
    if (empty($icon_type)) {
        $icon_type = 'emoji';
    }
    
    // Default to showing in headline
    if ($show_in_headline === '') {
        $show_in_headline = '1';
    }
    
    // Expanded Font Awesome icons organized by category
    $fa_icons = array(
        // General
        'fa-solid fa-house' => 'House',
        'fa-solid fa-user' => 'User',
        'fa-solid fa-users' => 'Users',
        'fa-solid fa-circle-user' => 'User Circle',
        'fa-solid fa-star' => 'Star',
        'fa-solid fa-heart' => 'Heart',
        'fa-solid fa-fire' => 'Fire',
        'fa-solid fa-bolt' => 'Bolt',
        'fa-solid fa-sun' => 'Sun',
        'fa-solid fa-moon' => 'Moon',
        'fa-solid fa-cloud' => 'Cloud',
        'fa-solid fa-snowflake' => 'Snowflake',
        'fa-solid fa-leaf' => 'Leaf',
        'fa-solid fa-seedling' => 'Seedling',
        'fa-solid fa-tree' => 'Tree',
        'fa-solid fa-mountain' => 'Mountain',
        'fa-solid fa-water' => 'Water',
        // Communication
        'fa-solid fa-envelope' => 'Envelope',
        'fa-solid fa-phone' => 'Phone',
        'fa-solid fa-comment' => 'Comment',
        'fa-solid fa-comments' => 'Comments',
        'fa-solid fa-message' => 'Message',
        'fa-solid fa-paper-plane' => 'Paper Plane',
        'fa-solid fa-inbox' => 'Inbox',
        'fa-solid fa-bell' => 'Bell',
        // Work & Business
        'fa-solid fa-briefcase' => 'Briefcase',
        'fa-solid fa-building' => 'Building',
        'fa-solid fa-industry' => 'Industry',
        'fa-solid fa-landmark' => 'Landmark',
        'fa-solid fa-chart-line' => 'Chart Line',
        'fa-solid fa-chart-pie' => 'Chart Pie',
        'fa-solid fa-chart-bar' => 'Chart Bar',
        'fa-solid fa-coins' => 'Coins',
        'fa-solid fa-wallet' => 'Wallet',
        'fa-solid fa-credit-card' => 'Credit Card',
        'fa-solid fa-handshake' => 'Handshake',
        // Creative & Media
        'fa-solid fa-palette' => 'Palette',
        'fa-solid fa-paintbrush' => 'Paintbrush',
        'fa-solid fa-pen' => 'Pen',
        'fa-solid fa-pencil' => 'Pencil',
        'fa-solid fa-pen-nib' => 'Pen Nib',
        'fa-solid fa-highlighter' => 'Highlighter',
        'fa-solid fa-camera' => 'Camera',
        'fa-solid fa-video' => 'Video',
        'fa-solid fa-film' => 'Film',
        'fa-solid fa-music' => 'Music',
        'fa-solid fa-headphones' => 'Headphones',
        'fa-solid fa-microphone' => 'Microphone',
        'fa-solid fa-guitar' => 'Guitar',
        'fa-solid fa-drum' => 'Drum',
        'fa-solid fa-image' => 'Image',
        'fa-solid fa-images' => 'Images',
        // Tech & Code
        'fa-solid fa-code' => 'Code',
        'fa-solid fa-terminal' => 'Terminal',
        'fa-solid fa-laptop' => 'Laptop',
        'fa-solid fa-desktop' => 'Desktop',
        'fa-solid fa-mobile' => 'Mobile',
        'fa-solid fa-tablet' => 'Tablet',
        'fa-solid fa-keyboard' => 'Keyboard',
        'fa-solid fa-mouse' => 'Mouse',
        'fa-solid fa-microchip' => 'Microchip',
        'fa-solid fa-server' => 'Server',
        'fa-solid fa-database' => 'Database',
        'fa-solid fa-cloud-arrow-up' => 'Cloud Upload',
        'fa-solid fa-wifi' => 'WiFi',
        'fa-solid fa-robot' => 'Robot',
        'fa-solid fa-bug' => 'Bug',
        'fa-solid fa-shield' => 'Shield',
        'fa-solid fa-lock' => 'Lock',
        'fa-solid fa-key' => 'Key',
        // Education & Learning
        'fa-solid fa-book' => 'Book',
        'fa-solid fa-book-open' => 'Book Open',
        'fa-solid fa-bookmark' => 'Bookmark',
        'fa-solid fa-graduation-cap' => 'Graduation Cap',
        'fa-solid fa-school' => 'School',
        'fa-solid fa-chalkboard' => 'Chalkboard',
        'fa-solid fa-lightbulb' => 'Lightbulb',
        'fa-solid fa-brain' => 'Brain',
        'fa-solid fa-atom' => 'Atom',
        'fa-solid fa-flask' => 'Flask',
        'fa-solid fa-microscope' => 'Microscope',
        'fa-solid fa-dna' => 'DNA',
        // Files & Organization
        'fa-solid fa-file' => 'File',
        'fa-solid fa-file-lines' => 'File Lines',
        'fa-solid fa-folder' => 'Folder',
        'fa-solid fa-folder-open' => 'Folder Open',
        'fa-solid fa-box' => 'Box',
        'fa-solid fa-boxes-stacked' => 'Boxes',
        'fa-solid fa-archive' => 'Archive',
        'fa-solid fa-clipboard' => 'Clipboard',
        'fa-solid fa-list' => 'List',
        'fa-solid fa-table' => 'Table',
        'fa-solid fa-calendar' => 'Calendar',
        'fa-solid fa-clock' => 'Clock',
        // Travel & Places
        'fa-solid fa-globe' => 'Globe',
        'fa-solid fa-earth-americas' => 'Earth Americas',
        'fa-solid fa-map' => 'Map',
        'fa-solid fa-location-dot' => 'Location',
        'fa-solid fa-compass' => 'Compass',
        'fa-solid fa-plane' => 'Plane',
        'fa-solid fa-car' => 'Car',
        'fa-solid fa-train' => 'Train',
        'fa-solid fa-ship' => 'Ship',
        'fa-solid fa-bicycle' => 'Bicycle',
        'fa-solid fa-road' => 'Road',
        'fa-solid fa-hotel' => 'Hotel',
        // Fun & Games
        'fa-solid fa-gamepad' => 'Gamepad',
        'fa-solid fa-puzzle-piece' => 'Puzzle',
        'fa-solid fa-dice' => 'Dice',
        'fa-solid fa-chess' => 'Chess',
        'fa-solid fa-trophy' => 'Trophy',
        'fa-solid fa-medal' => 'Medal',
        'fa-solid fa-crown' => 'Crown',
        'fa-solid fa-gem' => 'Gem',
        'fa-solid fa-gift' => 'Gift',
        'fa-solid fa-cake-candles' => 'Cake',
        'fa-solid fa-champagne-glasses' => 'Champagne',
        'fa-solid fa-party-horn' => 'Party Horn',
        // Arrows & UI
        'fa-solid fa-arrow-right' => 'Arrow Right',
        'fa-solid fa-arrow-up' => 'Arrow Up',
        'fa-solid fa-angles-right' => 'Angles Right',
        'fa-solid fa-circle-arrow-right' => 'Circle Arrow',
        'fa-solid fa-rocket' => 'Rocket',
        'fa-solid fa-wand-magic-sparkles' => 'Magic Wand',
        'fa-solid fa-sparkles' => 'Sparkles',
        'fa-solid fa-link' => 'Link',
        'fa-solid fa-share' => 'Share',
        'fa-solid fa-eye' => 'Eye',
        'fa-solid fa-magnifying-glass' => 'Search',
        'fa-solid fa-gear' => 'Gear',
        'fa-solid fa-sliders' => 'Sliders',
        'fa-solid fa-filter' => 'Filter',
        // Health & Wellness
        'fa-solid fa-heart-pulse' => 'Heart Pulse',
        'fa-solid fa-spa' => 'Spa',
        'fa-solid fa-yin-yang' => 'Yin Yang',
        'fa-solid fa-person-running' => 'Running',
        'fa-solid fa-dumbbell' => 'Dumbbell',
        // Food & Drink
        'fa-solid fa-mug-hot' => 'Mug Hot',
        'fa-solid fa-coffee' => 'Coffee',
        'fa-solid fa-utensils' => 'Utensils',
        'fa-solid fa-pizza-slice' => 'Pizza',
        'fa-solid fa-burger' => 'Burger',
        'fa-solid fa-cookie' => 'Cookie',
        'fa-solid fa-ice-cream' => 'Ice Cream',
        'fa-solid fa-wine-glass' => 'Wine Glass',
        'fa-solid fa-martini-glass' => 'Martini',
        'fa-solid fa-apple-whole' => 'Apple',
        'fa-solid fa-lemon' => 'Lemon',
        'fa-solid fa-carrot' => 'Carrot',
        // Animals
        'fa-solid fa-dog' => 'Dog',
        'fa-solid fa-cat' => 'Cat',
        'fa-solid fa-fish' => 'Fish',
        'fa-solid fa-dove' => 'Dove',
        'fa-solid fa-crow' => 'Crow',
        'fa-solid fa-feather' => 'Feather',
        'fa-solid fa-paw' => 'Paw',
        'fa-solid fa-spider' => 'Spider',
        'fa-solid fa-dragon' => 'Dragon',
        'fa-solid fa-hippo' => 'Hippo',
        'fa-solid fa-otter' => 'Otter',
        'fa-solid fa-frog' => 'Frog',
        'fa-solid fa-kiwi-bird' => 'Kiwi Bird',
        'fa-solid fa-horse' => 'Horse',
        // Brands
        'fa-brands fa-github' => 'GitHub',
        'fa-brands fa-twitter' => 'Twitter',
        'fa-brands fa-x-twitter' => 'X (Twitter)',
        'fa-brands fa-linkedin' => 'LinkedIn',
        'fa-brands fa-instagram' => 'Instagram',
        'fa-brands fa-facebook' => 'Facebook',
        'fa-brands fa-youtube' => 'YouTube',
        'fa-brands fa-tiktok' => 'TikTok',
        'fa-brands fa-discord' => 'Discord',
        'fa-brands fa-slack' => 'Slack',
        'fa-brands fa-spotify' => 'Spotify',
        'fa-brands fa-apple' => 'Apple',
        'fa-brands fa-google' => 'Google',
        'fa-brands fa-amazon' => 'Amazon',
        'fa-brands fa-dribbble' => 'Dribbble',
        'fa-brands fa-figma' => 'Figma',
        'fa-brands fa-codepen' => 'CodePen',
        'fa-brands fa-stack-overflow' => 'Stack Overflow',
        'fa-brands fa-npm' => 'NPM',
        'fa-brands fa-react' => 'React',
        'fa-brands fa-vuejs' => 'Vue.js',
        'fa-brands fa-node-js' => 'Node.js',
        'fa-brands fa-python' => 'Python',
        'fa-brands fa-js' => 'JavaScript',
        'fa-brands fa-php' => 'PHP',
        'fa-brands fa-wordpress' => 'WordPress',
    );
    
    // Emoji categories with colorful emojis
    $emoji_categories = array(
        'Smileys' => array('😀', '😃', '😄', '😁', '😊', '🥰', '😍', '🤩', '😎', '🤓', '🧐', '🤔', '😏', '🙂', '😌', '🤗'),
        'Gestures' => array('👍', '👎', '👏', '🙌', '🤝', '✌️', '🤞', '🤙', '👋', '✋', '🖐️', '👐', '💪', '🙏', '👆', '👉'),
        'Hearts' => array('❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '💖', '💝', '💘', '💗', '💓', '💕', '💞', '💟'),
        'Objects' => array('💡', '📚', '📖', '✏️', '🖊️', '🎨', '🎭', '🎬', '🎤', '🎧', '🎵', '🎹', '📷', '📱', '💻', '⌨️'),
        'Nature' => array('🌸', '🌺', '🌻', '🌹', '🌷', '🌱', '🌿', '🍀', '🌳', '🌴', '🍁', '🍂', '🌊', '🔥', '⭐', '🌙'),
        'Animals' => array('🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🦁', '🐯', '🐮', '🐷', '🐸', '🐵', '🦋'),
        'Food' => array('🍎', '🍊', '🍋', '🍇', '🍓', '🍑', '🥑', '🍕', '🍔', '🍟', '🌮', '🍣', '🍪', '🍩', '🍰', '☕'),
        'Activities' => array('⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🎱', '🎯', '🎮', '🎲', '🧩', '🏆', '🥇', '🏅', '🎪', '🎢'),
        'Travel' => array('✈️', '🚀', '🚂', '🚗', '🚕', '🚌', '🚢', '⛵', '🗺️', '🧭', '🏔️', '🏝️', '🏖️', '🌍', '🗽', '🗼'),
        'Symbols' => array('✅', '❌', '⭕', '❗', '❓', '💯', '🔴', '🟠', '🟡', '🟢', '🔵', '🟣', '⚫', '⚪', '🔶', '🔷'),
        'Misc' => array('🎉', '🎊', '🎁', '🎈', '🎀', '💎', '👑', '🔮', '🧲', '🔑', '🗝️', '💰', '💳', '📈', '🚩', '🏳️'),
        'Work' => array('💼', '📁', '📂', '📋', '📊', '📉', '📌', '📍', '✂️', '📎', '🖇️', '📐', '📏', '🗂️', '🗃️', '🗄️'),
    );
    
    // Phosphor Icons (clean, modern style)
    $phosphor_icons = array(
        'ph ph-house' => 'House',
        'ph ph-user' => 'User',
        'ph ph-users' => 'Users',
        'ph ph-heart' => 'Heart',
        'ph ph-star' => 'Star',
        'ph ph-fire' => 'Fire',
        'ph ph-lightning' => 'Lightning',
        'ph ph-sun' => 'Sun',
        'ph ph-moon' => 'Moon',
        'ph ph-cloud' => 'Cloud',
        'ph ph-tree' => 'Tree',
        'ph ph-leaf' => 'Leaf',
        'ph ph-flower' => 'Flower',
        'ph ph-mountains' => 'Mountains',
        'ph ph-waves' => 'Waves',
        'ph ph-envelope' => 'Envelope',
        'ph ph-chat' => 'Chat',
        'ph ph-chat-circle' => 'Chat Circle',
        'ph ph-phone' => 'Phone',
        'ph ph-paper-plane-tilt' => 'Paper Plane',
        'ph ph-bell' => 'Bell',
        'ph ph-megaphone' => 'Megaphone',
        'ph ph-briefcase' => 'Briefcase',
        'ph ph-buildings' => 'Buildings',
        'ph ph-chart-line-up' => 'Chart Up',
        'ph ph-chart-pie' => 'Chart Pie',
        'ph ph-wallet' => 'Wallet',
        'ph ph-coin' => 'Coin',
        'ph ph-palette' => 'Palette',
        'ph ph-paint-brush' => 'Paint Brush',
        'ph ph-pencil' => 'Pencil',
        'ph ph-pen-nib' => 'Pen Nib',
        'ph ph-camera' => 'Camera',
        'ph ph-video-camera' => 'Video',
        'ph ph-music-notes' => 'Music',
        'ph ph-headphones' => 'Headphones',
        'ph ph-microphone' => 'Microphone',
        'ph ph-image' => 'Image',
        'ph ph-code' => 'Code',
        'ph ph-terminal' => 'Terminal',
        'ph ph-laptop' => 'Laptop',
        'ph ph-desktop' => 'Desktop',
        'ph ph-device-mobile' => 'Mobile',
        'ph ph-cpu' => 'CPU',
        'ph ph-database' => 'Database',
        'ph ph-cloud-arrow-up' => 'Cloud Upload',
        'ph ph-wifi-high' => 'WiFi',
        'ph ph-robot' => 'Robot',
        'ph ph-bug' => 'Bug',
        'ph ph-shield' => 'Shield',
        'ph ph-lock' => 'Lock',
        'ph ph-key' => 'Key',
        'ph ph-book' => 'Book',
        'ph ph-book-open' => 'Book Open',
        'ph ph-bookmark' => 'Bookmark',
        'ph ph-graduation-cap' => 'Graduation',
        'ph ph-lightbulb' => 'Lightbulb',
        'ph ph-brain' => 'Brain',
        'ph ph-atom' => 'Atom',
        'ph ph-flask' => 'Flask',
        'ph ph-file' => 'File',
        'ph ph-folder' => 'Folder',
        'ph ph-archive' => 'Archive',
        'ph ph-clipboard' => 'Clipboard',
        'ph ph-calendar' => 'Calendar',
        'ph ph-clock' => 'Clock',
        'ph ph-globe' => 'Globe',
        'ph ph-map-pin' => 'Location',
        'ph ph-compass' => 'Compass',
        'ph ph-airplane' => 'Airplane',
        'ph ph-car' => 'Car',
        'ph ph-bicycle' => 'Bicycle',
        'ph ph-game-controller' => 'Gamepad',
        'ph ph-puzzle-piece' => 'Puzzle',
        'ph ph-trophy' => 'Trophy',
        'ph ph-crown' => 'Crown',
        'ph ph-gift' => 'Gift',
        'ph ph-cake' => 'Cake',
        'ph ph-confetti' => 'Confetti',
        'ph ph-rocket' => 'Rocket',
        'ph ph-magic-wand' => 'Magic Wand',
        'ph ph-sparkle' => 'Sparkle',
        'ph ph-link' => 'Link',
        'ph ph-eye' => 'Eye',
        'ph ph-magnifying-glass' => 'Search',
        'ph ph-gear' => 'Gear',
        'ph ph-sliders' => 'Sliders',
        'ph ph-heart-beat' => 'Heartbeat',
        'ph ph-barbell' => 'Barbell',
        'ph ph-coffee' => 'Coffee',
        'ph ph-cooking-pot' => 'Cooking',
        'ph ph-pizza' => 'Pizza',
        'ph ph-wine' => 'Wine',
        'ph ph-apple-logo' => 'Apple',
        'ph ph-dog' => 'Dog',
        'ph ph-cat' => 'Cat',
        'ph ph-fish' => 'Fish',
        'ph ph-bird' => 'Bird',
        'ph ph-butterfly' => 'Butterfly',
        'ph ph-paw-print' => 'Paw',
        'ph ph-github-logo' => 'GitHub',
        'ph ph-twitter-logo' => 'Twitter',
        'ph ph-linkedin-logo' => 'LinkedIn',
        'ph ph-instagram-logo' => 'Instagram',
        'ph ph-youtube-logo' => 'YouTube',
        'ph ph-discord-logo' => 'Discord',
        'ph ph-spotify-logo' => 'Spotify',
        'ph ph-figma-logo' => 'Figma',
    );
    
    // Remix Icons (friendly, rounded style)
    $remix_icons = array(
        'ri-home-line' => 'Home',
        'ri-user-line' => 'User',
        'ri-team-line' => 'Team',
        'ri-heart-line' => 'Heart',
        'ri-star-line' => 'Star',
        'ri-fire-line' => 'Fire',
        'ri-flashlight-line' => 'Flash',
        'ri-sun-line' => 'Sun',
        'ri-moon-line' => 'Moon',
        'ri-cloud-line' => 'Cloud',
        'ri-plant-line' => 'Plant',
        'ri-leaf-line' => 'Leaf',
        'ri-flower-line' => 'Flower',
        'ri-mail-line' => 'Mail',
        'ri-chat-1-line' => 'Chat',
        'ri-message-line' => 'Message',
        'ri-phone-line' => 'Phone',
        'ri-send-plane-line' => 'Send',
        'ri-notification-line' => 'Notification',
        'ri-briefcase-line' => 'Briefcase',
        'ri-building-line' => 'Building',
        'ri-line-chart-line' => 'Chart',
        'ri-pie-chart-line' => 'Pie Chart',
        'ri-wallet-line' => 'Wallet',
        'ri-money-dollar-circle-line' => 'Money',
        'ri-palette-line' => 'Palette',
        'ri-brush-line' => 'Brush',
        'ri-pencil-line' => 'Pencil',
        'ri-pen-nib-line' => 'Pen',
        'ri-camera-line' => 'Camera',
        'ri-video-line' => 'Video',
        'ri-music-line' => 'Music',
        'ri-headphone-line' => 'Headphones',
        'ri-mic-line' => 'Mic',
        'ri-image-line' => 'Image',
        'ri-code-line' => 'Code',
        'ri-terminal-line' => 'Terminal',
        'ri-macbook-line' => 'Laptop',
        'ri-computer-line' => 'Computer',
        'ri-smartphone-line' => 'Phone',
        'ri-cpu-line' => 'CPU',
        'ri-database-line' => 'Database',
        'ri-cloud-line' => 'Cloud',
        'ri-wifi-line' => 'WiFi',
        'ri-robot-line' => 'Robot',
        'ri-bug-line' => 'Bug',
        'ri-shield-line' => 'Shield',
        'ri-lock-line' => 'Lock',
        'ri-key-line' => 'Key',
        'ri-book-line' => 'Book',
        'ri-book-open-line' => 'Book Open',
        'ri-bookmark-line' => 'Bookmark',
        'ri-graduation-cap-line' => 'Graduation',
        'ri-lightbulb-line' => 'Lightbulb',
        'ri-brain-line' => 'Brain',
        'ri-flask-line' => 'Flask',
        'ri-file-line' => 'File',
        'ri-folder-line' => 'Folder',
        'ri-archive-line' => 'Archive',
        'ri-clipboard-line' => 'Clipboard',
        'ri-calendar-line' => 'Calendar',
        'ri-time-line' => 'Time',
        'ri-global-line' => 'Globe',
        'ri-map-pin-line' => 'Location',
        'ri-compass-line' => 'Compass',
        'ri-plane-line' => 'Plane',
        'ri-car-line' => 'Car',
        'ri-bike-line' => 'Bike',
        'ri-gamepad-line' => 'Gamepad',
        'ri-puzzle-line' => 'Puzzle',
        'ri-trophy-line' => 'Trophy',
        'ri-vip-crown-line' => 'Crown',
        'ri-gift-line' => 'Gift',
        'ri-cake-line' => 'Cake',
        'ri-rocket-line' => 'Rocket',
        'ri-magic-line' => 'Magic',
        'ri-sparkling-line' => 'Sparkle',
        'ri-link' => 'Link',
        'ri-eye-line' => 'Eye',
        'ri-search-line' => 'Search',
        'ri-settings-line' => 'Settings',
        'ri-equalizer-line' => 'Equalizer',
        'ri-heart-pulse-line' => 'Heartbeat',
        'ri-boxing-line' => 'Boxing',
        'ri-cup-line' => 'Cup',
        'ri-restaurant-line' => 'Restaurant',
        'ri-github-fill' => 'GitHub',
        'ri-twitter-line' => 'Twitter',
        'ri-linkedin-line' => 'LinkedIn',
        'ri-instagram-line' => 'Instagram',
        'ri-youtube-line' => 'YouTube',
        'ri-discord-line' => 'Discord',
        'ri-spotify-line' => 'Spotify',
        'ri-dribbble-line' => 'Dribbble',
        'ri-emotion-happy-line' => 'Happy',
        'ri-emotion-laugh-line' => 'Laugh',
        'ri-thumb-up-line' => 'Thumbs Up',
        'ri-hand-heart-line' => 'Hand Heart',
        'ri-medal-line' => 'Medal',
        'ri-award-line' => 'Award',
    );
    ?>
    <style>
        .page-icon-meta-box label { display: block; margin-bottom: 5px; font-weight: 600; }
        .page-icon-meta-box input[type="radio"] { margin-right: 5px; }
        .page-icon-meta-box .icon-type-row { margin-bottom: 15px; }
        .page-icon-meta-box .icon-type-row label { display: inline-block; margin-right: 10px; font-weight: normal; }
        .page-icon-meta-box .icon-input-row { margin-bottom: 10px; }
        .page-icon-meta-box input[type="text"] { width: 100%; }
        .page-icon-meta-box .icon-preview { margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 4px; text-align: center; font-size: 24px; }
        .page-icon-meta-box .description { color: #666; font-size: 12px; margin-top: 5px; }
        
        /* Icon Grid Styles */
        .page-icon-meta-box .icon-grid { 
            display: grid; 
            grid-template-columns: repeat(6, 1fr); 
            gap: 4px; 
            max-height: 220px; 
            overflow-y: auto; 
            border: 1px solid #ddd; 
            padding: 8px; 
            border-radius: 4px;
            background: #fff;
        }
        .page-icon-meta-box .icon-grid-item {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            background: #f9f9f9;
            transition: all 0.15s ease;
        }
        .page-icon-meta-box .icon-grid-item:hover {
            background: #e9e9e9;
            border-color: #999;
        }
        .page-icon-meta-box .icon-grid-item.selected {
            background: #0073aa;
            color: #fff;
            border-color: #0073aa;
        }
        .page-icon-meta-box .icon-grid-item i {
            pointer-events: none;
        }
        .page-icon-meta-box .selected-icon-name {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        /* Emoji Grid Styles */
        .page-icon-meta-box .emoji-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 3px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
            background: #fff;
        }
        .page-icon-meta-box .emoji-grid-item {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.15s ease;
        }
        .page-icon-meta-box .emoji-grid-item:hover {
            background: #e9e9e9;
            border-color: #999;
            transform: scale(1.2);
        }
        .page-icon-meta-box .emoji-grid-item.selected {
            background: #e0f0ff;
            border-color: #0073aa;
        }
        
        /* Category tabs */
        .page-icon-meta-box .emoji-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 8px;
        }
        .page-icon-meta-box .emoji-tab {
            padding: 4px 8px;
            font-size: 11px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 3px;
            cursor: pointer;
        }
        .page-icon-meta-box .emoji-tab:hover {
            background: #e0e0e0;
        }
        .page-icon-meta-box .emoji-tab.active {
            background: #0073aa;
            color: #fff;
            border-color: #0073aa;
        }
        .page-icon-meta-box .emoji-category {
            display: none;
        }
        .page-icon-meta-box .emoji-category.active {
            display: grid;
        }
    </style>
    
    <div class="page-icon-meta-box">
        <div class="icon-type-row">
            <label style="display:block; margin-bottom: 8px;">Icon Type:</label>
            <label>
                <input type="radio" name="page_icon_type" value="emoji" <?php checked($icon_type, 'emoji'); ?>>
                Emoji
            </label>
            <label>
                <input type="radio" name="page_icon_type" value="fontawesome" <?php checked($icon_type, 'fontawesome'); ?>>
                Font Awesome
            </label>
            <label>
                <input type="radio" name="page_icon_type" value="phosphor" <?php checked($icon_type, 'phosphor'); ?>>
                Phosphor
            </label>
            <label>
                <input type="radio" name="page_icon_type" value="remix" <?php checked($icon_type, 'remix'); ?>>
                Remix
            </label>
            <label>
                <input type="radio" name="page_icon_type" value="none" <?php checked($icon_type, 'none'); ?>>
                None
            </label>
        </div>
        
        <div class="icon-input-row emoji-input" style="<?php echo ($icon_type !== 'emoji') ? 'display:none;' : ''; ?>">
            <label>Select Emoji:</label>
            <div class="emoji-tabs">
                <?php $first = true; foreach ($emoji_categories as $cat_name => $emojis) : ?>
                    <span class="emoji-tab <?php echo $first ? 'active' : ''; ?>" data-category="<?php echo esc_attr(sanitize_title($cat_name)); ?>">
                        <?php echo esc_html($cat_name); ?>
                    </span>
                <?php $first = false; endforeach; ?>
            </div>
            <?php $first = true; foreach ($emoji_categories as $cat_name => $emojis) : ?>
                <div class="emoji-category emoji-grid <?php echo $first ? 'active' : ''; ?>" data-category="<?php echo esc_attr(sanitize_title($cat_name)); ?>">
                    <?php foreach ($emojis as $emoji) : ?>
                        <div class="emoji-grid-item <?php echo ($icon_type === 'emoji' && $icon_value === $emoji) ? 'selected' : ''; ?>" 
                             data-emoji="<?php echo esc_attr($emoji); ?>">
                            <?php echo $emoji; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php $first = false; endforeach; ?>
            <input type="hidden" id="page_icon_emoji" name="page_icon_emoji" 
                   value="<?php echo ($icon_type === 'emoji') ? esc_attr($icon_value) : ''; ?>">
            <p class="description" style="margin-top: 8px;">Or paste custom emoji:</p>
            <input type="text" id="page_icon_emoji_custom" name="page_icon_emoji_custom" 
                   placeholder="Paste any emoji here"
                   value="">
        </div>
        
        <div class="icon-input-row fa-input" style="<?php echo ($icon_type !== 'fontawesome') ? 'display:none;' : ''; ?>">
            <label>Select Icon:</label>
            <div class="icon-grid">
                <?php foreach ($fa_icons as $class => $name) : ?>
                    <div class="icon-grid-item <?php echo (($icon_type === 'fontawesome') && $icon_value === $class) ? 'selected' : ''; ?>" 
                         data-icon="<?php echo esc_attr($class); ?>" 
                         data-name="<?php echo esc_attr($name); ?>"
                         title="<?php echo esc_attr($name); ?>">
                        <i class="<?php echo esc_attr($class); ?>"></i>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="selected-icon-name">
                <?php 
                $selected_name = ($icon_type === 'fontawesome' && isset($fa_icons[$icon_value])) ? $fa_icons[$icon_value] : 'Click an icon to select';
                echo esc_html($selected_name);
                ?>
            </div>
            <input type="hidden" id="page_icon_fa" name="page_icon_fa" 
                   value="<?php echo ($icon_type === 'fontawesome') ? esc_attr($icon_value) : ''; ?>">
            
            <p class="description" style="margin-top: 10px;">Or enter custom Font Awesome class:</p>
            <input type="text" id="page_icon_fa_custom" name="page_icon_fa_custom" 
                   placeholder="e.g. fa-solid fa-star"
                   value="<?php echo ($icon_type === 'fontawesome' && !array_key_exists($icon_value, $fa_icons)) ? esc_attr($icon_value) : ''; ?>">
        </div>
        
        <!-- Phosphor Icons Grid -->
        <div class="icon-input-row phosphor-input" style="<?php echo ($icon_type !== 'phosphor') ? 'display:none;' : ''; ?>">
            <label>Select Phosphor Icon:</label>
            <div class="icon-grid">
                <?php foreach ($phosphor_icons as $class => $name) : ?>
                    <div class="icon-grid-item phosphor-icon <?php echo (($icon_type === 'phosphor') && $icon_value === $class) ? 'selected' : ''; ?>" 
                         data-icon="<?php echo esc_attr($class); ?>" 
                         data-name="<?php echo esc_attr($name); ?>"
                         title="<?php echo esc_attr($name); ?>">
                        <i class="<?php echo esc_attr($class); ?>"></i>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="selected-icon-name phosphor-name">
                <?php 
                $selected_name = ($icon_type === 'phosphor' && isset($phosphor_icons[$icon_value])) ? $phosphor_icons[$icon_value] : 'Click an icon to select';
                echo esc_html($selected_name);
                ?>
            </div>
            <input type="hidden" id="page_icon_phosphor" name="page_icon_phosphor" 
                   value="<?php echo ($icon_type === 'phosphor') ? esc_attr($icon_value) : ''; ?>">
        </div>
        
        <!-- Remix Icons Grid -->
        <div class="icon-input-row remix-input" style="<?php echo ($icon_type !== 'remix') ? 'display:none;' : ''; ?>">
            <label>Select Remix Icon:</label>
            <div class="icon-grid">
                <?php foreach ($remix_icons as $class => $name) : ?>
                    <div class="icon-grid-item remix-icon <?php echo (($icon_type === 'remix') && $icon_value === $class) ? 'selected' : ''; ?>" 
                         data-icon="<?php echo esc_attr($class); ?>" 
                         data-name="<?php echo esc_attr($name); ?>"
                         title="<?php echo esc_attr($name); ?>">
                        <i class="<?php echo esc_attr($class); ?>"></i>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="selected-icon-name remix-name">
                <?php 
                $selected_name = ($icon_type === 'remix' && isset($remix_icons[$icon_value])) ? $remix_icons[$icon_value] : 'Click an icon to select';
                echo esc_html($selected_name);
                ?>
            </div>
            <input type="hidden" id="page_icon_remix" name="page_icon_remix" 
                   value="<?php echo ($icon_type === 'remix') ? esc_attr($icon_value) : ''; ?>">
        </div>
        
        <?php if ($icon_value && $icon_type !== 'none') : ?>
        <div class="icon-preview">
            Preview: 
            <?php if ($icon_type === 'fontawesome' || $icon_type === 'phosphor' || $icon_type === 'remix') : ?>
                <i class="<?php echo esc_attr($icon_value); ?>"></i>
            <?php else : ?>
                <?php echo esc_html($icon_value); ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
            <label style="font-weight: normal; cursor: pointer;">
                <input type="checkbox" name="page_icon_show_in_headline" value="1" <?php checked($show_in_headline, '1'); ?>>
                Show icon in page headline
            </label>
            <p class="description">When enabled, the icon will appear before the page title on the page itself.</p>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Toggle icon type sections
        $('input[name="page_icon_type"]').on('change', function() {
            var type = $(this).val();
            $('.emoji-input, .fa-input, .phosphor-input, .remix-input').hide();
            if (type === 'emoji') {
                $('.emoji-input').show();
            } else if (type === 'fontawesome') {
                $('.fa-input').show();
            } else if (type === 'phosphor') {
                $('.phosphor-input').show();
            } else if (type === 'remix') {
                $('.remix-input').show();
            }
        });
        
        // Emoji category tabs
        $('.emoji-tab').on('click', function() {
            var category = $(this).data('category');
            $('.emoji-tab').removeClass('active');
            $(this).addClass('active');
            $('.emoji-category').removeClass('active');
            $('.emoji-category[data-category="' + category + '"]').addClass('active');
        });
        
        // Emoji grid selection
        $('.emoji-grid-item').on('click', function() {
            var emoji = $(this).data('emoji');
            $('.emoji-grid-item').removeClass('selected');
            $(this).addClass('selected');
            $('#page_icon_emoji').val(emoji);
            $('#page_icon_emoji_custom').val('');
        });
        
        // Custom emoji input
        $('#page_icon_emoji_custom').on('input', function() {
            if ($(this).val()) {
                $('.emoji-grid-item').removeClass('selected');
                $('#page_icon_emoji').val($(this).val());
            }
        });
        
        // Font Awesome grid selection
        $('.fa-input .icon-grid-item').on('click', function() {
            var icon = $(this).data('icon');
            var name = $(this).data('name');
            $('.fa-input .icon-grid-item').removeClass('selected');
            $(this).addClass('selected');
            $('#page_icon_fa').val(icon);
            $('.fa-input .selected-icon-name').text(name);
            $('#page_icon_fa_custom').val('');
        });
        
        // Custom FA input
        $('#page_icon_fa_custom').on('input', function() {
            if ($(this).val()) {
                $('.fa-input .icon-grid-item').removeClass('selected');
                $('#page_icon_fa').val('');
                $('.fa-input .selected-icon-name').text('Using custom class');
            }
        });
        
        // Phosphor grid selection
        $('.phosphor-input .icon-grid-item').on('click', function() {
            var icon = $(this).data('icon');
            var name = $(this).data('name');
            $('.phosphor-input .icon-grid-item').removeClass('selected');
            $(this).addClass('selected');
            $('#page_icon_phosphor').val(icon);
            $('.phosphor-name').text(name);
        });
        
        // Remix grid selection
        $('.remix-input .icon-grid-item').on('click', function() {
            var icon = $(this).data('icon');
            var name = $(this).data('name');
            $('.remix-input .icon-grid-item').removeClass('selected');
            $(this).addClass('selected');
            $('#page_icon_remix').val(icon);
            $('.remix-name').text(name);
        });
    });
    </script>
    <?php
}

// Save page icon meta data
function save_page_icon_meta($post_id) {
    // Check nonce
    if (!isset($_POST['page_icon_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['page_icon_meta_box_nonce'], 'page_icon_meta_box')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    
    // Get icon type
    $icon_type = isset($_POST['page_icon_type']) ? sanitize_text_field($_POST['page_icon_type']) : 'none';
    
    // Get icon value based on type
    $icon_value = '';
    if ($icon_type === 'emoji') {
        $icon_value = isset($_POST['page_icon_emoji']) ? sanitize_text_field($_POST['page_icon_emoji']) : '';
    } elseif ($icon_type === 'fontawesome') {
        // Check custom input first, then grid selection
        $custom_fa = isset($_POST['page_icon_fa_custom']) ? sanitize_text_field($_POST['page_icon_fa_custom']) : '';
        $select_fa = isset($_POST['page_icon_fa']) ? sanitize_text_field($_POST['page_icon_fa']) : '';
        $icon_value = !empty($custom_fa) ? $custom_fa : $select_fa;
    } elseif ($icon_type === 'phosphor') {
        $icon_value = isset($_POST['page_icon_phosphor']) ? sanitize_text_field($_POST['page_icon_phosphor']) : '';
    } elseif ($icon_type === 'remix') {
        $icon_value = isset($_POST['page_icon_remix']) ? sanitize_text_field($_POST['page_icon_remix']) : '';
    }
    
    update_post_meta($post_id, '_page_icon_type', $icon_type);
    update_post_meta($post_id, '_page_icon_value', $icon_value);
    
    // Save show in headline option
    $show_in_headline = isset($_POST['page_icon_show_in_headline']) ? '1' : '0';
    update_post_meta($post_id, '_page_icon_show_in_headline', $show_in_headline);
}
add_action('save_post_page', 'save_page_icon_meta');

// Helper function to get page title with icon
function get_page_title_with_icon($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $title = get_the_title($post_id);
    $icon_type = get_post_meta($post_id, '_page_icon_type', true);
    $icon_value = get_post_meta($post_id, '_page_icon_value', true);
    $show_in_headline = get_post_meta($post_id, '_page_icon_show_in_headline', true);
    
    // Default to showing if not set
    if ($show_in_headline === '') {
        $show_in_headline = '1';
    }
    
    // If icon should be shown and there is an icon
    if ($show_in_headline === '1' && $icon_value && $icon_type !== 'none') {
        if ($icon_type === 'fontawesome' || $icon_type === 'phosphor' || $icon_type === 'remix') {
            $icon_html = '<i class="' . esc_attr($icon_value) . '" style="margin-right: 0.3em;"></i>';
        } else {
            $icon_html = '<span class="page-title-emoji" style="margin-right: 0.3em;">' . esc_html($icon_value) . '</span>';
        }
        return $icon_html . esc_html($title);
    }
    
    return esc_html($title);
}

// Shortcut function to echo the title with icon
function the_page_title_with_icon($post_id = null) {
    echo get_page_title_with_icon($post_id);
}

// Load icon libraries in admin for preview
function load_icon_libraries_admin() {
    wp_enqueue_style('font-awesome-admin', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
    wp_enqueue_style('phosphor-icons-admin', 'https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css');
    wp_enqueue_style('remix-icon-admin', 'https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css');
}
add_action('admin_enqueue_scripts', 'load_icon_libraries_admin');

// Custom Nav Walker to display icons in menu
class Icon_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $icon_type = get_post_meta($item->object_id, '_page_icon_type', true);
        $icon_value = get_post_meta($item->object_id, '_page_icon_value', true);
        
        $icon_html = '';
        if ($icon_value && $icon_type !== 'none') {
            if ($icon_type === 'fontawesome' || $icon_type === 'phosphor' || $icon_type === 'remix') {
                $icon_html = '<i class="' . esc_attr($icon_value) . '"></i> ';
            } else {
                $icon_html = '<span class="menu-emoji">' . esc_html($icon_value) . '</span> ';
            }
        }
        
        // Get classes for the li element
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $output .= '<li' . $class_names . '>';
        
        $attributes = '';
        if (!empty($item->url)) {
            $attributes .= ' href="' . esc_attr($item->url) . '"';
        }
        
        $output .= '<a' . $attributes . '>' . $icon_html . esc_html($item->title) . '</a>';
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}
/*Page Icon Selector end*/

?>