<?php

/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined('ABSPATH') || exit;

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters('woocommerce_product_tabs', array());

if (!empty($product_tabs)) : ?>
	<section class="pillars-wc-product-tabs">
		<div class="container pillars-wc-product-tabs__container">
			<div class="row">
				<div class="col-12">
					<div class="block">
						<nav class="pillars-wc-product-tabs__nav">
							<ul class="pillars-wc-product-tabs__wrapper" role="tablist">
								<?php foreach ($product_tabs as $key => $product_tab) {
									echo sprintf(
										'<li class="pillars-wc-product-tabs__item" id="tab-title-%s" role="tab" aria-controls="tab-%s"><a href="#tab-%s">%s</a></li>',
										esc_attr($key),
										esc_attr($key),
										esc_attr($key),
										wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key))
									);
								} ?>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<?php foreach ($product_tabs as $key => $product_tab) : ?>
			<div class="container pillars-wc-product-tab__container --<?= esc_attr($key) ?>" id="tab-<?= esc_attr($key) ?>" role="tabpanel" aria-labelledby="tab-title-<?= esc_attr($key) ?>">
				<div class="row">
					<?php if (isset($product_tab['callback'])) {
						call_user_func($product_tab['callback'], $key, $product_tab);
					} ?>
				</div>
			</div>
		<?php endforeach; ?>

		<?php do_action('woocommerce_product_after_tabs'); ?>

	</section>

<?php endif; ?>