<?php get_header();

if (is_front_page()) :

	get_template_part('templates/main', 'page');

else : ?>

	<?php if (have_posts()) : ?>
		<?php
		// Start the loop.
		while (have_posts()) : the_post();

			get_template_part('template-parts/content/page');

		// End the loop.
		endwhile;

	// If no content, include the "No posts found" template.
	else : ?>
		<div class="row">
			<div class="col-12">
				<?php get_template_part('template-parts/content/none'); ?>
			</div>
		</div>
	<?php endif; ?>

<?php endif;
get_footer(); ?>