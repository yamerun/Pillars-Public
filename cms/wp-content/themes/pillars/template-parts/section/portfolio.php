<?php
function pillars_portfolio_slider_get_wrapper()
{
	query_posts(array(
		'post_type'     => 'portfolio',
		'post_status'   => 'publish',
		'orderby'       => 'date',
		'order'         => 'DESC',
		'posts_per_page' => 8,
	));

	if (have_posts()) {
		while (have_posts()) {
			the_post(); ?>
			<div class="portfolio-slider__slide swiper-slide">
				<div class="portfolio-item">
					<div class="portfolio-item__thumb">
						<div class="media-ratio">
							<?= get_the_post_thumbnail(get_the_ID(), 'full') ?>
						</div>
					</div>
					<a class="portfolio-item__link" href="<?= get_permalink() ?>">
						<h4 class="portfolio-item__title"><?php the_title(); ?></h4>
						<div class="portfolio-item__city"><?= get_post_meta(get_the_ID(), '_portfolio_city', true) ?></div>
					</a>
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
					<h2>Портфолио</h2>
					<div class="portfolio-slider hide-sm-buttons">
						<div class="portfolio-slider__container swiper-container --on-slide --left">
							<div class="portfolio-slider__wrapper swiper-wrapper">
								<?php pillars_portfolio_slider_get_wrapper(); ?>
							</div>

							<div class="pillars-slider__navigations">
								<div class="pillars-slider__pagination"></div>
							</div>
							<div class="pillars-slider__buttons-wrapper">
								<div class="pillars-slider__buttons meniscus --bottom-left">
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
</section>