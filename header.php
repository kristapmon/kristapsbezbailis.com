<html>

  <head>

    <?php

      wp_head(); // Prints scripts or data in the head tag on the front end

    ?>


<link rel="apple-touch-icon" sizes="57x57" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="wp-content/themes/kristapsbezbailis.com/images/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="wp-content/themes/kristapsbezbailis.com/images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="wp-content/themes/kristapsbezbailis.com/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="wp-content/themes/kristapsbezbailis.com/images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="wp-content/themes/kristapsbezbailis.com/images/favicon-16x16.png">
<link rel="manifest" href="wp-content/themes/kristapsbezbailis.com/images/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="wp-content/themes/kristapsbezbailis.com/images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<meta name="viewport" content="width=device-width, initial-scale=0.7">

<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Raleway&display=swap" rel="stylesheet">
  
  <script>
	function myFunction() {
	  var x = document.getElementById("myTopnav");
	  if (x.className === "topnav") {
		x.className += " responsive";
	  } else {
		x.className = "topnav";
	  }
	}
	</script>
  
  </head>

  <body>
  
<div class="container">

   <div class="row navigation-cover">

	<div class="five columns site-title">
        <a href="<?php echo get_bloginfo( 'wpurl' );?>"><?php echo get_bloginfo( 'name' ); ?></a>			
	</div>
	
	<div class="mobile-menu">
		<a href="javascript:void(0);" class="icon" onclick="myFunction()">
		<b class="fa fa-bars"></b>
		</a>
	</div>

   

      <div class="seven columns">

        <div class="navigation">
		<div class="topnav" id="myTopnav">
          <?php 

			wp_list_pages( '&title_li=' ); // Gets the list of Pages and displays in the navigation

          ?>
			
		</div>
        </div>

      </div>

    </div>
	


    