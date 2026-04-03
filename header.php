<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">

    <?php

      wp_head(); // Prints scripts or data in the head tag on the front end

    ?>


<link rel="apple-touch-icon" sizes="57x57" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/favicon-16x16.png">
<link rel="manifest" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="<?php echo esc_url( get_template_directory_uri() ); ?>/images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<meta name="viewport" content="width=device-width, initial-scale=0.7">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<!-- DataFast.io Tracking Code -->
<script
  defer
  data-website-id="68d541b0d91758792dab699c"
  data-domain="kristapsbezbailis.com"
  src="https://datafa.st/js/script.js">
</script>
<!-- End DataFast.io Tracking Code -->
  
  </head>

  <body>

<!-- Skip link for accessibility -->
<a href="#main-content" class="skip-link screen-reader-text">Skip to main content</a>
  
<div class="container">

   <header class="row navigation-cover" role="banner">

	<div class="five columns site-title">
        <a href="<?php echo get_bloginfo( 'wpurl' );?>" rel="home"><?php echo get_bloginfo( 'name' ); ?></a>			
	</div>
	
	<div class="mobile-menu">
		<button type="button" class="icon" onclick="toggleMobileMenu()" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="mobile-menu-overlay">
		<i class="fa-solid fa-bars" aria-hidden="true"></i>
		</button>
	</div>

   

      <div class="seven columns">

        <nav class="navigation" role="navigation" aria-label="Primary navigation">
		<div class="topnav" id="myTopnav">
          <?php 
            if ( has_nav_menu( 'header-menu' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'header-menu',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'walker'         => new Icon_Nav_Walker(),
                    'fallback_cb'    => false
                ) );
            } else {
                wp_list_pages( '&title_li=' );
            }
          ?>
		</div>
        </nav>

      </div>

    </header>

<div class="mobile-menu-overlay" id="mobile-menu-overlay" role="dialog" aria-label="Mobile navigation" aria-hidden="true">
  <nav aria-label="Mobile navigation">
    <?php 
      if ( has_nav_menu( 'header-menu' ) ) {
          wp_nav_menu( array(
              'theme_location' => 'header-menu',
              'container'      => false,
              'menu_class'     => 'mobile-menu-list',
              'walker'         => new Icon_Nav_Walker(),
              'fallback_cb'    => false
          ) );
      } else {
          echo '<ul class="mobile-menu-list">';
          wp_list_pages( '&title_li=' );
          echo '</ul>';
      }
    ?>
  </nav>
</div>

<main id="main-content" role="main">

    