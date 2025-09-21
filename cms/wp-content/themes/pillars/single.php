<?php get_header();

if (have_posts()) : ?>
	<?php
	// Start the loop.
	while (have_posts()) : the_post();

		if (get_post_type() == "post") {
			get_template_part('template-parts/content/single');
		} else {
			get_template_part('template-parts/content/' . get_post_type());
		}

	// End the loop.
	endwhile;

// Previous/next page navigation.


// If no content, include the "No posts found" template.
else : ?>
	<div class="row">
		<div class="col-12">
			<?php get_template_part('template-parts/content/none'); ?>
		</div>
	</div>
<?php endif;
?>

<?php get_footer(); ?>