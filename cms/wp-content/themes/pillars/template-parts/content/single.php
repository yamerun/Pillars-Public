<section class="post-article" id="post-<?= get_the_ID() ?>">
	<div class="container">
		<div class="row">
			<div class="col-lg-2 col-md-1"></div>
			<div class="col-lg-8 col-md-10">
				<div class="block">
					<h1><?php the_title(); ?></h1>
				</div>
				<div class="block wp-block">
					<?= get_the_post_thumbnail(get_the_ID(), 'large') ?>
					<time datetime="<?php the_time("Y-m-d H:i:s"); ?>" class="date"><?php the_time("d F Y H:i"); ?></time>
					<?php the_content(); ?>
					<?= do_shortcode('[tp-get-part part="call-me-action"]') ?>
				</div>
			</div>
			<div class="col-lg-2 col-md-1"></div>
			<div class="col-12">
				<div class="block">
					<?= theplugin_yandex_reviews_widget(theplugin_get_theme_mod('yandex_map_company')) ?>
				</div>
				<?= do_shortcode('[posts-related]') ?>
				<div class="block">
					<ul id="sidebar">
						<?php dynamic_sidebar(); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>