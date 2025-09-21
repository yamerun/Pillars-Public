<div class="col-sm-4 col-xs-6">
	<div class="block">
		<div class="news-item">
			<a class="news-item__thumb" href="<?= get_permalink() ?>">
				<div class="media-ratio">
					<?= get_the_post_thumbnail(get_the_ID(), 'medium_large') ?>
				</div>
				<div class="news-item__date">
					<time class="meniscus --top-right" datetime="<?php the_time('Y-m-d H:i:s'); ?>"><?= get_the_time('j M Y') ?></time>
				</div>
			</a>
			<h4 class="news-item__title"><?php the_title(); ?></h4>
			<?php /* the_excerpt(); */ ?>
			<a class="news-item__link" href="<?= get_permalink() ?>">Читать подробнее</a>
		</div>
	</div>
</div>