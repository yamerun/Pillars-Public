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
	<nav class="pillars-tabs row-sm" <?= $params['attrs'] ?>>
		<ul class="pillars-tabs__wrapper">
			<?php foreach ($params['items'] as $item) {
				echo sprintf(
					'
				<li class="%s"><a href="%s" data-id="%s">%s</a></li>',
					$item['class'],
					$item['href'],
					$item['data-id'],
					$item['label']
				);
			} ?>
		</ul>
	</nav>
<?php }
