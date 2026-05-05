<?php

/**
 * The template for displaying archive pages / post type portfolio
 *
 *
 * @package WordPress
 * @since 1.0.0
 */

get_header();
?>

<section>
	<div class="container">
		<div class="row">

			<?php if (have_posts()) : ?>

				<div class="col-12">
					<div class="block d-flex f-wrap f-center f-between">
						<?php the_archive_title('<h1>', '</h1>'); ?>
						<a class="btn-1 alt m-untop" href="https://vkvideo.ru/@pillars_ru" target="_blank">Видео-обзоры</a>
					</div>
				</div><!-- .page-header -->

			<?php
				// Start the Loop.
				while (have_posts()) :
					the_post();

					get_template_part('template-parts/content/portfolio', 'excerpt');

				// End the loop.
				endwhile;

				// Previous/next page navigation.
				echo do_shortcode('[tp-pagination]');

			// If no content, include the "No posts found" template.
			else :
				get_template_part('template-parts/content/none');

			endif;
			?>
		</div>
	</div><!-- .container -->
</section><!-- #primary -->

<?php
get_footer();
