<?php

add_filter('woocommerce_blocks_product_grid_item_html', 'pillars_wc_blocks_product_grid_item_html', 10, 3);
add_filter('woocommerce_gallery_thumbnail_size', 'pillars_woocommerce_gallery_thumbnail_size_filter');

/**
 * Function for `woocommerce_gallery_thumbnail_size` filter-hook.
 *
 * @source https://wp-kama.ru/plugin/woocommerce/hook/woocommerce_gallery_thumbnail_size
 * @param  $array
 *
 * @return
 */
function pillars_woocommerce_gallery_thumbnail_size_filter($array)
{
	return array(
		150,
		150,
	);
}

function pillars_wc_blocks_product_grid_item_html($html, $data, $product)
{
	$price_html = pillars_wc_get_product_price_html($product->get_id());

	ob_start(); ?>
	<li class="wc-block-grid__product">
		<a href="<?= $data->permalink ?>" class="wc-block-grid__product-link">
			<?= $data->image ?>
			<div class="wc-block-grid__product-info">
				<?= $data->title ?>
				<span class="price"><?= $price_html ?></span>
			</div>
		</a>
	</li>
<?php
	return ob_get_clean();
}

?>