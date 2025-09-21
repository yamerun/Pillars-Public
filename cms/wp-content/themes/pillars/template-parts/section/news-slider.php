<?php

$show = true;
if (is_multisite()) {
	if (get_current_blog_id() !== 1) {
		$show = false;
	}
}

if ($show) {

	function pillars_news_slider_get_wrapper()
	{
		query_posts(array(
			'post_type'		=> 'post',
			'post_status'	=> 'publish',
			'orderby'		=> 'date',
			'order'			=> 'DESC',
			'posts_per_page' => 8,
		));

		if (have_posts()) {
			while (have_posts()) {
				the_post(); ?>
				<div class="news-slider__slide swiper-slide">
					<div class="news-item">
						<a class="news-item__thumb" href="<?= get_permalink() ?>">
							<div class="media-ratio">
								<?= get_the_post_thumbnail(get_the_ID(), 'medium') ?>
							</div>
							<div class="news-item__date">
								<time class="meniscus --top-right" datetime="<?php the_time('Y-m-d'); ?>"><?= get_the_time('j M Y') ?></time>
							</div>
						</a>
						<h4 class="news-item__title"><?php the_title(); ?></h4>
						<a class="news-item__link" href="<?= get_permalink() ?>">Читать подробнее</a>
					</div>
				</div>
	<?php
			}
		}
		wp_reset_query();
	}
	?>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="block">
						<h2>Популярное в блоге</h2>
						<div class="news-slider hide-sm-buttons">
							<div class="news-slider__container swiper-container preload-items">
								<div class="news-slider__wrapper swiper-wrapper">
									<?php pillars_news_slider_get_wrapper(); ?>
								</div>

								<div class="pillars-slider__navigations">
									<div class="pillars-slider__pagination"></div>
									<div class="pillars-slider__buttons-wrapper">
										<div class="pillars-slider__buttons">
											<div class="pillars-slider__button-prev"></div>
											<div class="pillars-slider__button-next"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php }
