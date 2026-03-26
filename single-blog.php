<?php
/**
 * Single blog post template
 */

$blog_page_id = get_option('page_for_posts');
$blog_url     = $blog_page_id ? get_permalink($blog_page_id) : home_url('/');
$blog_label   = $blog_page_id ? strtoupper(get_the_title($blog_page_id)) : 'THOUGHTS';
?>

<div class="container">

	<nav class="single-post-back" aria-label="Back to blog">
		<a href="<?php echo esc_url($blog_url); ?>">&larr; BACK TO <?php echo esc_html($blog_label); ?></a>
	</nav>

	<article class="single-post">

		<header class="single-post-header">
			<p class="single-post-meta">
				<?php echo esc_html(get_the_date('jS F Y')); ?>
				&bull;
				<?php echo esc_html(get_reading_time()); ?>
			</p>

			<h1><?php the_title(); ?></h1>

			<?php
			$tags = get_the_tags();
			if ($tags) : ?>
				<div class="single-post-tags">
					<?php foreach ($tags as $tag) : ?>
						<a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="single-post-tag">
							<?php echo esc_html(strtoupper($tag->name)); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</header>

		<?php if (has_post_thumbnail()) : ?>
			<div class="single-post-featured-image">
				<?php the_post_thumbnail('full'); ?>
			</div>
		<?php endif; ?>

		<div class="single-post-content">
			<?php the_content(); ?>
		</div>

	</article>

</div>
