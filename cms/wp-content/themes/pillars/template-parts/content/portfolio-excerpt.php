<div class="col-12">
	<div class="block">
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
			<div class="portfolio-item__link-wrapper meniscus --bottom-right">
				<a href="<?= get_permalink() ?>" class="portfolio-item__link-text">Читать подробнее</a>
			</div>
		</div>
	</div>
</div>