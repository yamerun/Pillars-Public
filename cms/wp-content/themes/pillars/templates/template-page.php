<?php

/**
 * Template Name: Страница с заголовком
 */

get_header(); ?>

<section class="section-title-image firstscreen">
	<div class="container section-title-image__container">
		<div class="row section-title-image__cover">
			<div class="col-12">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= get_the_post_thumbnail(null, '1536x1536') ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row section-title-image__content --bottom">
			<div class="col-sm-6">
				<div class="block color-white">
					<h1><?php the_title(); ?></h1>
					<?php $excerpt = trim(apply_filters('the_excerpt', get_the_excerpt()));
					if ($excerpt != '<p>&nbsp;</p>') {
						echo $excerpt;
					} ?>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="block"></div>
			</div>
		</div>

	</div>
</section>

<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block wp-block">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_template_part('template-parts/section/partners', 'advantage');
get_template_part('template-parts/section/partners', 'product-cat');
get_template_part('template-parts/section/trust-us', null, ['section' => true]);
?>

<?php get_footer(); ?>