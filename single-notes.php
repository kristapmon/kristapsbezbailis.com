<?php

	// This page loops in single blog post and displays single-blog.php content

	get_header(); // Gets header.php

	if ( have_posts() ) : while ( have_posts() ) : the_post();
		?>

<?php if ( has_post_thumbnail()) : ?>
	
	<div class="header-detail">	
    
        <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'book-cover-detail' ) ); ?>
    
	</div>
	
<?php endif; ?>

		<?php if ( has_excerpt()) : ?>
		
			<h5 class="header-detail" style="padding: 0;"><?php the_excerpt(); ?></h5>	
			
		<?php endif; ?>
		
		<div class="header-detail">
		
		<?php 
		
		$post_terms = get_the_terms( get_the_ID(), 'category' ); 

		if ($post_terms):
		
		?>
		
		<h1 style="margin-bottom: 0px;"><?php the_title();?></h1>
		<div class="notes-category-detail"><?php echo $post_terms[0]->name; ?></div>
		
		<?php
		
		else:
		
		?>
		
			<h1 style="margin-bottom: 0px;"><?php the_title();?></h1>

		<?php endif; ?>



</div>

<?php

  the_content();

	endwhile; endif;

	get_footer(); // Gets footer.php

?>




