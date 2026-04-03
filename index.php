<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!-- Start looping all blog posts into a single page -->

<?php

	get_header(); // Gets header.php file


?>

<?php
// Check if there's a page for posts set (blog page) and use its icon
$blog_page_id = get_option('page_for_posts');
if ($blog_page_id) {
    $icon_type = get_post_meta($blog_page_id, '_page_icon_type', true);
    $icon_value = get_post_meta($blog_page_id, '_page_icon_value', true);
    $show_in_headline = get_post_meta($blog_page_id, '_page_icon_show_in_headline', true);
    $blog_title = get_the_title($blog_page_id);
} else {
    $icon_type = '';
    $icon_value = '';
    $show_in_headline = '0';
    $blog_title = 'Blog';
}
?>

<section class="timeline-section">
		
		<div class="timeline-header">
			<h1>
			<?php 
			if ($show_in_headline === '1' && $icon_value && $icon_type !== 'none') {
				if ($icon_type === 'fontawesome' || $icon_type === 'phosphor' || $icon_type === 'remix') {
					echo '<i class="' . esc_attr($icon_value) . '" style="margin-right: 0.3em;"></i>';
				} else {
					echo '<span style="margin-right: 0.3em;">' . esc_html($icon_value) . '</span>';
				}
			}
			echo esc_html($blog_title);
			?>
			</h1>
			<?php
			// Display blog page intro text if the user has added content to the blog page
			if ($blog_page_id) {
				$blog_page = get_post($blog_page_id);
				if ($blog_page && !empty($blog_page->post_content)) {
					echo '<div class="timeline-intro">';
					echo apply_filters('the_content', $blog_page->post_content);
					echo '</div>';
				}
			}
			?>
		</div>

		<?php
		// Get pagination info for lazy loading
		global $wp_query;
		$max_pages = $wp_query->max_num_pages;
		$current_page = get_query_var('paged') ? get_query_var('paged') : 1;
		?>

		<div class="timeline-container" 
		     data-page="<?php echo esc_attr($current_page); ?>" 
		     data-max-pages="<?php echo esc_attr($max_pages); ?>">
			
			<?php
			if ( have_posts() ) : while ( have_posts() ) : the_post();

				get_template_part( 'content', get_post_format() ); // Looks for content.php file to get content

			endwhile; endif;
			?>

		</div>

		<?php if ($max_pages > 1) : ?>
		<!-- Lazy load sentinel - triggers loading more posts when visible -->
		<div class="timeline-loader" id="timeline-loader">
			<div class="timeline-loader-spinner"></div>
			<span class="timeline-loader-text">Loading more posts...</span>
		</div>
		<?php endif; ?>


</section>

<?php
	get_footer(); // Gets footer.php file
?>
