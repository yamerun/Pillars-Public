<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block m-untop">
					<h2>Продукция</h2>
				</div>
			</div><!-- .col-12 -->
		</div>
		<div class="row">
			<!-- blog_<?= get_current_blog_id() ?>-->
			<?php

			$categories_grid = [
				'swing' => [
					'category_id'	=> 253,
					'image_id'		=> 0,
					'image_size'	=> 'medium_large',
					'class'			=> 'col-12 col-md-6 ratio-2-1',
				],
				'bench' => [
					'category_id'	=> 267,
					'image_id'		=> 2846,
					'image_size'	=> 'medium_large',
					'class'			=> 'col-12 col-md-6 ratio-2-1',
				],
				'urns' => [
					'category_id'	=> 1148,
					'image_id'		=> 0,
					'image_size'	=> 'medium',
					'class'			=> 'col-6 col-md-3 ratio-1-1',
				],
				'bike' => [
					'category_id'	=> 1217,
					'image_id'		=> 0,
					'image_size'	=> 'medium',
					'class'			=> 'col-6 col-md-3 ratio-1-1',
				],
				'pergolas' => [
					'category_id'	=> 446,
					'image_id'		=> 0,
					'image_size'	=> 'medium_large',
					'class'			=> 'col-12 col-md-6 ratio-2-1',
				],
				'parklet' => [
					'category_id'	=> 508,
					'image_id'		=> 0,
					'image_size'	=> 'medium_large',
					'class'			=> 'col-12 col-md-6 ratio-2-1',
				],
				'amphitheater' => [
					'category_id'	=> 1212,
					'image_id'		=> 0,
					'image_size'	=> 'medium',
					'class'			=> 'col-6 col-md-3 ratio-1-1',
				],
			];

			$blog_id = get_current_blog_id();

			foreach ($categories_grid as $slug => $category_item) {
				if (is_multisite()) {
					$category_item['category_id'] = theplugin_multisite_term_get_sibling_id($category_item['category_id'], $blog_id, 1);
				}

				$term = get_term($category_item['category_id'], 'product_cat');
				if ($term) {
					$image = (empty($category_item['image_id'])) ? get_term_meta($term->term_id, 'thumbnail_id', true) : $category_item['image_id'];
					$image = wp_get_attachment_image($image, $category_item['image_size']);

					echo sprintf(
						'
					<div class="category-grid--%s %s">
						<div class="block">
							<a href="%s" class="category-item">
								<div class="media-ratio">%s</div>
								<div class="category-item__cap"></div>
								<div class="category-item__title">%s</div>
							</a>
						</div>
					</div>',
						$slug,
						$category_item['class'],
						get_term_link($term->term_id, 'product_cat'),
						$image,
						(get_term_meta($term->term_id, '_pillars_short_title', true)) ? get_term_meta($term->term_id, '_pillars_short_title', true) : $term->name
					);
				}
			}
			?>
			<div class="col-6 col-md-3">
				<div class="block">
					<a class="category-item__placeholder" href="<?= get_permalink(wc_get_page_id('shop')) ?>">
						<p>Смотреть все категории</p>
					</a>
				</div>
			</div>
		</div><!-- .category-grid -->
	</div>
</section>