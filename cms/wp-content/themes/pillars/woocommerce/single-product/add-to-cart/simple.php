<?php

/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

global $product;

if (!$product->is_purchasable()) {
	return;
}

echo wc_get_stock_html($product); // WPCS: XSS ok.

if ($product->is_in_stock()) : ?>

	<?php
	do_action('woocommerce_before_add_to_cart_form');

	?>
	<form class="form-style cart" data-price="<?= esc_attr($product->get_price()) ?>" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
		<?php
		$price = pillars_wc_get_product_price_request($product->get_id());
		if ($price !== false) {
			pillars_wc_single_has_price_request_shortcode($price, $product->get_id(), false);
		} else {
			do_action('woocommerce_before_add_to_cart_button'); ?>

			<table class="variations pillars-wc-product__variations" role="presentation">
				<tbody>
					<tr>
						<th class="label"><label for="">Количество</label></th>
						<td class="quantity">
							<?php
							do_action('woocommerce_before_add_to_cart_quantity');

							woocommerce_quantity_input(
								array(
									'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
									'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
									'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
								)
							);

							do_action('woocommerce_after_add_to_cart_quantity');
							?>
						</td>
						<td class="price">
							<div class="woocommerce-variation single_variation">
								<div class="woocommerce-variation-description"></div>
								<div class="woocommerce-variation-price"><span class="<?= esc_attr(apply_filters('woocommerce_product_price_class', 'price')) ?>"><?= $product->get_price_html() ?></span></div>
								<div class="woocommerce-variation-availability"></div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>

			<button type="submit" name="add-to-cart" value="<?= esc_attr($product->get_id()) ?>" class="single_add_to_cart_button button alt<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>

			<?php do_action('woocommerce_after_add_to_cart_button'); ?>
		<?php } ?>
		<?php wc_get_template('single-product/add-to-cart/prodaction.php'); ?>
	</form>

	<?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php endif; ?>