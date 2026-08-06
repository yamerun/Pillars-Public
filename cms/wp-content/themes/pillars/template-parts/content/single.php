<section class="post-article" id="post-<?= get_the_ID() ?>">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-9">
				<div class="block">
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
			<div class="col-lg-4 col-md-3"></div>
			<div class="col-lg-8 col-md-9">
				<div class="block wp-block">
					<?= get_the_post_thumbnail(get_the_ID(), 'large') ?>
					<time datetime="<?php the_time("Y-m-d H:i:s"); ?>" class="date"><?php the_time("d F Y H:i"); ?></time>
					<?php if (theplugin_is_mobile()) { ?>
						<nav class="content-navigation">
							<?= theplugin_get_content_navigation_wrapper(apply_filters('the_content', get_the_content())) ?>
						</nav>
						<div class="content-navigation__toggle"></div>
					<?php } ?>
					<?php the_content(); ?>
					<?= do_shortcode('[tp-get-part part="call-me-action"]') ?>
				</div>
			</div>
			<?php if (!theplugin_is_mobile()) { ?>
				<div class="col-lg-4 col-md-3">
					<div class="block">
						<nav class="content-navigation">
							<?= theplugin_get_content_navigation_wrapper(apply_filters('the_content', get_the_content())) ?>
						</nav>
					</div>
				</div>
			<?php } ?>
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