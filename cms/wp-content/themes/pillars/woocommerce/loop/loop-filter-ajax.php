<?php

/**
 * Product Loop End for Filter
 *
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

defined('ABSPATH') || exit;

$params = pillars_wc_set_categories_tab_items($args);
if ($params['items']) {
	$has_thumbs = (pillars_wc_product_cat_has_filter_thumbnails() === 'yes') ? true : false;
	$class = ($has_thumbs) ? ' --thumb' : '';
?>
	<div id="category-tabs" class="block">
		<nav class="pillars-wc-product-tabs__nav<?= $class ?>" <?= $params['attrs'] ?>>
			<ul class="pillars-wc-product-tabs__wrapper<?= $class ?>" role="tablist">
				<?php foreach ($params['items'] as $item) {
					if ($has_thumbs) {
						$item['class'][] = $class;
					}
					echo sprintf(
						'<li class="%s" id="tab-%s" data-id="%s" role="tab" aria-controls="tab-%s"><a href="%s">%s%s</a></li>',
						esc_attr(join(' ', $item['class'])),
						esc_attr($item['data-id']),
						esc_attr($item['data-id']),
						esc_attr($item['data-id']),
						esc_attr($item['href']),
						($has_thumbs) ? wp_get_attachment_image(get_term_meta($item['id'], '_pillars_cat_product_image_id', true)) : '',
						wp_kses_post($item['label'])
					);
				} ?>
			</ul>
		</nav>
		<div class="pillars-wc-product-tabs__nav-feeder"></div>
	</div>
<?php }
