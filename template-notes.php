<?php
/*Template Name: Notes*/

get_header();

the_content();

query_posts(array(
   'post_type' => 'notes'
));


	if ( have_posts() ) : while ( have_posts() ) : the_post();

?>


<div class="row">

	<div class="two columns">
	
		<?php if ( has_post_thumbnail()) : ?>
			
				<a href="<?php the_permalink(); ?>" alt="<?php the_title_attribute(); ?>">
				
				<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'book-cover' ) ); ?>
				
				</a>
			<?php else: ?> <div style="width: 130px;"><p></p></div><?php ; ?>
			
		<?php endif; ?>
	
	</div>
	
	<div class="ten columns">

		<?php 
		
		$post_terms = get_the_terms( get_the_ID(), 'category' ); 

		if ($post_terms):
		
		?>
		
		<h5 class="blog-title" style="margin-bottom: 0px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
		<div class="notes-category"><?php echo $post_terms[0]->name; ?></div>
		
		<?php
		
		else:
		
		?>
		
				<h5 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>

		
		<?php
		
		endif;

		if ( has_excerpt()) : ?>
		
			<p><?php the_excerpt(); ?></p>	
			
		<?php endif; ?>
		
		<div class="notes-read-more"><strong><a href="<?php the_permalink(); ?>" alt="<?php the_title_attribute(); ?>">Read more</strong></a></div>
	
	</div>
	
</div>

<br />
<br />

<?php

	endwhile; endif;

	get_footer(); // Gets footer.php file

?>
