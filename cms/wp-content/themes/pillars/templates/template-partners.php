<?php

/**
 * Template Name: Сотрудничество
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

		<div class="row section-title-image__content --top">
			<div class="col-sm-6">
				<div class="block color-white">
					<h1><?php the_title(); ?></h1>
					<?php the_excerpt(); ?>
					<a href="#form-partner" class="btn-2">Оставить заявку на сотрудничество</a>
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

<?= do_shortcode('[tp-get-part part="advantage" args="section:true,"]') ?>
<?= do_shortcode('[tp-get-part part="trust-us" args="section:true,"]') ?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<div class="block">
					<?php
					$form = get_post_meta(get_the_ID(), '_cooperation_form', true);
					$form = ($form) ? $form : 'partner';
					echo do_shortcode('[pl-form-components part="' . $form . '"]');
					?>
				</div>
			</div>
			<div class="col-md-5">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(7604, 'medium') ?>
						</div>
					</div>
				</div>
			</div>
		</div><!-- .row -->
	</div>
</section>

<?php get_footer(); ?>