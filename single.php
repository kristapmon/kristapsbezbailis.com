<?php

	// This page loops in single blog post and displays single-blog.php content

	get_header(); // Gets header.php

	if ( have_posts() ) : while ( have_posts() ) : the_post();

		get_template_part( 'single-blog', get_post_format() ); // Looks for single-blog.php to get content

	endwhile; endif;

	get_footer(); // Gets footer.php

?>