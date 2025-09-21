<?php

/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<section class="pillars-wc-product-section">
	<div class="container">
		<div id="product-<?php the_ID(); ?>" <?php wc_product_class('row', $product); ?>>
			<div class="col-product-gallery">
				<div class="block">
					<?php
					/**
					 * Hook: woocommerce_before_single_product_summary.
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10 --> remove
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action('woocommerce_before_single_product_summary'); ?>
				</div>
			</div>
			<div class="col-product-title">
				<div class="block m-unbottom">
					<?php
					/**
					 * Hook: pillars_wc_single_product_title.
					 *
					 * @hooked woocommerce_template_single_title - 5
					 */
					do_action('pillars_wc_single_product_title'); ?>
				</div>
			</div>
			<div class="col-product-additional">
				<div class="block">
					<?php
					/**
					 * Hook: pillars_wc_single_product_additional.
					 *
					 * @hooked pillars_wc_single_product_advantages - 10
					 * @hooked pillars_wc_single_product_link_to_tab - 15
					 * @hooked pillars_wc_single_product_link_to_video - 20
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10 --> remove
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action('pillars_wc_single_product_additional'); ?>
				</div>
			</div>
			<div class="col-product-summary">
				<div class="block">
					<?php
					/**
					 * Hook: woocommerce_single_product_summary.
					 *
					 * @hooked woocommerce_template_single_title - 5 --> remove on `pillars_wc_single_product_additional`
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10 --> remove
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30 <-- add price simple
					 * @hooked woocommerce_template_single_meta - 40 --> replace on `woocommerce_template_single_title`
					 * @hooked woocommerce_template_single_sharing - 50 --> remove, add function on button-file
					 * @hooked WC_Structured_Data::generate_product_data() - 60
					 */
					do_action('woocommerce_single_product_summary');
					?>
				</div>
			</div>
		</div><!-- #product-id.row -->
	</div>
</section>
<?php
/**
 * Hook: woocommerce_after_single_product_summary.
 *
 * @hooked woocommerce_output_product_data_tabs - 10
 * @hooked woocommerce_upsell_display - 15 --> remove
 * @hooked woocommerce_output_related_products - 20
 */
do_action('woocommerce_after_single_product_summary');
?>

<?php
/**
 * Hook: woocommerce_after_single_product.
 *
 * @hooked pillars_wc_single_product_news_slider - 5
 */
do_action('woocommerce_after_single_product');
?>