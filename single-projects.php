<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Single Project Template
 */

get_header();

if (have_posts()) : while (have_posts()) : the_post();
	$project_url = get_post_meta(get_the_ID(), '_project_url', true);
	$show_featured_image = get_post_meta(get_the_ID(), '_project_show_featured_image', true);
	$show_excerpt = get_post_meta(get_the_ID(), '_project_show_excerpt', true);
	// Default to showing image and excerpt if not set
	$show_featured_image = ($show_featured_image === '' || $show_featured_image === '1');
	$show_excerpt = ($show_excerpt === '' || $show_excerpt === '1');
?>

<article class="single-project">
	<div class="container">
		
		<?php if (has_post_thumbnail() && $show_featured_image) : ?>
			<div class="single-project-hero">
				<?php the_post_thumbnail('full', array('class' => 'single-project-image')); ?>
			</div>
		<?php endif; ?>

		<div class="single-project-header">
			<h1 class="single-project-title"><?php the_title(); ?></h1>
			
			<?php if (has_excerpt() && $show_excerpt) : ?>
				<p class="single-project-excerpt"><?php echo get_the_excerpt(); ?></p>
			<?php endif; ?>
			
			<?php if ($project_url) : ?>
				<a href="<?php echo esc_url($project_url); ?>" class="single-project-link" target="_blank" rel="noopener noreferrer">
					View Live Project &rarr;
				</a>
			<?php endif; ?>
		</div>

		<div class="single-project-content">
			<?php the_content(); ?>
		</div>

		<div class="single-project-nav">
			<a href="javascript:history.back()" class="back-to-projects">&larr; Back to Projects</a>
		</div>

	</div>
</article>

<?php
endwhile; endif;

get_footer();
?>
