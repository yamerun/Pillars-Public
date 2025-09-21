<?php

/**
 * Template Name: Новинки
 */

get_header(); ?>

<section class="page" id="post-<?= get_the_ID() ?>">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<?php
		wc_get_template('loop/loop-filter-start.php', ['slug' => 'new-products']);
		wc_get_template('loop/loop-start.php');
		the_content();
		wc_get_template('loop/loop-end.php');
		wc_get_template('loop/loop-filter-end.php');
		?>
	</div>
</section>

<?php get_footer(); ?>