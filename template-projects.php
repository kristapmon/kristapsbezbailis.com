<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Template Name: Projects
 * Description: A page template to display all projects in a card grid layout
 */

get_header(); ?>

<section class="projects-section">
	<div class="container">
		
		<div class="projects-header">
			<h1><?php the_page_title_with_icon(); ?></h1>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<?php if (get_the_content()) : ?>
					<div class="projects-intro">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			<?php endwhile; endif; ?>
		</div>

		<div class="projects-grid">
			<?php
			$projects = new WP_Query(array(
				'post_type' => 'projects',
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order' => 'DESC'
			));

			if ($projects->have_posts()) :
				while ($projects->have_posts()) : $projects->the_post();
					$project_url = get_post_meta(get_the_ID(), '_project_url', true);
					$has_content = trim(get_the_content());
					
					// Determine primary link for image/title (external URL takes priority)
					$primary_link = '';
					$primary_target = '';
					if ($project_url) {
						$primary_link = $project_url;
						$primary_target = ' target="_blank" rel="noopener noreferrer"';
					} elseif ($has_content) {
						$primary_link = get_permalink();
						$primary_target = '';
					}
			?>
				<article class="project-card">
					<?php if (has_post_thumbnail()) : ?>
						<div class="project-card-image">
							<?php if ($primary_link) : ?>
								<a href="<?php echo esc_url($primary_link); ?>"<?php echo $primary_target; ?>>
									<?php the_post_thumbnail('large'); ?>
								</a>
							<?php else : ?>
								<?php the_post_thumbnail('large'); ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					
					<div class="project-card-content">
						<h3 class="project-card-title">
							<?php if ($primary_link) : ?>
								<a href="<?php echo esc_url($primary_link); ?>"<?php echo $primary_target; ?>><?php the_title(); ?></a>
							<?php else : ?>
								<?php the_title(); ?>
							<?php endif; ?>
						</h3>
						
						<?php if (has_excerpt()) : ?>
							<p class="project-card-excerpt"><?php echo get_the_excerpt(); ?></p>
						<?php endif; ?>
						
						<div class="project-card-links">
							<?php if ($has_content) : ?>
								<a href="<?php echo esc_url(get_permalink()); ?>" class="project-card-link">
									More Info &rarr;
								</a>
							<?php endif; ?>
							
							<?php if ($project_url) : ?>
								<a href="<?php echo esc_url($project_url); ?>" class="project-card-link" target="_blank" rel="noopener noreferrer">
									Visit Project &rarr;
								</a>
							<?php endif; ?>
						</div>
					</div>
				</article>
			<?php
				endwhile;
				wp_reset_postdata();
			else :
			?>
				<p class="no-projects">No projects found. Add some projects in the WordPress admin.</p>
			<?php endif; ?>
		</div>

	</div>
</section>

<?php get_footer(); ?>
