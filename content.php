<!-- Displays Blog Post Index page content and styling - loaded by index.php -->

<div class="row">
	<div class="two columns">
	<div class="blog-date">
	  <?php the_date(); ?>
	</div>
	</div>
	<div class="ten columns">
		<h5 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
	</div>
</div>