<?php

	// Starts looping template for the Static pages

	get_header(); // Gets header.php file

	if ( have_posts() ) : while ( have_posts() ) : the_post();

		get_template_part( 'page-single', get_post_format() ); // Looks for page-single.php file to get content

	endwhile; endif;

	get_footer(); // Gets footer.php file

?>
