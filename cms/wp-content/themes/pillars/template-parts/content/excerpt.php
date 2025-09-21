<?php

/**
 * Template part for displaying post archives and search results
 *
 *
 * @package WordPress
 * @since 1.0.0
 */

?>
<div id="post-<?= get_the_ID() ?>" class="row news-item">
	<div class="col-md-3 col-sm-4 col-xs-5">
		<div class="block">
			<a class="cover" href="<?= get_permalink() ?>"><img src="<?= get_the_post_thumbnail_url(get_the_ID(), 'medium') ?>" /></a>
		</div>
	</div>
	<div class="col-md-9 col-sm-8 col-xs-7">
		<div class="block">
			<a href="<?= get_permalink() ?>">
				<h4><?php the_title(); ?></h4>
			</a>
			<time class="date"><?php the_time("d.m.Y, H:i"); ?></time>
			<?php the_excerpt(); ?>
			<a class="permalink" href="<?= get_permalink() ?>">Читать дальше</a>
		</div>
	</div>
</div>