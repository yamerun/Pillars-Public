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
if ($params['items']) { ?>
	<div id="category-tabs" class="block">
		<nav class="pillars-wc-product-tabs__nav" <?= $params['attrs'] ?>>
			<ul class="pillars-wc-product-tabs__wrapper" role="tablist">
				<?php foreach ($params['items'] as $item) {
					echo sprintf(
						'<li class="%s" id="tab-%s" data-id="%s" role="tab" aria-controls="tab-%s"><a href="%s">%s</a></li>',
						esc_attr(join(' ', $item['class'])),
						esc_attr($item['data-id']),
						esc_attr($item['data-id']),
						esc_attr($item['data-id']),
						esc_attr($item['href']),
						wp_kses_post($item['label'])
					);
				} ?>
			</ul>
		</nav>
		<div class="pillars-wc-product-tabs__nav-feeder"></div>
	</div>
<?php }
