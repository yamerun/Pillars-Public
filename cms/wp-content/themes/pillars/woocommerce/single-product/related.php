<?php

/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woo.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

defined('ABSPATH') || exit;

$columns	= esc_attr(wc_get_loop_prop('columns'));
$replace = array(
	'<div class="woocommerce columns-' . $columns . ' ">'	=> '',
	'products-columns-' . $columns			=> 'product-slider__wrapper swiper-wrapper',
	'<div class="product '					=> '<div class="product-slider__slide swiper-slide '
);

if ($related_products) : ?>

	<section class="related-products p-unbottom">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="block">

						<?php
						$heading = apply_filters('woocommerce_product_related_products_heading', __('Related products', 'woocommerce'));

						if ($heading) { ?>
							<h2><?= esc_html($heading); ?></h2>
						<?php } ?>
						<div class="product-slider">
							<div class="product-slider__container swiper-container">
								<?php
								ob_start();
								woocommerce_product_loop_start();
								echo strtr(ob_get_clean(), $replace);
								?>

								<?php foreach ($related_products as $related_product) : ?>

									<?php
									$post_object = get_post($related_product->get_id());

									setup_postdata($GLOBALS['post'] = &$post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
									ob_start();
									wc_get_template_part('content', 'product');
									echo strtr(ob_get_clean(), $replace);
									?>

								<?php endforeach; ?>

								<?php woocommerce_product_loop_end(); ?>
								<div class="pillars-slider__navigations">
									<div class="pillars-slider__pagination"></div>
									<div class="pillars-slider__buttons">
										<div class="pillars-slider__button-prev"></div>
										<div class="pillars-slider__button-next"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
endif;

wp_reset_postdata();
