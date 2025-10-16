<?php

	get_header(); // Gets header.php file

	// Get the front page content
	if ( have_posts() ) : while ( have_posts() ) : the_post();

		$front_page_content = apply_filters( 'the_content', get_the_content() );
		$has_featured_image = has_post_thumbnail();

		?>

		<section class="hero-section">
			<div class="container">
				<div class="row">
					<div class="hero-content <?php echo $has_featured_image ? 'seven columns' : 'twelve columns'; ?>">
						<?php echo $front_page_content; ?>
					</div>
					<?php if ( $has_featured_image ) : ?>
						<div class="hero-image five columns">
							<?php echo get_the_post_thumbnail( get_the_ID(), 'large', array( 'class' => 'hero-featured-image' ) ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

	<?php endwhile; endif;

	get_footer(); // Gets footer.php file

?>
