<!-- Content for the Static pages - loaded by single.php -->

<?php
  // Show page title with icon if icon is set
  $icon_type = get_post_meta(get_the_ID(), '_page_icon_type', true);
  $icon_value = get_post_meta(get_the_ID(), '_page_icon_value', true);
  $show_in_headline = get_post_meta(get_the_ID(), '_page_icon_show_in_headline', true);
  
  // Show title if there's an icon to display
  if ($icon_value && $icon_type !== 'none' && $show_in_headline === '1') :
?>
  <h1><?php the_page_title_with_icon(); ?></h1>
<?php endif; ?>

<?php
  the_content();
?>
