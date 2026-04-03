<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/*Template Name: Notes*/

get_header();
?>

<section class="notes-section">
	<div class="container">
		
		<div class="notes-header">
			<h1><?php the_page_title_with_icon(); ?></h1>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<?php if (get_the_content()) : ?>
					<div class="notes-intro">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			<?php endwhile; endif; ?>
		</div>

<?php
// Reset and query notes
wp_reset_query();
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
	
	wp_reset_query();
?>

	</div>
</section>

<?php
	get_footer(); // Gets footer.php file
?>
