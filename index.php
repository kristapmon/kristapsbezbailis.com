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
<div class="header-detail">
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
</div>

<?php
	if ( have_posts() ) : while ( have_posts() ) : the_post();

		get_template_part( 'content', get_post_format() ); // Looks for content.php file to get content

	endwhile; endif;

	get_footer(); // Gets footer.php file

?>
