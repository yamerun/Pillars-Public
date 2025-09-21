<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h1><?= get_the_title(get_option('page_for_posts')) ?></h1>
				</div>
			</div>
			<?php if (have_posts()) {
				// Start the loop.
				while (have_posts()) {
					the_post();
					get_template_part('template-parts/content/content');
				} // End the loop.
			?>
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
			} ?>
		</div>
	</div>
</section>