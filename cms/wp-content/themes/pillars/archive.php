<?php

/**
 * The template for displaying archive pages
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

			<?php if (have_posts()) { ?>

				<div class="col-12">
					<div class="block">
						<?php the_archive_title('<h1>', '</h1>'); ?>
					</div>
				</div><!-- .page-header -->

				<?php
				// Start the Loop.
				while (have_posts()) :
					the_post();

					// $post_type = theplugin_get_post_type(get_post_type());

					if (get_template_part('template-parts/content/' . get_post_type(), 'excerpt') === false) {
						get_template_part('template-parts/content/content');
					}

				// End the loop.
				endwhile; ?>
				<div class="col-12">
					<div class="block">
						<?php
						// Previous/next page navigation.
						echo do_shortcode('[tp-pagination]');
						?>
					</div>
				</div>
			<?php
			} else {
				// If no content, include the "No posts found" template.
				get_template_part('template-parts/content/none');
			}
			?>
		</div>
	</div><!-- .container -->
</section><!-- #primary -->

<?php
get_footer();
