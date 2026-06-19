<?php
$content	= get_the_content();
$gallery	= pillars_portfolio_get_gallery_ids($content);
$iframe		= pillars_portfolio_get_iframe($content);
$products	= pillars_portfolio_get_product_ids($content);
?>
<section id="post-<?= get_the_ID() ?>">
	<div class="container">
		<div class="row portfolio">
			<div class="col-portfolio-title">
				<div class="block m-unbottom">
					<h1 class="portfolio-title"><?php the_title(); ?></h1>
				</div>
			</div>
			<div class="col-portfolio-gallery">
				<div class="portfolio-wrapper">
					<?= $iframe ?>
					<figure class="portfolio-gallery">
						<?php foreach ($gallery as $image) { ?>
							<figure class="portfolio-gallery-item">
								<a href="<?= wp_get_attachment_image_url($image, 'full') ?>" data-fancybox="portfolio">
									<?= wp_get_attachment_image($image, 'medium_large', false, ['class' => 'wp-iimage-' . $image]) ?>
								</a>
							</figure>
						<?php } ?>
					</figure>
				</div>
			</div>
			<div class="col-portfolio-summary post-article">
				<div class="block wp-block">
					<time datetime="<?php the_time("Y-m-d H:i:s"); ?>" class="date"><?php the_time("F Y"); ?></time>
					<?= apply_filters('the_content', $content) ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_template_part('template-parts/content/portfolio', 'products', ['products' => $products]); ?>

<section class="post-article p-untop">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<?= do_shortcode('[tp-get-part part="call-me-action"]') ?>
				</div>
			</div>
			<div class="col-12">
				<div class="block">
					<?= theplugin_yandex_reviews_widget(theplugin_get_theme_mod('yandex_map_company')) ?>
				</div>
			</div>
		</div>
	</div>
</section>