<?php

/**
 * Template Name: Оформление заказа
 */

get_header(); ?>

<section class="page" id="post-<?php the_ID(); ?>">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
		</div>
		<?php the_content(); ?>
	</div>
</section>

<?php get_footer(); ?>