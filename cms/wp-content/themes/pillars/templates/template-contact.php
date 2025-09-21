<?php

/**
 * Template Name: Контакты
 */

$_blog_id = 1;
if (is_multisite()) {
	if (get_current_blog_id() !== 1) {
		$_blog_id = 2;
	}
}

get_header(); ?>

<section class="page" id="post-<?= get_the_ID() ?>">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
		</div>
	</div>
	<?= do_shortcode('[tp-get-part part="contact-blog-' . $_blog_id . '"]') ?>
</section>

<?php get_footer(); ?>