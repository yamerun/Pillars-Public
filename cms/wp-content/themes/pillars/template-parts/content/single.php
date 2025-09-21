<section class="page article" id="post-<?= get_the_ID() ?>">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
			<div class="col-sm-9">
				<div class="block wp-block">
					<?= get_the_post_thumbnail(get_the_ID(), 'large') ?>
					<time datetime="<?php the_time("Y-m-d H:i:s"); ?>" class="date"><?php the_time("d-m-Y H:i"); ?></time>
					<?php the_content(); ?>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="block">
					<ul id="sidebar">
						<?php dynamic_sidebar(); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>