<!-- Displays Blog Post Index page content and styling - loaded by index.php -->
<!-- Timeline item structure for blog posts -->

<a href="<?php the_permalink(); ?>" class="timeline-item">
	<div class="timeline-date">
		<?php echo get_the_date('jS F Y'); ?>
	</div>
	<div class="timeline-content">
		<h3 class="timeline-title"><?php the_title(); ?></h3>
		<?php if (has_excerpt()) : ?>
			<p class="timeline-excerpt"><?php echo get_the_excerpt(); ?></p>
		<?php else : ?>
			<p class="timeline-excerpt"><?php echo wp_trim_words(get_the_content(), 25, '...'); ?></p>
		<?php endif; ?>
	</div>
</a>
