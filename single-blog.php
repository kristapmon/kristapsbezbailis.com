<!-- Displays full blog post page content and styling - loaded from single.php -->



<div class="header-detail">
	<div class="blog-date" style="margin-top: 0px;">
		 <?php the_date(); ?>
	</div>
	
	<h1><?php the_title(); ?></h1>
</div>

<?php

  the_content();

?>

<!-- Include comments.php template -->
<?php //comments_template(); ?>
