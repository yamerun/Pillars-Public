<?php

/**
 * Variable product button submit add to cart
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 6.1.0
 */

defined('ABSPATH') || exit;

/**
 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
 *
 * @since 2.4.0
 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
 */
do_action('woocommerce_single_variation');
