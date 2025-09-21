<?php get_header();

if (is_front_page()) :

	get_template_part('templates/main', 'page');

elseif (is_home()) :

	get_template_part('templates/post', 'page');

else : ?>
	<section id="post-<?= get_the_ID() ?>">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="block">
						<h1><?php single_post_title(); ?></h1>
					</div>
				</div>

				<?php if (have_posts()) : ?>
					<?php
					// Start the loop.
					while (have_posts()) : the_post();

						get_template_part('template-parts/content/' . get_post_format());

					// End the loop.
					endwhile;

				// Previous/next page navigation.


				// If no content, include the "No posts found" template.
				else : ?>
					<div class="col-12">
						<?php get_template_part('template-parts/content/none'); ?>
					</div>
				<?php endif;
				?>

			</div>
		</div>
	</section>

<?php endif; ?>

<?php get_footer(); ?>